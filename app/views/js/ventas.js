(function () {
    const URL_VENTAS = '../controllers/ventas.php';
    const IVA = 0.16;

    // Elementos de la pagina
    const inputBuscar = document.getElementById('buscarProducto');
    const tbodyResultados = document.getElementById('resultadosProductos');
    const mensajeBusqueda = document.getElementById('mensajeBusqueda');
    const modalBuscar = document.getElementById('modalBuscarProducto');
    const btnBuscarProducto = document.getElementById('btnBuscarProducto');
    const tbodyCarrito = document.getElementById('cartTableBody');
    const mensajeVenta = document.getElementById('ventaMensaje');
    const inputCodigo = document.getElementById('barcodeInput');
    const inputCliente = document.getElementById('clienteInput');
    const lblItems = document.getElementById('lblItems');
    const lblSubtotal = document.getElementById('lblSubtotal');
    const lblIva = document.getElementById('lblIva');
    const lblTotal = document.getElementById('lblTotal');
    const btnNueva = document.getElementById('btnNuevaVenta');
    const btnCancelar = document.getElementById('btnCancelarVenta');
    const btnCobrar = document.getElementById('btnCobrar');

    // Elementos del buscador de servicios
    const inputBuscarServ = document.getElementById('buscarServicio');
    const tbodyServicios = document.getElementById('resultadosServicios');
    const mensajeBusquedaServ = document.getElementById('mensajeBusquedaServicio');
    const modalBuscarServ = document.getElementById('modalBuscarServicio');
    const btnAgregarServicio = document.getElementById('btnAgregarServicio');

    // Elementos del modal de pago
    const modalPago = document.getElementById('modalPagarVenta');
    const inputMonto = document.getElementById('montoRecibido');
    const btnConfirmar = modalPago.querySelector('.pago-confirmar');
    const tbodyPago = modalPago.querySelector('.pago-tabla tbody');
    const totalesPago = modalPago.querySelectorAll('.pago-totales strong');
    const totalPagar = modalPago.querySelector('.pago-calculo-monto > strong');
    const cambioPago = modalPago.querySelector('.pago-cambio > span');
    const metodos = modalPago.querySelectorAll('.pago-metodo');

    let temporizador = null;
    let temporizadorServ = null;
    let carrito = [];
    let formaPago = 'efectivo';
    // Ultima venta guardada, para poder facturarla
    let ultimaVentaId = null;
    let ultimoFolio = null;
    let enviandoFactura = false;

    // Funciones de apoyo
    function redondear(n) { return Math.round(n * 100) / 100; }
    function dinero(n) { return '$' + n.toFixed(2); }
    function descuentoLinea(i) { return redondear(i.precio * i.cantidad * i.descuento / 100); }
    function subtotalLinea(i) { return redondear(i.precio * i.cantidad - descuentoLinea(i)); }
    function ivaLinea(i) { return redondear(subtotalLinea(i) * IVA); }

    function celda(contenido) {
        const td = document.createElement('td');
        if (contenido instanceof Node) {
            td.appendChild(contenido);
        } else {
            td.textContent = contenido;
        }
        return td;
    }

    function boton(texto, clase, accion) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = clase;
        b.textContent = texto;
        b.addEventListener('click', accion);
        return b;
    }

    // Pone estilos directo al elemento
    function estilos(elemento, css) {
        Object.assign(elemento.style, css);
    }

    // Boton chico para - y +
    function botonMini(texto, accion) {
        const b = boton(texto, 'btn-secundario', accion);
        estilos(b, {
            width: 'auto',
            minWidth: '26px',
            margin: '0',
            padding: '3px 8px',
            fontSize: '13px',
            lineHeight: '1.2'
        });
        return b;
    }

    // Campo de numero chico
    function inputMini(min, max, valor) {
        const i = document.createElement('input');
        i.type = 'number';
        i.className = 'venta-cantidad';
        i.min = min;
        i.max = max;
        i.step = '1';
        i.value = valor;
        estilos(i, { width: '52px', padding: '4px', textAlign: 'center' });
        return i;
    }

    // Fila para poner los controles uno al lado del otro
    function filaControl() {
        const d = document.createElement('div');
        estilos(d, {
            display: 'flex',
            alignItems: 'center',
            flexWrap: 'nowrap',
            gap: '4px'
        });
        return d;
    }

    // Busqueda de productos

    // Da scroll a la lista y deja fijos los titulos
    const cajaResultados = modalBuscar.querySelector('.busqueda-tabla');
    estilos(cajaResultados, { maxHeight: '320px', overflowY: 'auto' });
    modalBuscar.querySelectorAll('.busqueda-tabla th').forEach(function (th) {
        estilos(th, { position: 'sticky', top: '0', zIndex: '1' });
    });

    // Muestra un texto en la tabla de resultados
    function filaMensaje(texto) {
        tbodyResultados.innerHTML = '';
        const tr = document.createElement('tr');
        const td = celda(texto);
        td.colSpan = 5;
        td.className = 'vacio';
        tr.appendChild(td);
        tbodyResultados.appendChild(tr);
    }

    // Muestra los productos encontrados
    function dibujarResultados(productos) {
        if (productos.length === 0) {
            filaMensaje('No se encontraron productos.');
            return;
        }
        tbodyResultados.innerHTML = '';
        productos.forEach(function (p) {
            const tr = document.createElement('tr');
            tr.appendChild(celda(p.codigo));
            tr.appendChild(celda(p.nombre));
            tr.appendChild(celda(dinero(Number(p.precio_venta))));
            tr.appendChild(celda(p.existencia));

            const btn = boton('Agregar', 'btn btn-info', function () {
                agregarAlCarrito(p);
                modalBuscar.close();
            });
            // Si no hay existencia no se puede agregar
            btn.disabled = Number(p.existencia) <= 0;
            tr.appendChild(celda(btn));
            tbodyResultados.appendChild(tr);
        });
    }

    // Pide los productos al servidor
    async function buscar(termino) {
        mensajeBusqueda.textContent = '';
        try {
            const resp = await fetch(URL_VENTAS + '?accion=buscar_productos&q=' + encodeURIComponent(termino));
            const datos = await resp.json();
            // Si ya escribio otra cosa, ignora esta respuesta
            if (termino !== inputBuscar.value.trim()) return;
            dibujarResultados(datos);
        } catch (e) {
            mensajeBusqueda.textContent = 'No se pudo realizar la búsqueda.';
        }
    }

    // Busca cuando deja de escribir
    inputBuscar.addEventListener('input', function () {
        clearTimeout(temporizador);
        const termino = inputBuscar.value.trim();
        temporizador = setTimeout(function () { buscar(termino); }, 300);
    });

    // Al abrir el modal muestra los productos
    btnBuscarProducto.addEventListener('click', function () {
        inputBuscar.value = '';
        buscar('');
    });

    // Busqueda de servicios

    // Muestra un texto en la tabla de servicios
    function filaMensajeServicio(texto) {
        tbodyServicios.innerHTML = '';
        const tr = document.createElement('tr');
        const td = celda(texto);
        td.colSpan = 6;
        td.className = 'vacio';
        tr.appendChild(td);
        tbodyServicios.appendChild(tr);
    }

    // Muestra los servicios encontrados
    function dibujarServicios(servicios) {
        if (!Array.isArray(servicios) || servicios.length === 0) {
            filaMensajeServicio('No se encontraron servicios.');
            return;
        }
        tbodyServicios.innerHTML = '';
        servicios.forEach(function (s) {
            const tr = document.createElement('tr');
            tr.appendChild(celda(s.nombre));
            tr.appendChild(celda(s.categoria || ''));
            tr.appendChild(celda(s.descripcion || ''));
            tr.appendChild(celda(s.duracion + ' min'));
            tr.appendChild(celda(dinero(Number(s.precio))));
            tr.appendChild(celda(boton('Agregar', 'btn btn-info', function () {
                agregarServicio(s);
                modalBuscarServ.close();
            })));
            tbodyServicios.appendChild(tr);
        });
    }

    // Pide los servicios al servidor
    async function buscarServicios(termino) {
        mensajeBusquedaServ.textContent = '';
        try {
            const resp = await fetch(URL_VENTAS + '?accion=buscar_servicios&q=' + encodeURIComponent(termino));
            const datos = await resp.json();
            // Si ya escribio otra cosa, ignora esta respuesta
            if (termino !== inputBuscarServ.value.trim()) return;
            dibujarServicios(datos);
        } catch (e) {
            mensajeBusquedaServ.textContent = 'No se pudo realizar la búsqueda.';
        }
    }

    // Busca cuando deja de escribir
    inputBuscarServ.addEventListener('input', function () {
        clearTimeout(temporizadorServ);
        const termino = inputBuscarServ.value.trim();
        temporizadorServ = setTimeout(function () { buscarServicios(termino); }, 300);
    });

    // Al abrir el modal muestra los servicios
    btnAgregarServicio.addEventListener('click', function () {
        inputBuscarServ.value = '';
        buscarServicios('');
    });

    // Carrito

    // Suma todo lo que hay en el carrito
    function calcularTotales() {
        let items = 0, subtotal = 0, iva = 0;
        carrito.forEach(function (i) {
            items += i.cantidad;
            subtotal += subtotalLinea(i);
            iva += ivaLinea(i);
        });
        subtotal = redondear(subtotal);
        iva = redondear(iva);
        return { items: items, subtotal: subtotal, iva: iva, total: redondear(subtotal + iva) };
    }

    // Muestra los totales en el resumen
    function totales() {
        const t = calcularTotales();
        lblItems.textContent = t.items;
        lblSubtotal.textContent = dinero(t.subtotal);
        lblIva.textContent = dinero(t.iva);
        lblTotal.textContent = dinero(t.total) + ' MXN';
    }

    // El descuento va de 0 a 100
    function ajustarDescuento(item) {
        if (isNaN(item.descuento) || item.descuento < 0) item.descuento = 0;
        if (item.descuento > 100) item.descuento = 100;
    }

    // Dibuja la tabla del carrito
    function dibujarCarrito() {
        tbodyCarrito.innerHTML = '';

        if (carrito.length === 0) {
            const tr = document.createElement('tr');
            const td = celda('No hay productos ni servicios en la venta.');
            td.colSpan = 7;
            td.className = 'venta-carrito-vacio';
            tr.appendChild(td);
            tbodyCarrito.appendChild(tr);
            totales();
            return;
        }

        carrito.forEach(function (item, indice) {
            const tr = document.createElement('tr');
            tr.appendChild(celda(item.nombre));
            tr.appendChild(celda(item.codigo));
            tr.appendChild(celda(dinero(item.precio)));

            // El servicio siempre es 1, sin - ni +
            if (item.tipo === 'servicio') {
                tr.appendChild(celda('1'));
            } else {
                // Cantidad con - y + a los lados
                const cant = filaControl();
                const inputCant = inputMini('1', item.existencia, item.cantidad);
                inputCant.addEventListener('change', function () {
                    let n = parseInt(inputCant.value, 10);
                    if (isNaN(n) || n < 1) n = 1;
                    if (n > item.existencia) {
                        n = item.existencia;
                        mensajeVenta.textContent = 'Solo hay ' + item.existencia + ' en existencia.';
                    }
                    item.cantidad = n;
                    dibujarCarrito();
                });
                cant.appendChild(botonMini('-', function () {
                    if (item.cantidad > 1) {
                        item.cantidad--;
                        dibujarCarrito();
                    }
                }));
                cant.appendChild(inputCant);
                cant.appendChild(botonMini('+', function () {
                    if (item.cantidad < item.existencia) {
                        item.cantidad++;
                        dibujarCarrito();
                    } else {
                        mensajeVenta.textContent = 'No hay más existencia de ' + item.nombre + '.';
                    }
                }));
                const tdCant = celda(cant);
                tdCant.style.whiteSpace = 'nowrap';
                tr.appendChild(tdCant);
            }

            // Descuento en porcentaje
            const caja = document.createElement('div');
            const desc = filaControl();
            const inputDesc = inputMini('0', '100', item.descuento);
            inputDesc.title = 'Porcentaje de descuento';
            inputDesc.addEventListener('change', function () {
                item.descuento = Number(inputDesc.value) || 0;
                ajustarDescuento(item);
                dibujarCarrito();
            });
            desc.appendChild(inputDesc);
            desc.appendChild(document.createTextNode('%'));
            caja.appendChild(desc);

            // Muestra cuanto se descuenta en pesos
            const ahorro = descuentoLinea(item);
            if (ahorro > 0) {
                const nota = document.createElement('small');
                nota.textContent = '-' + dinero(ahorro);
                estilos(nota, {
                    display: 'block',
                    fontSize: '12px',
                    color: 'var(--texto-suave)'
                });
                caja.appendChild(nota);
            }
            tr.appendChild(celda(caja));

            tr.appendChild(celda(dinero(subtotalLinea(item))));

            tr.appendChild(celda(boton('Quitar', 'btn btn-peligro venta-quitar', function () {
                carrito.splice(indice, 1);
                dibujarCarrito();
            })));

            tbodyCarrito.appendChild(tr);
        });
        totales();
    }

    // Agrega un producto al carrito
    function agregarAlCarrito(p) {
        mensajeVenta.textContent = '';
        const existencia = Number(p.existencia);

        if (existencia <= 0) {
            mensajeVenta.textContent = p.nombre + ' no tiene existencia.';
            return;
        }

        const actual = carrito.find(function (i) { return i.tipo === 'producto' && i.id_producto === p.id_producto; });
        if (actual) {
            if (actual.cantidad < actual.existencia) {
                actual.cantidad++;
            } else {
                mensajeVenta.textContent = 'No hay más existencia de ' + p.nombre + '.';
            }
        } else {
            carrito.push({
                tipo: 'producto',
                id_producto: p.id_producto,
                codigo: p.codigo,
                nombre: p.nombre,
                precio: Number(p.precio_venta),
                existencia: existencia,
                cantidad: 1,
                descuento: 0
            });
        }
        dibujarCarrito();
    }

    // Agrega un servicio al carrito (una sola linea, sin stock)
    function agregarServicio(s) {
        mensajeVenta.textContent = '';
        const id = Number(s.id_servicio);

        const actual = carrito.find(function (i) { return i.tipo === 'servicio' && i.id_servicio === id; });
        if (actual) {
            mensajeVenta.textContent = 'El servicio ya esta en la venta.';
            return;
        }

        carrito.push({
            tipo: 'servicio',
            id_servicio: id,
            codigo: s.categoria || '',
            nombre: s.nombre,
            precio: Number(s.precio),
            cantidad: 1,
            descuento: 0
        });
        dibujarCarrito();
    }

    // Codigo de barras: agrega el producto al dar Enter
    inputCodigo.addEventListener('keydown', async function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const codigo = inputCodigo.value.trim();
        if (codigo === '') return;

        try {
            const resp = await fetch(URL_VENTAS + '?accion=producto_por_codigo&codigo=' + encodeURIComponent(codigo));
            const datos = await resp.json();
            if (datos.error) {
                mensajeVenta.textContent = datos.error;
            } else {
                agregarAlCarrito(datos);
                inputCodigo.value = '';
            }
        } catch (err) {
            mensajeVenta.textContent = 'No se pudo buscar el producto.';
        }
    });

    // Nueva venta y cancelar venta

    // Deja la pantalla limpia
    function reiniciar() {
        carrito = [];
        mensajeVenta.textContent = '';
        inputCodigo.value = '';
        inputCliente.value = '';
        dibujarCarrito();
        inputCodigo.focus();
    }

    // Olvida la venta facturable y oculta el mensaje de factura
    function limpiarFactura() {
        ultimaVentaId = null;
        ultimoFolio = null;
        const mensajeFactura = document.getElementById('mensajeFactura');
        if (mensajeFactura) {
            mensajeFactura.textContent = '';
            mensajeFactura.hidden = true;
        }
    }

    // Pregunta antes de limpiar
    function confirmarYReiniciar(texto) {
        if (carrito.length === 0) {
            reiniciar();
            limpiarFactura();
            mensajeVenta.textContent = 'No hay productos ni servicios en la venta.';
            return;
        }
        if (confirm(texto)) {
            reiniciar();
            limpiarFactura();
            mensajeVenta.textContent = 'Venta cancelada.';
        }
    }

    btnNueva.addEventListener('click', function () {
        confirmarYReiniciar('Se perderá la venta actual. ¿Iniciar una nueva venta?');
    });

    btnCancelar.addEventListener('click', function () {
        confirmarYReiniciar('¿Cancelar la venta actual y limpiar la pantalla?');
    });

    // Modal de pago

    // Pone un monto con la palabra MXN chica
    function conMxn(elemento, monto) {
        elemento.textContent = dinero(monto) + ' ';
        const small = document.createElement('small');
        small.textContent = 'MXN';
        elemento.appendChild(small);
    }

    // Llena el resumen con lo que hay en el carrito
    function llenarPago() {
        const t = calcularTotales();
        tbodyPago.innerHTML = '';

        carrito.forEach(function (i) {
            const tr = document.createElement('tr');

            const icono = document.createElement('span');
            icono.className = 'pago-producto-icono';
            const detalle = document.createElement('span');
            detalle.className = 'pago-producto-detalle';
            const nombre = document.createElement('strong');
            nombre.textContent = i.nombre;
            const codigo = document.createElement('small');
            codigo.textContent = (i.tipo === 'servicio') ? 'Servicio · ' + i.codigo : 'Cód. ' + i.codigo;
            detalle.appendChild(nombre);
            detalle.appendChild(codigo);

            const tdProducto = document.createElement('td');
            tdProducto.appendChild(icono);
            tdProducto.appendChild(detalle);
            tr.appendChild(tdProducto);

            tr.appendChild(celda(i.cantidad));
            tr.appendChild(celda(dinero(i.precio)));
            tr.appendChild(celda(dinero(subtotalLinea(i))));
            tbodyPago.appendChild(tr);
        });

        totalesPago[0].textContent = dinero(t.subtotal);
        totalesPago[1].textContent = dinero(t.iva);
        totalesPago[2].textContent = dinero(t.total) + ' MXN';
        conMxn(totalPagar, t.total);
    }

    // Calcula el cambio y activa o apaga el boton de confirmar
    function actualizarPago() {
        const total = calcularTotales().total;
        const efectivo = formaPago === 'efectivo';

        // El monto solo se escribe en efectivo
        inputMonto.disabled = !efectivo;
        if (!efectivo) {
            inputMonto.value = total.toFixed(2);
        }

        const recibido = Number(inputMonto.value) || 0;
        conMxn(cambioPago, Math.max(0, redondear(recibido - total)));

        // Solo se confirma si el monto cubre el total
        const alcanza = Math.round(recibido * 100) >= Math.round(total * 100);
        btnConfirmar.disabled = !(total > 0 && alcanza);
    }

    // Marca la forma de pago elegida
    function elegirMetodo(metodo) {
        metodos.forEach(function (m) {
            const activo = (m === metodo);
            m.classList.toggle('seleccionado', activo);
            m.querySelector('.pago-metodo-marca').textContent = activo ? '✓' : '';
        });
        formaPago = metodo.querySelector('strong').textContent.trim().toLowerCase();
        actualizarPago();
    }

    metodos.forEach(function (m) {
        m.addEventListener('click', function () {
            elegirMetodo(m);
        });
    });

    inputMonto.addEventListener('input', actualizarPago);

    // Al pulsar Cobrar se prepara el modal (el modal ya se abre solo)
    btnCobrar.addEventListener('click', function () {
        if (carrito.length === 0) {
            modalPago.close();
            mensajeVenta.textContent = 'Agrega productos o servicios antes de cobrar.';
            return;
        }
        mensajeVenta.textContent = '';
        llenarPago();
        inputMonto.value = '';
        inputMonto.placeholder = '0.00';
        elegirMetodo(metodos[0]);
        inputMonto.focus();
    });
        // Confirmar pago: manda la venta al servidor
    btnConfirmar.addEventListener('click', async function () {
        // Si es abono de Pagos de servicios no se guarda la venta
        if (typeof contextoPago !== 'undefined' && contextoPago && contextoPago.tipo === 'servicio') return;

        // Solo termina si el carrito completo esta vacio
        if (carrito.length === 0) return;

        const modalConfirmado = document.getElementById('modalPagoConfirmado');

        // Evita que den doble clic
        btnConfirmar.disabled = true;

        // Datos de tarjeta / transferencia (se leen antes de que el modal se cierre)
        const campoNumero = document.getElementById('numeroTarjeta');
        const campoTitular = document.getElementById('nombreTarjeta');
        const campoVencimiento = document.getElementById('vencimientoTarjeta');
        const campoCvv = document.getElementById('cvvTarjeta');
        const campoReferencia = document.getElementById('referenciaTransferencia');

        let datosPago = null;
        if (formaPago === 'tarjeta') {
            datosPago = {
                numero_tarjeta: campoNumero ? campoNumero.value.replace(/\D/g, '') : '',
                titular: campoTitular ? campoTitular.value.trim() : '',
                vencimiento: campoVencimiento ? campoVencimiento.value.trim() : '',
                cvv: campoCvv ? campoCvv.value.trim() : ''
            };
        } else if (formaPago === 'transferencia') {
            datosPago = {
                referencia: campoReferencia ? campoReferencia.value.trim() : ''
            };
        }

        const datos = {
            forma_pago: formaPago,
            recibido: Number(inputMonto.value) || 0,
            datos_pago: datosPago,
            items: carrito.filter(function (i) { return i.tipo === 'producto'; }).map(function (i) {
                return {
                    id_producto: i.id_producto,
                    cantidad: i.cantidad,
                    descuento: i.descuento
                };
            }),
            servicios: carrito.filter(function (i) { return i.tipo === 'servicio'; }).map(function (i) {
                return {
                    id_servicio: i.id_servicio,
                    descuento: i.descuento
                };
            })
        };

        try {
            const resp = await fetch(URL_VENTAS + '?accion=guardar_venta', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            const r = await resp.json();

            if (!r.ok) {
                mostrarError('No se pudo guardar: ' + r.error);
                return;
            }

            if (modalPago.open) modalPago.close();
            limpiarCamposPago();
            reiniciar();

            // Venta nueva guardada: se recuerda para facturarla
            limpiarFactura();
            ultimaVentaId = r.id_venta;
            ultimoFolio = r.folio;

            // Muestra el resultado real en el modal de confirmacion
            if (modalConfirmado) {
                const texto = modalConfirmado.querySelector('.resultado-mensaje');
                if (texto) {
                    texto.textContent = 'Venta ' + r.folio + ' guardada correctamente.' +
                        (r.cambio > 0 ? ' Cambio: ' + dinero(r.cambio) : '');
                }
                const totalReal = document.getElementById('totalVentaDemostracion');
                if (totalReal) totalReal.textContent = dinero(r.total);
                if (!modalConfirmado.open) modalConfirmado.showModal();
            }
        } catch (e) {
            mostrarError('Error de conexion con el servidor.');
        } finally {
            // No deja los datos sensibles en memoria
            datosPago = null;
            datos.datos_pago = null;
        }

        // Cierra la confirmacion, avisa y regresa al modal de pago
        function mostrarError(msg) {
            if (modalConfirmado && modalConfirmado.open) modalConfirmado.close();
            alert(msg);
            if (!modalPago.open) modalPago.showModal();
            // Sincroniza la forma de pago con el boton que quedo marcado en pantalla
            const marcado = Array.prototype.find.call(metodos, function (m) {
                return m.classList.contains('seleccionado');
            });
            if (marcado) formaPago = marcado.querySelector('strong').textContent.trim().toLowerCase();
            actualizarPago();
        }

        // Limpia los campos de tarjeta y la referencia tras guardar
        function limpiarCamposPago() {
            [campoNumero, campoTitular, campoVencimiento, campoCvv, campoReferencia].forEach(function (c) {
                if (c) c.value = '';
            });
        }
    });

    // Factura de la ultima venta guardada.
    // El listener inline de Ventas.php corre antes (cierra el modal y pone su texto);
    // este resultado llega despues y lo reemplaza.
    const formFactura = document.getElementById('formDatosFactura');
    if (formFactura) {
        formFactura.addEventListener('submit', async function (e) {
            e.preventDefault();
            const mensajeFactura = document.getElementById('mensajeFactura');

            function avisarFactura(texto) {
                if (!mensajeFactura) return;
                mensajeFactura.textContent = texto;
                mensajeFactura.hidden = false;
            }

            // Error: avisa y regresa al formulario para corregir
            function errorFactura(texto) {
                avisarFactura(texto);
                const modalFactura = document.getElementById('modalDatosFactura');
                if (modalFactura && !modalFactura.open) modalFactura.showModal();
            }

            if (!ultimaVentaId) {
                avisarFactura('No hay una venta guardada para facturar.');
                return;
            }
            // Evita doble envio
            if (enviandoFactura) return;
            enviandoFactura = true;

            const valor = function (id) {
                const campo = document.getElementById(id);
                return campo ? campo.value.trim() : '';
            };
            const datosFactura = {
                id_venta: ultimaVentaId,
                rfc: valor('facturaRfc').toUpperCase(),
                razon_social: valor('facturaRazonSocial'),
                codigo_postal: valor('facturaCodigoPostal'),
                correo: valor('facturaCorreo')
            };
            const folio = ultimoFolio;

            try {
                const resp = await fetch(URL_VENTAS + '?accion=guardar_factura', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosFactura)
                });
                const r = await resp.json();

                if (r.ok) {
                    avisarFactura('Factura solicitada para la venta ' + folio + '.');
                } else {
                    errorFactura(r.error || 'No se pudo guardar la factura.');
                }
            } catch (err) {
                errorFactura('Error de conexion con el servidor.');
            } finally {
                enviandoFactura = false;
            }
        });
    }

    // Dibuja el carrito vacio al cargar
    dibujarCarrito();
})(); 