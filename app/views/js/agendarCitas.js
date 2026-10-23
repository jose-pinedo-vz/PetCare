document.addEventListener("DOMContentLoaded", async function()
{
    const fecha = document.getElementById('fecha');

    if (fecha)
    {
        fecha.addEventListener('input', async (evento) => 
        {
            // extraccion de fecha
            evento.target.value.toLowerCase().trim();
            const seleccionFecha = evento.target.value;

            const respuesta = await fetch(`../controllers/citas.php?accion=orario&fecha=${seleccionFecha}`, {
                method: "GET"
            });
            const datos = await respuesta.json();

            // insercino de datos

            const selectHorarios = document.getElementById("orararioConsulta");
            selectHorarios.innerHTML = ""; 

            for (const horarioTexto of datos)
            {
                const option = document.createElement("option");
                option.value = horarioTexto;      
                option.textContent = horarioTexto; 
                selectHorarios.appendChild(option);
            }

        });
    }
});
