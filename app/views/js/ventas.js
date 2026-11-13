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
    let carrito = [];
    let formaPago = 'efectivo';

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
            const td = celda('No hay productos en la venta.');
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

        const actual = carrito.find(function (i) { return i.id_producto === p.id_producto; });
        if (actual) {
            if (actual.cantidad < actual.existencia) {
                actual.cantidad++;
            } else {
                mensajeVenta.textContent = 'No hay más existencia de ' + p.nombre + '.';
            }
        } else {
            carrito.push({
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

    // Pregunta antes de limpiar
    function confirmarYReiniciar(texto) {
        if (carrito.length === 0) {
            reiniciar();
            mensajeVenta.textContent = 'No hay productos en la venta.';
            return;
        }
        if (confirm(texto)) {
            reiniciar();
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
            codigo.textContent = 'Cód. ' + i.codigo;
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
            mensajeVenta.textContent = 'Agrega productos antes de cobrar.';
            return;
        }
        mensajeVenta.textContent = '';
        llenarPago();
        inputMonto.value = '';
        inputMonto.placeholder = '0.00';
        elegirMetodo(metodos[0]);
        inputMonto.focus();
    });

    // Dibuja el carrito vacio al cargar
    dibujarCarrito();
})(); 