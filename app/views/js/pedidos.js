(() => {
    const api = '../controllers/pedido.php';
    const filtroEstado = document.getElementById('filtro-estado-pedido');
    const buscador = document.getElementById('buscar-pedido');
    const dialogoRegistro = document.getElementById('dialogo-registro-pedido');
    const formularioRegistro = document.getElementById('formulario-registro-pedido');
    const selectorProducto = document.getElementById('pedido-producto');
    const selectorProveedor = document.getElementById('pedido-proveedor');
    const campoPrecio = document.getElementById('pedido-precio');
    const campoCantidad = document.getElementById('pedido-cantidad');
    const botonRegistrar = formularioRegistro.querySelector('.lote-dialog-acciones button:last-child');
    const botonesDetalle = document.querySelectorAll('#modal-detalle-pedido .pedido-detalle-acciones button');
    const token = dialogoRegistro.dataset.csrf;
    let pedidoActivo = null;
    let productosPendientes = [];

    const establecerVisibilidad = (elemento, visible) => {
        elemento.hidden = !visible;
        elemento.style.display = visible ? '' : 'none';
    };

    const mostrarMensaje = (contenedor, mensaje, error = false) => {
        let elemento = contenedor.querySelector('[data-pedido-mensaje]');
        if (!elemento) {
            elemento = document.createElement('div');
            elemento.dataset.pedidoMensaje = '';
            elemento.className = error ? 'msg-error' : 'msg-success';
            elemento.setAttribute('role', error ? 'alert' : 'status');
            contenedor.prepend(elemento);
        }
        elemento.className = error ? 'msg-error' : 'msg-success';
        elemento.replaceChildren();
        elemento.setAttribute('role', error ? 'alert' : 'status');

        if (error && Array.isArray(mensaje)) {
            const titulo = document.createElement('h4');
            titulo.textContent = 'Por favor corrige los siguientes errores:';
            const lista = document.createElement('ul');
            mensaje.forEach((texto) => {
                const item = document.createElement('li');
                item.textContent = texto;
                lista.append(item);
            });
            elemento.append(titulo, lista);
            return;
        }

        const texto = document.createElement('p');
        texto.textContent = mensaje;
        elemento.append(texto);
    };

    const solicitar = async (url, opciones) => {
        const respuesta = await fetch(url, opciones);
        let datos;
        try {
            datos = await respuesta.json();
        } catch {
            throw new Error('El servidor devolvió una respuesta no válida.');
        }
        if (!respuesta.ok || !datos.ok) {
            const error = new Error(datos.mensaje || 'No se pudo completar la operación.');
            error.errores = datos.errores;
            throw error;
        }
        return datos;
    };

    const formatoDinero = (valor) => '$' + Number(valor || 0).toLocaleString('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });

    const estadoDeFiltro = (valor) => valor === 'confirmado' ? 'entregado' : valor;

    const aplicarFiltros = () => {
        const consulta = buscador.value.trim().toLocaleLowerCase('es');
        const estadoElegido = estadoDeFiltro(filtroEstado.value);
        const tarjetas = [...document.querySelectorAll('[data-pedido]')];
        let visibles = 0;

        tarjetas.forEach((tarjeta) => {
            const coincideTexto = tarjeta.textContent.toLocaleLowerCase('es').includes(consulta);
            const coincideEstado = !estadoElegido || tarjeta.dataset.estado === estadoElegido;
            tarjeta.hidden = !(coincideTexto && coincideEstado);
            if (!tarjeta.hidden) visibles += 1;
        });

        const sinPedidos = document.getElementById('sin-pedidos');
        sinPedidos.hidden = visibles !== 0;
        const etiquetaPaginacion = document.querySelector('.pedido-paginacion span');
        etiquetaPaginacion.textContent = `${visibles} de ${tarjetas.length} pedidos`;
    };

    const actualizarEstadoTarjeta = (tarjeta, estado) => {
        tarjeta.dataset.estado = estado;
        const insignia = tarjeta.querySelector('.pedido-encabezado .historial-estado');
        const clase = estado === 'entregado' ? 'pagada' : estado === 'cancelado' ? 'cancelada' : 'pendiente';
        const texto = estado === 'entregado' ? 'Entregado' : estado === 'cancelado' ? 'Cancelado' : 'Pendiente';
        insignia.textContent = texto;
        insignia.className = `historial-estado ${clase}`;

        if (estado !== 'pendiente') {
            tarjeta.querySelector('[data-abrir-registro]')?.remove();
            tarjeta.querySelector('.pedido-eliminar')?.remove();
        }
    };

    const renderizarOpcionesProducto = () => {
        selectorProducto.replaceChildren(new Option('Selecciona un producto', ''));
        productosPendientes.forEach((producto) => {
            const descripcion = `${producto.nombre} · pendiente: ${producto.cantidad_pendiente}`;
            selectorProducto.add(new Option(descripcion, producto.id_detalle));
        });
        selectorProducto.disabled = productosPendientes.length === 0;
        if (productosPendientes.length === 0) {
            mostrarMensaje(formularioRegistro, 'Todos los productos de este pedido ya fueron registrados.');
        }
    };

    const cargarPedido = async (idPedido) => {
        const datos = await solicitar(`${api}?accion=detalles&id=${encodeURIComponent(idPedido)}`);
        const pedido = datos.pedido;
        productosPendientes = pedido.productos_pendientes;

        selectorProveedor.replaceChildren(new Option(pedido.proveedor, pedido.id_proveedor));
        selectorProveedor.value = String(pedido.id_proveedor);
        renderizarOpcionesProducto();
        if (productosPendientes.length > 0) {
            formularioRegistro.querySelector('[data-pedido-mensaje]')?.remove();
        }
        return pedido;
    };

    selectorProducto.addEventListener('change', () => {
        const producto = productosPendientes.find((item) => String(item.id_detalle) === selectorProducto.value);
        if (!producto) {
            campoPrecio.value = '';
            campoCantidad.value = '';
            campoCantidad.removeAttribute('max');
            return;
        }
        campoPrecio.value = Number(producto.precio_unitario).toFixed(2);
        campoCantidad.max = producto.cantidad_pendiente;
        campoCantidad.value = producto.cantidad_pendiente;
    });

    document.querySelectorAll('[data-abrir-registro]').forEach((boton) => {
        boton.addEventListener('click', async () => {
            const tarjeta = boton.closest('[data-pedido]');
            pedidoActivo = tarjeta?.dataset.pedidoId || null;
            formularioRegistro.reset();
            campoPrecio.value = '';
            selectorProducto.replaceChildren(new Option('Cargando productos…', ''));
            selectorProveedor.replaceChildren(new Option('Cargando proveedor…', ''));
            formularioRegistro.querySelector('[data-pedido-mensaje]')?.remove();
            botonRegistrar.disabled = true;
            try {
                await cargarPedido(pedidoActivo);
            } catch (error) {
                mostrarMensaje(formularioRegistro, error.errores || error.message, true);
            } finally {
                botonRegistrar.disabled = false;
            }
        });
    });

    botonRegistrar.addEventListener('click', async () => {
        if (!formularioRegistro.reportValidity()) return;
        if (!pedidoActivo || !selectorProducto.value) {
            mostrarMensaje(formularioRegistro, 'Selecciona un producto pendiente del pedido.', true);
            return;
        }

        botonRegistrar.disabled = true;
        const datos = new URLSearchParams({
            accion: 'recibir',
            csrf: token,
            id_pedido: pedidoActivo,
            id_detalle: selectorProducto.value,
            lote: document.getElementById('pedido-id').value.trim(),
            fecha_caducidad: document.getElementById('pedido-caducidad').value,
            ubicacion: document.getElementById('pedido-ubicacion').value.trim(),
            cantidad: campoCantidad.value
        });

        try {
            const resultado = await solicitar(api, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body: datos
            });
            productosPendientes = resultado.productos_pendientes;
            renderizarOpcionesProducto();
            campoPrecio.value = '';
            campoCantidad.value = '';
            campoCantidad.removeAttribute('max');
            document.getElementById('pedido-id').value = '';
            document.getElementById('pedido-caducidad').value = '';
            mostrarMensaje(
                formularioRegistro,
                productosPendientes.length
                    ? 'Producto registrado. Selecciona el siguiente producto pendiente.'
                    : 'Pedido recibido por completo. Se actualizó su estado a entregado.'
            );

            const tarjeta = document.querySelector(`[data-pedido-id="${CSS.escape(String(pedidoActivo))}"]`);
            if (resultado.pedido.estado !== 'pendiente' && tarjeta) {
                actualizarEstadoTarjeta(tarjeta, resultado.pedido.estado);
            }
            if (productosPendientes.length === 0) {
                window.setTimeout(() => dialogoRegistro.close(), 900);
            }
            aplicarFiltros();
        } catch (error) {
            mostrarMensaje(formularioRegistro, error.errores || error.message, true);
        } finally {
            botonRegistrar.disabled = false;
        }
    });

    botonesDetalle[0].addEventListener('click', () => {
        const folio = document.getElementById('modal-pedido-folio').textContent;
        const tarjeta = [...document.querySelectorAll('[data-pedido]')]
            .find((item) => item.querySelector('.pedido-encabezado h2').textContent === folio);
        if (!tarjeta) return;
        pedidoActivo = tarjeta.dataset.pedidoId;
        formularioRegistro.reset();
        campoPrecio.value = '';
        dialogoRegistro.showModal();
        cargarPedido(pedidoActivo).catch((error) => mostrarMensaje(formularioRegistro, error.message, true));
    });

    const actualizarDetalle = async (boton) => {
        const tarjeta = boton.closest('[data-pedido]');
        if (!tarjeta) return;
        const esPendiente = tarjeta.dataset.estado === 'pendiente';
        establecerVisibilidad(botonesDetalle[0], esPendiente);
        establecerVisibilidad(botonesDetalle[1], esPendiente);
        establecerVisibilidad(botonesDetalle[2], true);

        try {
            const datos = await solicitar(`${api}?accion=detalles&id=${encodeURIComponent(tarjeta.dataset.pedidoId)}`);
            const pedido = datos.pedido;
            pedidoActivo = pedido.clave_compra;
            document.getElementById('modal-pedido-folio').textContent = `ORD-${pedido.clave_compra}`;
            document.getElementById('modal-pedido-proveedor').textContent = pedido.proveedor;
            ['solicitud', 'esperada'].forEach((nombre, indice) => {
                const clave = indice === 0 ? 'fecha_solicitud' : 'fecha_esperada';
                const elemento = document.getElementById(`modal-pedido-${nombre}`);
                elemento.textContent = pedido[clave] || 'Sin definir';
                elemento.dateTime = pedido[clave] || '';
            });
            document.getElementById('modal-pedido-total').textContent = formatoDinero(pedido.total_estimado);
            const estadoModal = document.getElementById('modal-pedido-estado');
            estadoModal.textContent = pedido.estado === 'entregado'
                ? 'Entregado'
                : pedido.estado === 'cancelado' ? 'Cancelado' : 'Pendiente';
            estadoModal.className = `historial-estado ${pedido.estado === 'entregado' ? 'pagada' : pedido.estado === 'cancelado' ? 'cancelada' : 'pendiente'}`;

            const cuerpo = document.getElementById('modal-pedido-productos');
            cuerpo.replaceChildren(...pedido.productos.map((producto) => {
                const fila = document.createElement('tr');
                [
                    producto.nombre,
                    producto.cantidad,
                    formatoDinero(producto.precio_unitario),
                    formatoDinero(Number(producto.precio_unitario) * Number(producto.cantidad))
                ].forEach((valor) => {
                    const celda = document.createElement('td');
                    celda.textContent = String(valor);
                    fila.append(celda);
                });
                return fila;
            }));

            const pedidoPendiente = pedido.estado === 'pendiente';
            establecerVisibilidad(botonesDetalle[0], pedidoPendiente);
            establecerVisibilidad(botonesDetalle[1], pedidoPendiente);
            establecerVisibilidad(botonesDetalle[2], true);
            botonesDetalle[2].onclick = () => {
                window.open(`../controllers/pdf_ordencompra.php?id=${encodeURIComponent(pedido.clave_compra)}`, '_blank', 'noopener');
            };
        } catch (error) {
            mostrarMensaje(document.querySelector('#modal-detalle-pedido .pedido-detalle-contenido'), error.message, true);
        }
    };

    document.querySelectorAll('[data-abrir-detalles]').forEach((boton) => {
        boton.addEventListener('click', () => actualizarDetalle(boton));
    });

    botonesDetalle[1].addEventListener('click', async () => {
        if (!pedidoActivo) {
            const folio = document.getElementById('modal-pedido-folio').textContent.replace(/^ORD-/, '');
            const tarjeta = [...document.querySelectorAll('[data-pedido]')]
                .find((item) => item.dataset.pedidoId === folio);
            pedidoActivo = tarjeta?.dataset.pedidoId || null;
        }
        if (!pedidoActivo || !window.confirm('¿Deseas cancelar este pedido?')) return;
        try {
            await solicitar(api, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                body: new URLSearchParams({ accion: 'cancelar', csrf: token, id_pedido: pedidoActivo })
            });
            const tarjeta = document.querySelector(`[data-pedido-id="${CSS.escape(String(pedidoActivo))}"]`);
            if (tarjeta) actualizarEstadoTarjeta(tarjeta, 'cancelado');
            establecerVisibilidad(botonesDetalle[0], false);
            establecerVisibilidad(botonesDetalle[1], false);
            document.getElementById('modal-pedido-estado').textContent = 'Cancelado';
            document.getElementById('modal-pedido-estado').className = 'historial-estado cancelada';
            aplicarFiltros();
            document.getElementById('modal-detalle-pedido').close();
        } catch (error) {
            mostrarMensaje(document.querySelector('#modal-detalle-pedido .pedido-detalle-contenido'), error.message, true);
        }
    });

    document.querySelectorAll('.pedido-eliminar').forEach((boton) => {
        boton.addEventListener('click', async () => {
            const tarjeta = boton.closest('[data-pedido]');
            if (!tarjeta || !window.confirm('¿Deseas cancelar este pedido pendiente?')) return;
            try {
                const resultado = await solicitar(api, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
                    body: new URLSearchParams({
                        accion: 'cancelar',
                        csrf: token,
                        id_pedido: tarjeta.dataset.pedidoId
                    })
                });
                actualizarEstadoTarjeta(tarjeta, 'cancelado');
                aplicarFiltros();
                window.alert(resultado.mensaje);
            } catch (error) {
                window.alert(error.message);
            }
        });
    });

    filtroEstado.addEventListener('change', aplicarFiltros);
    buscador.addEventListener('input', aplicarFiltros);
    aplicarFiltros();
})();
