document.addEventListener("DOMContentLoaded", async function()
{
    const fecha = document.getElementById('fecha');
    const responsable = document.getElementById('responsable');

    if (fecha && responsable)
    {
        const actualizarHorarios = async () =>
        {
            const fechaVal = fecha.value.trim();
            const respVal = responsable.value.trim();


            const selectHorarios = document.getElementById("horarioConsulta") || document.getElementById("orararioConsulta");
            if (!selectHorarios) return;

            if (!fechaVal || !respVal) {
                selectHorarios.innerHTML = '<option value="">Seleccione una fecha primero</option>';
                return;
            }

            selectHorarios.innerHTML = '<option value="">Cargando horarios...</option>';

            try {
                const respuesta = await fetch(`../controllers/citas.php?accion=horario&fecha=${encodeURIComponent(fechaVal)}&responsable=${encodeURIComponent(respVal)}`, {
                    method: "GET"
                });
                const datos = await respuesta.json();

                selectHorarios.innerHTML = "";

                if (Array.isArray(datos) && datos.length > 0)
                {
                    const optionDefault = document.createElement("option");
                    optionDefault.value = "";
                    optionDefault.textContent = "Seleccione un horario";
                    selectHorarios.appendChild(optionDefault);

                    for (const horarioTexto of datos)
                    {
                        const option = document.createElement("option");
                        option.value = horarioTexto;
                        option.textContent = horarioTexto;
                        selectHorarios.appendChild(option);
                    }
                }
                else
                {
                    const option = document.createElement("option");
                    option.value = "";
                    option.textContent = "No hay horarios disponibles para esta fecha";
                    selectHorarios.appendChild(option);
                }
            } catch (error)
            {
                selectHorarios.innerHTML = '<option value="">Error al cargar los horarios</option>';
            }
        };

        fecha.addEventListener('input', actualizarHorarios);
        fecha.addEventListener('change', actualizarHorarios);

        responsable.addEventListener('input', actualizarHorarios);
        responsable.addEventListener('change', actualizarHorarios);
    }
});

// const cliente = document.getElementById('claveCliente');
// console.log("¿Qué encontró el DOM?:", cliente);



// mostrar las mascotas de cada cliente en el select de mascotas
document.addEventListener("DOMContentLoaded", async function()
{
    const cliente = document.getElementById('claveCliente');
    //console.log("¿Qué encontró el DOM?:", cliente);

    if (cliente)
    {

        const elegir_mascota = async (mostrarMascotas) =>
        {
            //console.log("se ejecuta");
            const mascota = document.getElementById("mascota");

            if (!mostrarMascotas) {
                mascota.innerHTML = '<option value="">Seleccione un cliente primero</option>';
                return;
            }

            let datos = [];

            try
            {
                const respuesta = await fetch(`../controllers/citas.php?accion=mostrarMascota&id_cliente=${encodeURIComponent(mostrarMascotas)}`, {
                    method: "GET"
                });
                datos = await respuesta.json();

                console.log(datos);
            }
            catch (error)
            {
                // console.log(error)
                mascota.innerHTML = '<option value="">Error al cargar las mascotas</option>';
            }

            mascota.innerHTML = "";

            if (datos.length === 0)
            {
                mascota.innerHTML = '<option value="">El usuario no tiene mascotas registradas</option>';
            }
            else
            {
                for (const item of datos)
                {
                    const option = document.createElement("option");
                    option.value = item.id_mascota;
                    option.textContent = item.nombre;
                    mascota.appendChild(option);
                }
            }
        }
        cliente.addEventListener('input', (event) =>
        {

            elegir_mascota(event.target.value.trim());
        });
    }
});



// mostrar todos los servicios en el select de servicios
document.addEventListener("DOMContentLoaded", async function()
{
    //console.log("se ejecuta");
    const servicio = document.getElementById('servicio');

    if (servicio)
    {
        const mas_servicios = async (masServicios) =>
        {
            let datos = [];

            try
            {
                const respuesta = await fetch('../controllers/citas.php?accion=mostrarServicio',
                    {method: "GET"
                });

                datos = await respuesta.json();

                for (const item of datos)
                {
                    const option = document.createElement("option");
                    option.value = item.id_servicio;
                    option.textContent = item.nombre;
                    servicio.appendChild(option);
                }
            }
            catch (error)
            {

            }
        }
        mas_servicios();
    }
});


document.addEventListener("DOMContentLoaded", async function()
{
    const servicio_listo = document.getElementById('servicio');
    const responsable = document.getElementById('responsable');

    if (servicio_listo)
    {
        const responsables = async (evet) =>
        {
            let datos = [];
            try
            {
                responsable.innerHTML = '<option value="">Seleccione un responsable</option>';
                const respuesta = await fetch(`../controllers/citas.php?accion=mostrarResponsable&servicio=${encodeURIComponent(evet)}`, {
                    method: "GET"
                });
                datos = await respuesta.json();
                console.log(datos);


                for (const item of datos)
                {
                    const option = document.createElement('option');
                    option.value = item.id_empleado;
                    option.textContent = item.nombre;
                    responsable.appendChild(option);
                }

            }
            catch (error)
            {
                responsable.innerHTML = '<option value="">Error al cargar los responsables</option>';
            }
        }

        servicio_listo.addEventListener('change', (event) =>
        {
            responsables(event.target.value.trim());
        });


    }
});






// reprogramar horarios 


document.addEventListener("DOMContentLoaded", async function()
{
    const fecha = document.getElementById('reprogramar_fecha');
    const responsable = document.getElementById('responsable_reprogramar');

    //responsable_reprogramar
    

    if (fecha && responsable)
    {
        const actualizarHorarios = async () =>
        {
            const fechaVal = fecha.value.trim();
            const respVal = responsable.value.trim();


            const selectHorarios = document.getElementById("reprogramarHorario");
            if (!selectHorarios) return;

            if (!fechaVal) {
                selectHorarios.innerHTML = '<option value="">Seleccione una fecha primero</option>';
                return;
            }

            selectHorarios.innerHTML = '<option value="">Cargando horarios...</option>';

            try {
                const respuesta = await fetch(`../controllers/citas.php?accion=horario&fecha=${encodeURIComponent(fechaVal)}&responsable=${encodeURIComponent(respVal)}`, {
                    method: "GET"
                });
                const datos = await respuesta.json();

                selectHorarios.innerHTML = "";

                if (Array.isArray(datos) && datos.length > 0)
                {
                    const optionDefault = document.createElement("option");
                    optionDefault.value = "";
                    optionDefault.textContent = "Seleccione un horario";
                    selectHorarios.appendChild(optionDefault);

                    for (const horarioTexto of datos)
                    {
                        const option = document.createElement("option");
                        option.value = horarioTexto;
                        option.textContent = horarioTexto;
                        selectHorarios.appendChild(option);
                    }
                }
                else
                {
                    const option = document.createElement("option");
                    option.value = "";
                    option.textContent = "No hay horarios disponibles para esta fecha";
                    selectHorarios.appendChild(option);
                }
            } catch (error)
            {
                selectHorarios.innerHTML = '<option value="">Error al cargar los horarios</option>';
            }
        };

        fecha.addEventListener('input', actualizarHorarios);
        fecha.addEventListener('change', actualizarHorarios);

        // responsable.addEventListener('input', actualizarHorarios);
        // responsable.addEventListener('change', actualizarHorarios);
    }
});