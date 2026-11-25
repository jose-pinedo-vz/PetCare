document.addEventListener('DOMContentLoaded', () => {
    const buscadores = [
        {
            inputId: 'cliente_busqueda',
            listaId: 'lista_clientes_mascota',
            ocultoId: 'id_cliente'
        },
        {
            inputId: 'veterinario_busqueda',
            listaId: 'lista_veterinarios_mascota',
            ocultoId: 'id_veterinario'
        }
    ];

    // Conecta las búsquedas con sus campos ocultos, que son los que envían los IDs.
    buscadores.forEach(({ inputId, listaId, ocultoId }) => {
        const input = document.getElementById(inputId);
        const lista = document.getElementById(listaId);
        const campoId = document.getElementById(ocultoId);

        if (!input || !lista || !campoId) return;

        lista.style.display = 'none';

        const opciones = Array.from(lista.querySelectorAll('li'));

        function filtrarOpciones() {
            const busqueda = input.value.trim().toLocaleLowerCase('es');
            let coincidencias = 0;

            opciones.forEach((opcion) => {
                const coincide = opcion.textContent.toLocaleLowerCase('es').includes(busqueda);
                opcion.hidden = !coincide;
                if (coincide) coincidencias += 1;
            });

            lista.style.display = coincidencias > 0 ? 'block' : 'none';
        }

        input.addEventListener('focus', () => {
            if (campoId.value !== '' && input.value.trim() !== '') {
                lista.style.display = 'none';
                return;
            }
            filtrarOpciones();
        });

        input.addEventListener('input', () => {
            campoId.value = '';
            filtrarOpciones();
        });

        opciones.forEach((opcion) => {
            opcion.addEventListener('mousedown', (evento) => evento.preventDefault());
            opcion.addEventListener('click', () => {
                input.value = opcion.textContent.trim();
                campoId.value = opcion.dataset.id;
                lista.style.display = 'none';
            });
        });

        document.addEventListener('click', (evento) => {
            if (!input.parentElement.contains(evento.target)) {
                lista.style.display = 'none';
            }
        });

        input.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape') {
                lista.style.display = 'none';
            }
        });
    });

    const especie = document.getElementById('especie');
    const raza = document.getElementById('raza');

    if (!especie || !raza) return;

    // Muestra solo las razas relacionadas con el ID de la especie seleccionada.
    function filtrarRazas(limpiarSeleccion) {
        const especieSeleccionada = especie.options[especie.selectedIndex];
        const especieId = especieSeleccionada ? especieSeleccionada.dataset.especieId : '';
        const razaActual = raza.value;
        let razaActualValida = false;

        Array.from(raza.options).forEach((opcion) => {
            if (!opcion.value) {
                opcion.hidden = false;
                opcion.disabled = false;
                return;
            }

            const perteneceEspecie = especieId !== '' && opcion.dataset.especieId === especieId;
            opcion.hidden = !perteneceEspecie;
            opcion.disabled = !perteneceEspecie;

            if (perteneceEspecie && opcion.value === razaActual) {
                razaActualValida = true;
            }
        });

        raza.disabled = especieId === '';
        if (limpiarSeleccion || !razaActualValida) {
            raza.value = '';
        }
    }

    const contenedorVacunas = document.getElementById('contenedorVacunas');
    const notaVacunas = document.getElementById('notaVacunas');

    // La fecha solo aparece (y se envia) si la casilla de esa vacuna esta marcada.
    function actualizarFechaVacuna(casilla, fecha) {
        fecha.disabled = !casilla.checked;
        fecha.classList.toggle('oculto', !casilla.checked);
        if (!casilla.checked) fecha.value = '';
    }

    // Muestra solo las vacunas relacionadas con el ID de la especie seleccionada.
    function filtrarVacunas() {
        if (!contenedorVacunas) return;

        const especieSeleccionada = especie.options[especie.selectedIndex];
        const especieId = especieSeleccionada ? especieSeleccionada.dataset.especieId : '';
        let hayVacunas = false;

        contenedorVacunas.querySelectorAll('label[data-especie-id]').forEach((etiqueta) => {
            const casilla = etiqueta.querySelector('input[type="checkbox"]');
            const fecha = etiqueta.querySelector('input[type="date"]');
            const perteneceEspecie = especieId !== '' && etiqueta.dataset.especieId === especieId;

            etiqueta.classList.toggle('oculto', !perteneceEspecie);
            casilla.disabled = !perteneceEspecie;
            if (!perteneceEspecie) casilla.checked = false;
            if (perteneceEspecie) hayVacunas = true;

            actualizarFechaVacuna(casilla, fecha);
            casilla.onchange = () => actualizarFechaVacuna(casilla, fecha);
        });

        if (notaVacunas) {
            notaVacunas.textContent = especieId === ''
                ? 'Selecciona primero una especie...'
                : (hayVacunas ? '' : 'No hay vacunas registradas para esta especie.');
        }
    }

    especie.addEventListener('change', () => {
        filtrarRazas(true)
        filtrarVacunas();
    });
    filtrarRazas(false);
    filtrarVacunas();
});
