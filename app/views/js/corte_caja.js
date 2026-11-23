(() => {
    const tabs = Array.from(document.querySelectorAll('.corte-tab[role="tab"]'));
    const panels = Array.from(document.querySelectorAll('.corte-panel[role="tabpanel"]'));
    const dialogArqueo = document.getElementById('modalArqueo');
    const dialogMovimiento = document.getElementById('modalSalida');

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

    document.querySelectorAll('[data-grafica-vista]').forEach((boton) => {
        boton.addEventListener('click', () => {
            const grupo = boton.closest('[data-grafica-grupo]');
            if (!grupo) return;

            const vista = boton.dataset.graficaVista;
            grupo.querySelectorAll('[data-grafica-vista]').forEach((opcion) => {
                const activa = opcion === boton;
                opcion.classList.toggle('activa', activa);
                opcion.setAttribute('aria-pressed', String(activa));
            });
            grupo.querySelectorAll('[data-grafica-panel]').forEach((panel) => {
                panel.hidden = panel.dataset.graficaPanel !== vista;
            });
        });
    });

    document.getElementById('abrirArqueo')?.addEventListener('click', () => {
        if (dialogArqueo && !dialogArqueo.open) dialogArqueo.showModal();
    });
    document.getElementById('abrirSalida')?.addEventListener('click', () => {
        if (dialogMovimiento && !dialogMovimiento.open) dialogMovimiento.showModal();
    });

    document.querySelectorAll('[data-cerrar-dialog]').forEach((boton) => {
        boton.addEventListener('click', () => boton.closest('dialog').close());
    });
    document.querySelectorAll('dialog.corte-dialog').forEach((dialogo) => {
        dialogo.addEventListener('click', (evento) => {
            if (evento.target === dialogo) dialogo.close();
        });
    });

    if (dialogArqueo) {
        const formArqueo = document.getElementById('formArqueo');
        const totalContado = document.getElementById('efectivoContado');
        const errorArqueo = document.getElementById('errorArqueo');
        const camposCantidad = Array.from(dialogArqueo.querySelectorAll('[data-cantidad]'));

        const botonesModo = Array.from(dialogArqueo.querySelectorAll('[data-modo]'));
        const panelDenominacion = document.getElementById('panelDenominacion');
        const panelExacto = document.getElementById('panelExacto');
        const campoExacto = document.getElementById('montoExacto');
        const formatoMoneda = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' });
        let modo = 'denominacion';

        // Todo se calcula en centavos enteros para evitar errores de decimales
        function aCentavos(valor) {
            return Math.round(Number(valor) * 100);
        }

        function centavosExactos() {
            const texto = campoExacto.value.trim();
            if (texto === '' || !/^\d+(\.\d{1,2})?$/.test(texto)) return null;
            return aCentavos(texto);
        }

        function actualizarTotalContado() {
            let centavos = 0;
            if (modo === 'exacto') {
                const exacto = centavosExactos();
                centavos = exacto === null ? 0 : exacto;
            } else {
                camposCantidad.forEach((campo) => {
                    const cantidad = Number(campo.value);
                    const subtotal = Number.isInteger(cantidad) && cantidad >= 0 ? cantidad * aCentavos(campo.dataset.valor) : 0;
                    centavos += subtotal;
                    campo.closest('tr').querySelector('[data-subtotal]').textContent = formatoMoneda.format(subtotal / 100);
                });
            }
            document.getElementById('totalArqueo').textContent = formatoMoneda.format(centavos / 100);
            totalContado.value = (centavos / 100).toFixed(2);
            return centavos;
        }

        function cambiarModo(nuevoModo) {
            modo = nuevoModo;
            botonesModo.forEach((boton) => {
                const activo = boton.dataset.modo === modo;
                boton.classList.toggle('activa', activo);
                boton.setAttribute('aria-selected', activo ? 'true' : 'false');
                boton.tabIndex = activo ? 0 : -1;
            });
            panelDenominacion.hidden = modo !== 'denominacion';
            panelExacto.hidden = modo !== 'exacto';
            errorArqueo.hidden = true;
            actualizarTotalContado();
        }

        botonesModo.forEach((boton) => boton.addEventListener('click', () => cambiarModo(boton.dataset.modo)));
        camposCantidad.forEach((campo) => campo.addEventListener('input', actualizarTotalContado));
        campoExacto.addEventListener('input', () => {
            errorArqueo.hidden = true;
            actualizarTotalContado();
        });
        formArqueo.addEventListener('submit', (evento) => {
            let valido;
            if (modo === 'exacto') {
                valido = centavosExactos() !== null;
            } else {
                valido = camposCantidad.every((campo) => {
                    const valor = Number(campo.value);
                    return Number.isInteger(valor) && valor >= 0;
                });
            }
            if (!valido) {
                evento.preventDefault();
                errorArqueo.hidden = false;
                return;
            }
            actualizarTotalContado();
        });
    }

    const tipoMovimiento = document.getElementById('tipoMovimiento');
    const campoBeneficiario = document.getElementById('campoBeneficiario');
    const beneficiario = document.getElementById('empleadoBeneficiario');
    if (tipoMovimiento && campoBeneficiario && beneficiario) {
        const actualizarBeneficiario = () => {
            const esNomina = tipoMovimiento.value === 'nomina';
            campoBeneficiario.hidden = !esNomina;
            beneficiario.required = esNomina;
            if (!esNomina) beneficiario.value = '';
        };
        tipoMovimiento.addEventListener('change', actualizarBeneficiario);
        actualizarBeneficiario();
    }

    const filasHistorial = Array.from(document.querySelectorAll('#tablaHistorial tr[data-search]'));
    const buscarHistorial = document.getElementById('buscarHistorial');
    const resultadoHistorial = document.getElementById('resultadoHistorial');
    if (buscarHistorial && resultadoHistorial) {
        function filtrarHistorial() {
            const busqueda = buscarHistorial.value.trim().toLocaleLowerCase('es-MX');
            let visibles = 0;
            filasHistorial.forEach((fila) => {
                fila.hidden = busqueda !== '' && !fila.dataset.search.includes(busqueda);
                if (!fila.hidden) visibles += 1;
            });
            resultadoHistorial.textContent = `${visibles} corte(s) visibles en el periodo.`;
        }
        buscarHistorial.addEventListener('input', filtrarHistorial);
    }

    document.querySelectorAll('[data-imprimir]').forEach((boton) => {
        boton.addEventListener('click', () => window.print());
    });

    const dialogoDetalle = document.getElementById('modalDetalleCorte');
    if (dialogoDetalle?.dataset.autoOpen !== undefined) dialogoDetalle.showModal();
})();
