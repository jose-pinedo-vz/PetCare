document.addEventListener('DOMContentLoaded', () => {
    const buscadores = [
        {
            inputId: 'id_cliente',
            listaId: 'lista_clientes',
            ocultoId: 'cliente_id_oculto'
        },
        {
            inputId: 'id_mascota',
            listaId: 'lista_mascotas',
            ocultoId: 'mascota_id_oculto'
        },
        {
            inputId: 'id_jaula',
            listaId: 'lista_jaulas',
            ocultoId: 'jaula_id_oculto'
        }
    ];

    // Guarda los campos y las opciones de cada buscador para reutilizarlos
    const controles = buscadores.map((buscador) => ({
        input: document.getElementById(buscador.inputId),
        lista: document.getElementById(buscador.listaId),
        oculto: document.getElementById(buscador.ocultoId)
    })).filter((control) => control.input && control.lista && control.oculto);

    const idClienteOculto = document.getElementById('cliente_id_oculto');
    const inputMascota = document.getElementById('id_mascota');
    const idMascotaOculto = document.getElementById('mascota_id_oculto');
    const listaMascotas = document.getElementById('lista_mascotas');
    const fechaEntrada = document.getElementById('fecha_entrada');
    const fechaSalida = document.getElementById('fecha_salida');
    const noches = document.getElementById('noches');
    const precioNoche = document.getElementById('precio_noche');
    const saldo = document.getElementById('saldo');
    const tablaVacunas = document.getElementById('tabla_vacunas_body');
    const estadoVacunas = document.getElementById('estado_validacion_vacunas');
    let solicitudVacunas = 0;

    // Convierte una fecha de calendario a UTC para no afectar el cálculo por cambios de horario.
    function obtenerFechaUTC(valorFecha) {
        if (!valorFecha) {
            return null;
        }

        const [anio, mes, dia] = valorFecha.split('-').map(Number);
        const fecha = new Date(Date.UTC(anio, mes - 1, dia));

        if (
            fecha.getUTCFullYear() !== anio ||
            fecha.getUTCMonth() !== mes - 1 ||
            fecha.getUTCDate() !== dia
        ) {
            return null;
        }

        return fecha.getTime();
    }

    // Calcula las noches entre entrada y salida y multiplica por el precio nocturno.
    function calcularEstancia() {
        if (!fechaEntrada || !fechaSalida || !noches || !precioNoche || !saldo) {
            return;
        }

        const entrada = obtenerFechaUTC(fechaEntrada.value);
        const salida = obtenerFechaUTC(fechaSalida.value);

        if (entrada === null || salida === null || salida <= entrada) {
            noches.value = '';
            saldo.value = '';
            return;
        }

        const cantidadNoches = (salida - entrada) / 86400000;
        const precio = Number(precioNoche.value);

        noches.value = String(cantidadNoches);
        saldo.value = precioNoche.value !== '' && Number.isFinite(precio)
            ? (cantidadNoches * precio).toFixed(2)
            : '';
    }

    // Recalcula cuando cambia cualquiera de las fechas o el precio por noche.
    [fechaEntrada, fechaSalida, precioNoche].forEach((campo) => {
        if (campo) {
            campo.addEventListener('input', calcularEstancia);
            campo.addEventListener('change', calcularEstancia);
        }
    });
    calcularEstancia();

    // Muestra cada problema en su propio elemento de lista
    function mostrarErroresVacunas(errores) {
        if (!estadoVacunas) {
            return;
        }

        while (estadoVacunas.firstChild) {
            estadoVacunas.removeChild(estadoVacunas.firstChild);
        }

        const listaErrores = document.createElement('ul');
        errores.forEach((error) => {
            const elemento = document.createElement('li');
            elemento.textContent = error;
            listaErrores.appendChild(elemento);
        });

        estadoVacunas.className = 'msg-error';
        estadoVacunas.appendChild(listaErrores);
    }

    // Vacía la tabla o muestra una indicación mientras faltan datos
    function limpiarTablaVacunas(mensaje) {
        if (!tablaVacunas) {
            return;
        }

        while (tablaVacunas.firstChild) {
            tablaVacunas.removeChild(tablaVacunas.firstChild);
        }

        const fila = document.createElement('tr');
        const celda = document.createElement('td');
        celda.colSpan = 9;
        celda.textContent = mensaje;
        fila.appendChild(celda);
        tablaVacunas.appendChild(fila);
    }

    // Presenta cada campo de la tabla vacunas sin interpretar contenido como HTML
    function mostrarVacunas(vacunas) {
        if (!tablaVacunas) {
            return;
        }

        while (tablaVacunas.firstChild) {
            tablaVacunas.removeChild(tablaVacunas.firstChild);
        }

        if (!vacunas.length) {
            limpiarTablaVacunas('No hay vacunas configuradas para la especie de esta mascota.');
            return;
        }

        const campos = [
            'tipo_vacunas',
            'fecha_aplicacion',
            'fecha_proxima',
            'veterinario',
            'lote',
            'fabricante',
            'observaciones'
        ];

        vacunas.forEach((vacuna) => {
            const fila = document.createElement('tr');

            campos.forEach((campo) => {
                const celda = document.createElement('td');
                celda.textContent = campo === 'veterinario'
                    ? (vacuna.veterinario
                        ? `${vacuna.veterinario}${vacuna.veterinario_nombre ? ` - ${vacuna.veterinario_nombre}` : ''}`
                        : '-')
                    : (vacuna[campo] || '-');
                fila.appendChild(celda);
            });

            const celdaRequerida = document.createElement('td');
            celdaRequerida.textContent = vacuna.requerida ? 'Sí' : 'No';
            fila.appendChild(celdaRequerida);

            const celdaEstado = document.createElement('td');
            const indicador = document.createElement('span');
            indicador.className = vacuna.requerida
                ? (vacuna.valida ? 'historial-estado pagada' : 'historial-estado cancelada')
                : 'historial-estado pendiente';
            indicador.textContent = vacuna.requerida
                ? vacuna.estado_validacion
                : 'Registro informativo';
            celdaEstado.appendChild(indicador);
            fila.appendChild(celdaEstado);
            tablaVacunas.appendChild(fila);
        });
    }

    // Consulta las vacunas de la especie y calcula vigencia para salida o para hoy.
    async function validarVacunasSeleccionadas() {
        const numeroSolicitud = ++solicitudVacunas;
        const idMascota = idMascotaOculto ? idMascotaOculto.value : '';
        const hoy = new Date();
        const salida = fechaSalida && fechaSalida.value
            ? fechaSalida.value
            : `${hoy.getFullYear()}-${String(hoy.getMonth() + 1).padStart(2, '0')}-${String(hoy.getDate()).padStart(2, '0')}`;

        if (!idMascota) {
            limpiarTablaVacunas('Selecciona una mascota para consultar sus vacunas.');
            if (estadoVacunas) {
                estadoVacunas.className = 'nota-campo';
                estadoVacunas.textContent = 'Selecciona una mascota para consultar sus vacunas.';
            }
            return;
        }

        limpiarTablaVacunas('Consultando historial de vacunas...');
        if (estadoVacunas) {
            estadoVacunas.className = 'nota-campo';
            estadoVacunas.textContent = 'Consultando el historial de vacunas...';
        }

        try {
            const respuesta = await fetch('obtener_vacunas_hotel.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    id_mascota: idMascota,
                    fecha_salida: salida
                })
            });
            const resultado = await respuesta.json();

            // Ignora una respuesta anterior si el usuario cambió mascota o fecha
            if (numeroSolicitud !== solicitudVacunas) {
                return;
            }

            if (!respuesta.ok || !resultado.ok) {
                limpiarTablaVacunas('No se pudo completar la consulta.');
                mostrarErroresVacunas(resultado.errores && resultado.errores.length
                    ? resultado.errores
                    : ['No se pudo consultar el historial de vacunas.']);
                return;
            }

            mostrarVacunas(resultado.vacunas || []);
            if (estadoVacunas) {
                if (!resultado.vacunas || resultado.vacunas.length === 0) {
                    estadoVacunas.className = 'nota-campo';
                    estadoVacunas.textContent = 'No hay vacunas configuradas para la especie de esta mascota.';
                } else if (resultado.valida) {
                    estadoVacunas.className = 'historial-estado pagada';
                    estadoVacunas.textContent = fechaSalida && fechaSalida.value
                        ? 'Todas las vacunas requeridas están vigentes hasta la fecha de salida.'
                        : 'Las vacunas de esta especie están vigentes a la fecha de hoy.';
                } else {
                    mostrarErroresVacunas([
                        //'Hay vacunas pendientes o vencidas; el sistema permite guardar la reservación.',
                        ...(resultado.errores || []),
                        'Si quieres tener todas las vacunas vigentes o actualizadas, dirigete al veterinario para que las aplique y actualice el historial de vacunas.'
                    ]);
                }
            }
        } catch (error) {
            if (numeroSolicitud !== solicitudVacunas) {
                return;
            }

            limpiarTablaVacunas('No se pudo completar la consulta.');
            mostrarErroresVacunas([error.message || 'No se pudo consultar el historial de vacunas.']);
        }
    }

    // Muestra solo las mascotas que pertenecen al cliente seleccionado
    function filtrarMascotas(idCliente) {
        if (!listaMascotas) {
            return;
        }

        Array.from(listaMascotas.querySelectorAll('li')).forEach((mascota) => {
            mascota.style.display = idCliente && mascota.dataset.cliente === idCliente ? '' : 'none';
        });
    }

    // Al cambiar de cliente se borra la mascota anterior para evitar cruces
    function actualizarMascotasPorCliente() {
        if (inputMascota && idMascotaOculto) {
            inputMascota.value = '';
            idMascotaOculto.value = '';
            inputMascota.disabled = !idClienteOculto.value;
            inputMascota.placeholder = idClienteOculto.value
                ? 'Seleccionar mascota...'
                : 'Primero selecciona un cliente';
        }

        filtrarMascotas(idClienteOculto ? idClienteOculto.value : '');
        validarVacunasSeleccionadas();
    }

    controles.forEach(({ input, lista, oculto }) => {
        const opciones = Array.from(lista.querySelectorAll('li'));

        // Al enfocar, cierra las demás listas y abre la lista del campo activo
        input.addEventListener('focus', () => {
            document.querySelectorAll('.lista-resultados').forEach((otraLista) => {
                otraLista.style.display = 'none';
            });
            lista.style.display = 'block';
        });

        // Filtra las opciones conforme el usuario escribe y descarta IDs anteriores
        input.addEventListener('input', () => {
            oculto.value = '';
            lista.style.display = 'block';

            opciones.forEach((opcion) => {
                const textoOpcion = `${opcion.textContent} ${opcion.dataset.tamano || ''}`.toLowerCase();
                const coincideTexto = textoOpcion.includes(input.value.toLowerCase());
                const coincideCliente = lista.id !== 'lista_mascotas' ||
                    opcion.dataset.cliente === (idClienteOculto ? idClienteOculto.value : '');

                opcion.style.display = coincideTexto && coincideCliente ? '' : 'none';
            });

            if (lista.id === 'lista_clientes') {
                actualizarMascotasPorCliente();
            } else if (lista.id === 'lista_mascotas') {
                validarVacunasSeleccionadas();
            }
        });

        // Copia el texto elegido al campo y guarda su ID para enviarlo a PHP
        opciones.forEach((opcion) => {
            opcion.addEventListener('click', () => {
                input.value = opcion.textContent.trim();
                oculto.value = opcion.dataset.id;
                lista.style.display = 'none';

                if (lista.id === 'lista_clientes') {
                    actualizarMascotasPorCliente();
                } else if (lista.id === 'lista_mascotas') {
                    validarVacunasSeleccionadas();
                }
            });
        });
    });

    // Una nueva fecha de salida siempre invalida y vuelve a calcular la validación.
    if (fechaSalida) {
        fechaSalida.addEventListener('change', validarVacunasSeleccionadas);
    }

    // Aplica el filtro al cargar la página, también con datos restaurados por sesion
    filtrarMascotas(idClienteOculto ? idClienteOculto.value : '');
    validarVacunasSeleccionadas();

    // Oculta las listas cuando el usuario hace clic fuera de los buscadores
    document.addEventListener('click', (evento) => {
        if (!evento.target.closest('.contenedor-buscador')) {
            document.querySelectorAll('.lista-resultados').forEach((lista) => {
                lista.style.display = 'none';
            });
        }
    });
});