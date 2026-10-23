document.addEventListener("DOMContentLoaded", async function()
{
    const fecha = document.getElementById('fecha');

    if (fecha)
    {
        const actualizarHorarios = async (seleccionFecha) => 
        {
            const selectHorarios = document.getElementById("horarioConsulta") || document.getElementById("orararioConsulta");
            if (!selectHorarios) return;

            if (!seleccionFecha) {
                selectHorarios.innerHTML = '<option value="">Seleccione una fecha primero</option>';
                return;
            }

            selectHorarios.innerHTML = '<option value="">Cargando horarios...</option>';

            try {
                const respuesta = await fetch(`../controllers/citas.php?accion=horario&fecha=${encodeURIComponent(seleccionFecha)}`, {
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
            } catch (error) {
                selectHorarios.innerHTML = '<option value="">Error al cargar los horarios</option>';
            }
        };

        fecha.addEventListener('input', (evento) => {
            actualizarHorarios(evento.target.value.trim());
        });

        fecha.addEventListener('change', (evento) => {
            actualizarHorarios(evento.target.value.trim());
        });

        if (fecha.value) {
            actualizarHorarios(fecha.value.trim());
        }
    }
});
