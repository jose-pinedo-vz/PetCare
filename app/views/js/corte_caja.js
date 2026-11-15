(() => {
    const formatMoney = new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN',
        minimumFractionDigits: 2
    });
    const tabs = Array.from(document.querySelectorAll('.corte-tab[role="tab"]'));
    const panels = Array.from(document.querySelectorAll('.corte-panel[role="tabpanel"]'));
    const tablaMovimientos = document.getElementById('tablaMovimientos');
    const inputFondo = document.getElementById('fondoInicial');
    const inputContado = document.getElementById('efectivoContado');
    const dialogArqueo = document.getElementById('modalArqueo');
    const dialogSalida = document.getElementById('modalSalida');
    const dialogCierre = document.getElementById('modalConfirmarCierre');
    const dialogCierreMensual = document.getElementById('modalCierreMensual');
    const dialogDetalle = document.getElementById('modalDetalleCorte');
    const aviso = document.getElementById('avisoCorte');
    const botonAbrirCaja = document.getElementById('abrirCaja');
    const botonCerrarCorte = document.getElementById('abrirConfirmarCierre');
    let avisoTemporizador;

    function moneda(valor) {
        return formatMoney.format(valor);
    }

    function numero(valor) {
        const resultado = Number.parseFloat(valor);
        return Number.isFinite(resultado) ? resultado : 0;
    }

    function mostrarAviso(mensaje) {
        aviso.textContent = mensaje;
        aviso.hidden = false;
        window.clearTimeout(avisoTemporizador);
        avisoTemporizador = window.setTimeout(() => {
            aviso.hidden = true;
        }, 4200);
    }

    function seleccionarTab(tab, moverFoco) {
        const seleccionada = tab.dataset.tab;
        tabs.forEach((elemento) => {
            const activa = elemento === tab;
            elemento.classList.toggle('activa', activa);
            elemento.setAttribute('aria-selected', String(activa));
            elemento.tabIndex = activa ? 0 : -1;
        });
        panels.forEach((panel) => {
            panel.hidden = panel.dataset.panel !== seleccionada;
        });
        if (moverFoco) tab.focus();
    }

    tabs.forEach((tab, indice) => {
        tab.addEventListener('click', () => seleccionarTab(tab, false));
        tab.addEventListener('keydown', (evento) => {
            let destino = indice;
            if (evento.key === 'ArrowRight') destino = (indice + 1) % tabs.length;
            else if (evento.key === 'ArrowLeft') destino = (indice - 1 + tabs.length) % tabs.length;
            else if (evento.key === 'Home') destino = 0;
            else if (evento.key === 'End') destino = tabs.length - 1;
            else return;
            evento.preventDefault();
            seleccionarTab(tabs[destino], true);
        });
    });

    const fechaActual = document.getElementById('fechaActual');
    fechaActual.textContent = new Intl.DateTimeFormat('es-MX', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(new Date());

    function recalcularCorte() {
        const filaFondo = tablaMovimientos.querySelector('tr[data-impact]');
        const fondo = Math.max(0, numero(inputFondo.value));
        filaFondo.dataset.impact = String(fondo);
        filaFondo.querySelector('[data-amount]').textContent = moneda(fondo);

        const esperado = Array.from(tablaMovimientos.querySelectorAll('tr[data-impact]'))
            .reduce((total, fila) => total + numero(fila.dataset.impact), 0);
        const contado = Math.max(0, numero(inputContado.value));
        const diferencia = Math.round((contado - esperado) * 100) / 100;
        const tarjetaDiferencia = document.getElementById('tarjetaDiferencia');
        const diferenciaTexto = document.getElementById('estadoDiferencia');

        document.getElementById('efectivoEsperado').textContent = moneda(esperado);
        document.getElementById('totalMovimientos').textContent = moneda(esperado);
        document.getElementById('diferenciaCorte').textContent = moneda(Math.abs(diferencia));
        tarjetaDiferencia.classList.remove('corte-kpi--faltante', 'corte-kpi--sobrante');
        if (diferencia < 0) {
            tarjetaDiferencia.classList.add('corte-kpi--faltante');
            diferenciaTexto.textContent = `Faltante de ${moneda(Math.abs(diferencia))}`;
        } else if (diferencia > 0) {
            tarjetaDiferencia.classList.add('corte-kpi--sobrante');
            diferenciaTexto.textContent = `Sobrante de ${moneda(diferencia)}`;
        } else {
            diferenciaTexto.textContent = 'Caja cuadrada';
        }
    }

    inputFondo.addEventListener('input', recalcularCorte);
    inputContado.addEventListener('input', recalcularCorte);

    botonAbrirCaja.addEventListener('click', () => {
        const estado = document.getElementById('estadoTurno');
        const turno = document.getElementById('turnoCorte');
        const hora = new Intl.DateTimeFormat('es-MX', {
            hour: '2-digit',
            minute: '2-digit'
        }).format(new Date());
        estado.textContent = 'Abierto';
        estado.classList.remove('cerrado');
        estado.classList.add('abierto');
        document.getElementById('tituloEstadoTurno').textContent = `Turno ${turno.options[turno.selectedIndex].text} iniciado`;
        document.getElementById('detalleEstadoTurno').textContent = `Hoy, ${hora}`;
        document.getElementById('turnoSesion').textContent = turno.options[turno.selectedIndex].text;
        botonAbrirCaja.hidden = true;
        botonCerrarCorte.disabled = false;
        botonCerrarCorte.focus();
        mostrarAviso('Apertura de demostración; no se guardó en la base de datos.');
    });

    function abrirDialog(dialogo) {
        if (!dialogo.open) dialogo.showModal();
    }

    document.getElementById('abrirArqueo').addEventListener('click', () => abrirDialog(dialogArqueo));
    document.getElementById('abrirSalida').addEventListener('click', () => {
        document.getElementById('errorSalida').hidden = true;
        abrirDialog(dialogSalida);
    });
    document.getElementById('abrirConfirmarCierre').addEventListener('click', () => {
        document.getElementById('resumenCierreEsperado').textContent = document.getElementById('efectivoEsperado').textContent;
        document.getElementById('resumenCierreContado').textContent = moneda(numero(inputContado.value));
        document.getElementById('resumenCierreDiferencia').textContent = document.getElementById('diferenciaCorte').textContent;
        abrirDialog(dialogCierre);
    });
    document.getElementById('abrirCierreMensual').addEventListener('click', () => abrirDialog(dialogCierreMensual));

    document.querySelectorAll('[data-cerrar-dialog]').forEach((boton) => {
        boton.addEventListener('click', () => boton.closest('dialog').close());
    });
    document.querySelectorAll('dialog.corte-dialog').forEach((dialogo) => {
        dialogo.addEventListener('click', (evento) => {
            if (evento.target === dialogo) dialogo.close();
        });
    });

    function actualizarArqueo() {
        let total = 0;
        dialogArqueo.querySelectorAll('[data-cantidad]').forEach((campo) => {
            const subtotal = Math.max(0, numero(campo.value)) * numero(campo.dataset.valor);
            total += subtotal;
            campo.closest('tr').querySelector('[data-subtotal]').textContent = moneda(subtotal);
        });
        document.getElementById('totalArqueo').textContent = moneda(total);
    }

    dialogArqueo.querySelectorAll('[data-cantidad]').forEach((campo) => {
        campo.addEventListener('input', actualizarArqueo);
    });
    document.getElementById('aplicarArqueo').addEventListener('click', () => {
        actualizarArqueo();
        const total = dialogArqueo.querySelectorAll('[data-cantidad]')
            .length
            ? Array.from(dialogArqueo.querySelectorAll('[data-cantidad]'))
                .reduce((suma, campo) => suma + Math.max(0, numero(campo.value)) * numero(campo.dataset.valor), 0)
            : 0;
        inputContado.value = total.toFixed(2);
        inputContado.dispatchEvent(new Event('input', { bubbles: true }));
        dialogArqueo.close();
    });

    document.getElementById('guardarSalida').addEventListener('click', () => {
        const concepto = document.getElementById('conceptoSalida').value.trim();
        const monto = numero(document.getElementById('montoSalida').value);
        const error = document.getElementById('errorSalida');
        if (!concepto || monto <= 0) {
            error.hidden = false;
            return;
        }

        const fila = document.createElement('tr');
        fila.dataset.impact = String(-monto);
        const tipo = document.createElement('td');
        const etiquetaTipo = document.createElement('span');
        etiquetaTipo.className = 'corte-tipo-movimiento salida';
        etiquetaTipo.textContent = 'Salida';
        tipo.appendChild(etiquetaTipo);

        const celdaConcepto = document.createElement('td');
        const categoria = document.getElementById('tipoSalida').value;
        celdaConcepto.textContent = `${categoria}: ${concepto}`;
        const hora = document.createElement('td');
        hora.textContent = new Intl.DateTimeFormat('es-MX', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        }).format(new Date());
        const responsable = document.createElement('td');
        responsable.textContent = 'María López';
        const importe = document.createElement('td');
        importe.className = 'corte-monto';
        importe.dataset.amount = '';
        importe.textContent = `−${moneda(monto)}`;
        fila.append(tipo, celdaConcepto, hora, responsable, importe);
        tablaMovimientos.appendChild(fila);

        dialogSalida.close();
        document.getElementById('conceptoSalida').value = '';
        document.getElementById('montoSalida').value = '';
        document.getElementById('notaSalida').value = '';
        error.hidden = true;
        recalcularCorte();
        mostrarAviso('Movimiento agregado a esta vista de demostración; no se guardó.');
    });

    document.getElementById('confirmarCierre').addEventListener('click', () => {
        const estado = document.getElementById('estadoTurno');
        estado.textContent = 'Cerrado';
        estado.classList.remove('abierto');
        estado.classList.add('cerrado');
        document.getElementById('tituloEstadoTurno').textContent = 'Sin corte abierto';
        document.getElementById('detalleEstadoTurno').textContent = 'Configura el turno y el fondo para iniciar.';
        botonAbrirCaja.hidden = false;
        botonCerrarCorte.disabled = true;
        dialogCierre.close();
        botonAbrirCaja.focus();
        mostrarAviso('Corte marcado como cerrado solo en esta vista; no se guardó en la base de datos.');
    });

    document.getElementById('confirmarCierreMensual').addEventListener('click', () => {
        dialogCierreMensual.close();
        mostrarAviso('Cierre mensual simulado. No se generó ni guardó ningún reporte.');
    });

    const filasHistorial = Array.from(document.querySelectorAll('#tablaHistorial tr'));
    const filtroTipo = document.getElementById('filtroTipo');
    const filtroPeriodo = document.getElementById('filtroPeriodo');
    const buscarHistorial = document.getElementById('buscarHistorial');

    function filtrarHistorial() {
        const tipo = filtroTipo.value;
        const periodo = filtroPeriodo.value;
        const busqueda = buscarHistorial.value.trim().toLocaleLowerCase('es-MX');
        let visibles = 0;
        filasHistorial.forEach((fila) => {
            const coincideTipo = tipo === 'todos' || fila.dataset.kind === tipo;
            const coincidePeriodo = !periodo || fila.dataset.period === periodo;
            const coincideTexto = !busqueda || fila.dataset.search.includes(busqueda);
            fila.hidden = !(coincideTipo && coincidePeriodo && coincideTexto);
            if (!fila.hidden) visibles += 1;
        });
        document.getElementById('resultadoHistorial').textContent = `${visibles} registro${visibles === 1 ? '' : 's'} de ejemplo.`;
    }

    [filtroTipo, filtroPeriodo, buscarHistorial].forEach((control) => {
        control.addEventListener(control === buscarHistorial ? 'input' : 'change', filtrarHistorial);
    });
    filtrarHistorial();

    document.querySelectorAll('[data-detalle]').forEach((boton) => {
        boton.addEventListener('click', () => {
            document.getElementById('tituloDetalleModal').textContent = `Corte ${boton.dataset.folio}`;
            document.getElementById('detallePeriodo').textContent = boton.dataset.periodo;
            document.getElementById('detalleCajero').textContent = boton.dataset.cajero;
            document.getElementById('detalleTotal').textContent = boton.dataset.total;
            document.getElementById('detalleDiferencia').textContent = boton.dataset.diferencia;
            document.getElementById('detalleEstado').textContent = boton.dataset.estado;
            abrirDialog(dialogDetalle);
        });
    });

    document.querySelectorAll('[data-aviso]').forEach((boton) => {
        boton.addEventListener('click', () => mostrarAviso(boton.dataset.aviso));
    });

    const periodoEstadisticas = document.getElementById('periodoEstadisticas');
    periodoEstadisticas.addEventListener('change', () => {
        const etiquetas = {
            semana: 'Esta semana',
            mes: 'Este mes',
            trimestre: 'Últimos 3 meses'
        };
        document.querySelector('.corte-eje span:last-child').textContent =
            `Datos de demostración · ${etiquetas[periodoEstadisticas.value]}`;
    });

    recalcularCorte();
})();
