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

        input.addEventListener('focus', filtrarOpciones);
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

    especie.addEventListener('change', () => filtrarRazas(true));
    filtrarRazas(false);
});