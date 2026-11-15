(function () {
    const URL_VENTAS = '../controllers/ventas.php';
    const POR_PAGINA = 10;

    // Elementos de la pagina
    const inputBuscar = document.getElementById('buscarVenta');
    const inputDesde = document.getElementById('fechaDesde');
    const inputHasta = document.getElementById('fechaHasta');
    const selectEstado = document.getElementById('estadoVenta');
    const btnBuscar = document.getElementById('btnBuscarHistorial');
    const btnLimpiar = document.getElementById('btnLimpiarHistorial');
    const tbody = document.getElementById('tablaHistorial');
    const textoConteo = document.getElementById('textoConteo');
    const paginacion = document.getElementById('paginacionHistorial');

    // Modales
    const modalDetalle = document.getElementById('modalDetalleVenta');
    const contenidoDetalle = document.getElementById('contenidoDetalleVenta');
    const tituloDetalle = document.getElementById('tituloDetalleVenta');
    const modalFactura = document.getElementById('modalFacturaVenta');
    const formFactura = document.getElementById('formFacturaVenta');
    const inputRfc = document.getElementById('facturaRfc');
    const inputRazon = document.getElementById('facturaRazonSocial');
    const inputCp = document.getElementById('facturaCodigoPostal');
    const inputCorreo = document.getElementById('facturaCorreo');
    const errorFactura = document.getElementById('errorFactura');
    const btnCancelarFactura = document.getElementById('btnCancelarFactura');
    const btnGuardarFactura = document.getElementById('btnGuardarFactura');

    const moneda = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });

    let paginaActual = 1;
    let peticion = 0;
    let ventaFactura = 0;

    // Funciones de apoyo
    function dinero(n) { return moneda.format(Number(n) || 0); }

    function dosDigitos(n) { return String(n).padStart(2, '0'); }

    // Fecha de hoy en hora local como YYYY-MM-DD
    function hoy() {
        const d = new Date();
        return d.getFullYear() + '-' + dosDigitos(d.getMonth() + 1) + '-' + dosDigitos(d.getDate());
    }

    // De YYYY-MM-DD HH:MM:SS a dd/mm/aaaa hh:mm
    function formatoFecha(texto) {
        const m = /^(\d{4})-(\d{2})-(\d{2})[ T](\d{2}):(\d{2})/.exec(texto || '');
        if (!m) return texto || '';
        return m[3] + '/' + m[2] + '/' + m[1] + ' ' + m[4] + ':' + m[5];
    }

    function mayuscula(texto) {
        texto = String(texto || '');
        return texto.charAt(0).toUpperCase() + texto.slice(1);
    }

    function porcentaje(n) {
        return (Number(n) || 0) + '%';
    }

    // Crea un elemento con texto y estilos opcionales
    function el(tag, texto, css) {
        const e = document.createElement(tag);
        if (texto !== undefined && texto !== null) e.textContent = texto;
        if (css) Object.assign(e.style, css);
        return e;
    }

    function celda(contenido) {
        const td = document.createElement('td');
        if (contenido instanceof Node) {
            td.appendChild(contenido);
        } else {
            td.textContent = contenido;
        }
        return td;
    }

    function boton(texto, clase, etiqueta, accion) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = clase;
        b.textContent = texto;
        b.setAttribute('aria-label', etiqueta);
        b.addEventListener('click', accion);
        return b;
    }

    // Telefono a mostrar, o raya si no aplica
    function telefonoCliente(v) {
        const tel = String(v.cliente_telefono || '').trim();
        if (v.cliente_nombre === 'Publico en general' || tel === '' || tel === '0000000000') return '—';
        return 'Tel. ' + tel;
    }

    // Pide JSON al servidor
    async function pedir(url, opciones) {
        const resp = await fetch(url, opciones);
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        return resp.json();
    }

    // Listado

    function filaMensaje(texto) {
        tbody.innerHTML = '';
        const tr = document.createElement('tr');
        const td = celda(texto);
        td.colSpan = 9;
        td.className = 'vacio';
        tr.appendChild(td);
        tbody.appendChild(tr);
    }

    function filtros(pagina) {
        const p = new URLSearchParams();
        p.set('accion', 'historial_ventas');
        p.set('q', inputBuscar.value.trim());
        p.set('desde', inputDesde.value);
        p.set('hasta', inputHasta.value);
        p.set('estado', selectEstado.value);
        p.set('pagina', String(pagina));
        return p.toString();
    }

    async function cargar(pagina) {
        paginaActual = pagina;
        const miPeticion = ++peticion;
        filaMensaje('Cargando ventas...');

        try {
            const datos = await pedir(URL_VENTAS + '?' + filtros(pagina));
            // Si ya se pidio otra pagina, ignora esta
            if (miPeticion !== peticion) return;
            dibujarTabla(datos.ventas || []);
            dibujarPie(Number(datos.total) || 0, Number(datos.pagina) || pagina, Number(datos.por_pagina) || POR_PAGINA);
        } catch (e) {
            if (miPeticion !== peticion) return;
            filaMensaje('No se pudo cargar el historial.');
            dibujarPie(0, 1, POR_PAGINA);
        }
    }

    function dibujarTabla(ventas) {
        if (ventas.length === 0) {
            filaMensaje('No hay ventas con esos filtros.');
            return;
        }
        tbody.innerHTML = '';

        ventas.forEach(function (v) {
            const tr = document.createElement('tr');
            const folio = v.folio || '';
            const pagada = v.estado === 'Pagada';

            tr.appendChild(celda(el('strong', folio)));
            tr.appendChild(celda(formatoFecha(v.fecha)));

            const tdCliente = celda(v.cliente_nombre || '');
            tdCliente.appendChild(el('small', telefonoCliente(v)));
            tr.appendChild(tdCliente);

            tr.appendChild(celda(v.empleado_nombre || ''));
            tr.appendChild(celda(String(v.num_items)));
            tr.appendChild(celda(dinero(v.total)));
            tr.appendChild(celda(mayuscula(v.forma_pago)));

            const estado = el('span', v.estado || '');
            estado.className = 'historial-estado ' + String(v.estado || '').toLowerCase();
            tr.appendChild(celda(estado));

            // Botones de accion
            const acciones = document.createElement('div');
            acciones.className = 'historial-acciones';

            acciones.appendChild(boton('Detalles', 'btn btn-chico accion-detalles', 'Ver venta ' + folio, function () {
                abrirDetalle(v.id_venta);
            }));

            const btnImprimir = boton('Imprimir ticket', 'btn btn-chico accion-imprimir', 'Imprimir ticket de la venta ' + folio, function () {
                imprimirTicket(v.id_venta);
            });
            btnImprimir.disabled = !pagada;
            acciones.appendChild(btnImprimir);

            const btnFactura = boton(v.tiene_factura ? 'Factura solicitada' : 'Generar factura',
                'btn btn-chico accion-factura', 'Generar factura de la venta ' + folio, function () {
                    abrirFactura(v.id_venta);
                });
            btnFactura.disabled = !pagada || !!v.tiene_factura;
            acciones.appendChild(btnFactura);

            tr.appendChild(celda(acciones));
            tbody.appendChild(tr);
        });
    }

    function botonPagina(texto, pagina, deshabilitado, etiqueta) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'btn btn-chico';
        b.textContent = texto;
        if (etiqueta) b.setAttribute('aria-label', etiqueta);
        b.disabled = deshabilitado;
        b.addEventListener('click', function () { cargar(pagina); });
        return b;
    }

    function dibujarPie(total, pagina, porPagina) {
        const paginas = Math.max(1, Math.ceil(total / porPagina));
        const inicio = total === 0 ? 0 : (pagina - 1) * porPagina + 1;
        const fin = Math.min(total, pagina * porPagina);
        textoConteo.textContent = 'Mostrando ' + inicio + ' - ' + fin + ' de ' + total + ' ventas';

        paginacion.innerHTML = '';
        paginacion.appendChild(botonPagina('‹', pagina - 1, pagina <= 1, 'Página anterior'));

        // Hasta 5 numeros alrededor de la actual
        let desde = Math.max(1, pagina - 2);
        let hasta = Math.min(paginas, desde + 4);
        desde = Math.max(1, hasta - 4);
        for (let n = desde; n <= hasta; n++) {
            const b = botonPagina(String(n), n, false);
            if (n === pagina) {
                b.classList.add('pagina-activa');
                b.setAttribute('aria-current', 'page');
            }
            paginacion.appendChild(b);
        }

        paginacion.appendChild(botonPagina('›', pagina + 1, pagina >= paginas, 'Página siguiente'));
    }

    // Detalle de venta

    function tabla(encabezados, filas) {
        const envoltura = el('div');
        envoltura.className = 'tabla-envoltura';
        const t = document.createElement('table');
        const thead = document.createElement('thead');
        const trh = document.createElement('tr');
        encabezados.forEach(function (h) { trh.appendChild(el('th', h)); });
        thead.appendChild(trh);
        t.appendChild(thead);
        const tb = document.createElement('tbody');
        filas.forEach(function (f) {
            const tr = document.createElement('tr');
            f.forEach(function (c) { tr.appendChild(celda(c)); });
            tb.appendChild(tr);
        });
        t.appendChild(tb);
        envoltura.appendChild(t);
        return envoltura;
    }

    // Renglon etiqueta: valor
    function dato(etiqueta, valor) {
        const p = el('p', null, { margin: '4px 0' });
        p.appendChild(el('strong', etiqueta + ': '));
        p.appendChild(document.createTextNode(valor));
        return p;
    }

    function dibujarDetalle(d) {
        contenidoDetalle.innerHTML = '';
        // Titulo con el folio real
        tituloDetalle.textContent = 'Detalle de venta ' + (d.folio || '');

        // Encabezado
        const cab = el('div', null, {
            display: 'grid',
            gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))',
            gap: '0 16px',
            marginBottom: '12px'
        });
        cab.appendChild(dato('Folio', d.folio || ''));
        cab.appendChild(dato('Fecha', formatoFecha(d.fecha)));
        cab.appendChild(dato('Cliente', d.cliente_nombre || ''));
        cab.appendChild(dato('Empleado', d.empleado_nombre || ''));
        cab.appendChild(dato('Estado', d.estado || ''));
        cab.appendChild(dato('Forma de pago', mayuscula(d.forma_pago) || '—'));
        if (d.forma_pago === 'efectivo') {
            cab.appendChild(dato('Monto recibido', dinero(d.monto_recibido)));
            cab.appendChild(dato('Cambio', dinero(d.cambio)));
        }
        contenidoDetalle.appendChild(cab);

        // Productos
        const productos = d.productos || [];
        contenidoDetalle.appendChild(el('h2', 'Productos', { fontSize: '16px', margin: '12px 0 6px' }));
        if (productos.length === 0) {
            contenidoDetalle.appendChild(el('p', 'Sin productos.'));
        } else {
            contenidoDetalle.appendChild(tabla(
                ['Código', 'Producto', 'Cantidad', 'Precio unitario', 'Descuento', 'IVA', 'Subtotal'],
                productos.map(function (p) {
                    return [p.codigo || '', p.nombre || '', String(p.cantidad), dinero(p.precio_unitario),
                        porcentaje(p.descuento), dinero(p.iva), dinero(p.subtotal)];
                })
            ));
        }

        // Servicios, solo si hay
        const servicios = d.servicios || [];
        if (servicios.length > 0) {
            contenidoDetalle.appendChild(el('h2', 'Servicios', { fontSize: '16px', margin: '12px 0 6px' }));
            contenidoDetalle.appendChild(tabla(
                ['Servicio', 'Categoría', 'Precio unitario', 'Descuento', 'IVA', 'Subtotal'],
                servicios.map(function (s) {
                    return [s.nombre || '', s.categoria || '', dinero(s.precio_unitario),
                        porcentaje(s.descuento), dinero(s.iva), dinero(s.subtotal)];
                })
            ));
        }

        // Totales
        const tot = el('div', null, { textAlign: 'right', marginTop: '12px' });
        tot.appendChild(dato('Subtotal', dinero(d.subtotal)));
        tot.appendChild(dato('IVA', dinero(d.iva)));
        tot.appendChild(dato('Total', dinero(d.total)));
        contenidoDetalle.appendChild(tot);

        // Datos de factura
        if (d.factura) {
            const f = d.factura;
            const caja = el('div', null, {
                marginTop: '12px',
                padding: '10px',
                border: '1px solid var(--borde, #ccc)',
                borderRadius: '8px'
            });
            caja.appendChild(el('h2', 'Datos de facturación', { fontSize: '16px', margin: '0 0 6px' }));
            caja.appendChild(dato('RFC', f.rfc || ''));
            caja.appendChild(dato('Razón social', f.razon_social || ''));
            caja.appendChild(dato('Código postal', f.codigo_postal || ''));
            caja.appendChild(dato('Correo', f.correo || ''));
            caja.appendChild(dato('Fecha de solicitud', formatoFecha(f.fecha_solicitud)));
            contenidoDetalle.appendChild(caja);
        }
    }

    async function abrirDetalle(id) {
        try {
            const d = await pedir(URL_VENTAS + '?accion=detalle_venta&id=' + encodeURIComponent(id));
            if (d.error) {
                alert(d.error);
                return;
            }
            dibujarDetalle(d);
            if (!modalDetalle.open) modalDetalle.showModal();
        } catch (e) {
            alert('No se pudo cargar el detalle de la venta.');
        }
    }

    // Ticket

    function imprimirTicket(id) {
        // Se abre en el clic para que no la bloqueen
        const w = window.open('', '_blank', 'width=420,height=640');
        if (!w) {
            alert('Permite las ventanas emergentes para imprimir el ticket.');
            return;
        }
        w.document.write(
            '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Ticket</title>' +
            '<style>' +
            '@page { size: 80mm auto; margin: 4mm; }' +
            'body { width: 72mm; margin: 0 auto; font-family: "Courier New", monospace; font-size: 12px; color: #000; }' +
            'h1 { font-size: 16px; text-align: center; margin: 4px 0; }' +
            'p { margin: 2px 0; }' +
            '.linea { display: flex; justify-content: space-between; gap: 6px; }' +
            '.sep { border-top: 1px dashed #000; margin: 6px 0; }' +
            '.centro { text-align: center; }' +
            '.fuerte { font-weight: bold; }' +
            '</style></head><body><p class="centro">Cargando ticket...</p></body></html>'
        );
        w.document.close();

        pedir(URL_VENTAS + '?accion=detalle_venta&id=' + encodeURIComponent(id))
            .then(function (d) {
                if (d.error) throw new Error(d.error);
                escribirTicket(w, d);
                w.focus();
                w.print();
            })
            .catch(function () {
                if (!w.closed) {
                    w.document.body.textContent = 'No se pudo cargar el ticket.';
                }
            });
    }

    function escribirTicket(w, d) {
        const doc = w.document;
        const body = doc.body;
        body.textContent = '';

        function p(texto, clase) {
            const e = doc.createElement('p');
            e.textContent = texto;
            if (clase) e.className = clase;
            body.appendChild(e);
        }
        function linea(izq, der, clase) {
            const e = doc.createElement('div');
            e.className = 'linea' + (clase ? ' ' + clase : '');
            const a = doc.createElement('span');
            a.textContent = izq;
            const b = doc.createElement('span');
            b.textContent = der;
            e.appendChild(a);
            e.appendChild(b);
            body.appendChild(e);
        }
        function sep() {
            const e = doc.createElement('div');
            e.className = 'sep';
            body.appendChild(e);
        }

        const h = doc.createElement('h1');
        h.textContent = 'PetCare';
        body.appendChild(h);
        sep();
        p('Folio: ' + (d.folio || ''));
        p('Fecha: ' + formatoFecha(d.fecha));
        p('Cajero: ' + (d.empleado_nombre || ''));
        p('Cliente: ' + (d.cliente_nombre || ''));
        sep();

        (d.productos || []).forEach(function (x) {
            linea(x.cantidad + ' x ' + (x.nombre || ''), dinero(x.subtotal));
        });
        (d.servicios || []).forEach(function (x) {
            linea('1 x ' + (x.nombre || ''), dinero(x.subtotal));
        });
        sep();

        linea('Subtotal', dinero(d.subtotal));
        linea('IVA', dinero(d.iva));
        linea('Total', dinero(d.total), 'fuerte');
        sep();
        linea('Forma de pago', mayuscula(d.forma_pago));
        if (d.forma_pago === 'efectivo') {
            linea('Recibido', dinero(d.monto_recibido));
            linea('Cambio', dinero(d.cambio));
        }
        sep();
        p('Gracias por su compra', 'centro');
        doc.title = 'Ticket ' + (d.folio || '');
    }

    // Factura

    function abrirFactura(id) {
        ventaFactura = id;
        formFactura.reset();
        errorFactura.textContent = '';
        btnGuardarFactura.disabled = false;
        if (!modalFactura.open) modalFactura.showModal();
        inputRfc.focus();
    }

    btnCancelarFactura.addEventListener('click', function () {
        modalFactura.close();
    });

    inputRfc.addEventListener('input', function () {
        inputRfc.value = inputRfc.value.toUpperCase();
    });

    inputCp.addEventListener('input', function () {
        inputCp.value = inputCp.value.replace(/\D/g, '').slice(0, 5);
    });

    // Revisa los datos antes de mandarlos
    function validarFactura(datos) {
        if (!/^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/.test(datos.rfc)) {
            return { campo: inputRfc, msg: 'El RFC debe tener 12 o 13 caracteres en mayúsculas con formato válido.' };
        }
        if (datos.razon_social.length < 3 || datos.razon_social.length > 100) {
            return { campo: inputRazon, msg: 'La razón social debe tener de 3 a 100 caracteres.' };
        }
        if (!/^[0-9]{5}$/.test(datos.codigo_postal)) {
            return { campo: inputCp, msg: 'El código postal debe tener 5 dígitos.' };
        }
        if (datos.correo === '' || !inputCorreo.checkValidity()) {
            return { campo: inputCorreo, msg: 'Escribe un correo válido.' };
        }
        return null;
    }

    formFactura.addEventListener('submit', async function (e) {
        e.preventDefault();
        errorFactura.textContent = '';

        const datos = {
            id_venta: ventaFactura,
            rfc: inputRfc.value.trim().toUpperCase(),
            razon_social: inputRazon.value.trim(),
            codigo_postal: inputCp.value.trim(),
            correo: inputCorreo.value.trim()
        };

        const fallo = validarFactura(datos);
        if (fallo) {
            errorFactura.textContent = fallo.msg;
            fallo.campo.focus();
            return;
        }

        btnGuardarFactura.disabled = true;
        try {
            const r = await pedir(URL_VENTAS + '?accion=guardar_factura', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            if (r.ok) {
                modalFactura.close();
                cargar(paginaActual);
                return;
            }
            errorFactura.textContent = r.error || 'No se pudo guardar la factura.';
        } catch (err) {
            errorFactura.textContent = 'Error de conexión con el servidor.';
        }
        btnGuardarFactura.disabled = false;
    });

    // Filtros

    btnBuscar.addEventListener('click', function () { cargar(1); });

    inputBuscar.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            cargar(1);
        }
    });

    selectEstado.addEventListener('change', function () { cargar(1); });
    inputDesde.addEventListener('change', function () { cargar(1); });
    inputHasta.addEventListener('change', function () { cargar(1); });

    btnLimpiar.addEventListener('click', function () {
        inputBuscar.value = '';
        selectEstado.value = '';
        inputDesde.value = hoy();
        inputHasta.value = hoy();
        cargar(1);
    });

    // Al cargar: fechas de hoy y primera pagina
    inputDesde.value = hoy();
    inputHasta.value = hoy();
    cargar(1);
})();
