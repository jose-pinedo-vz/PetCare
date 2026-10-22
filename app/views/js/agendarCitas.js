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

            console.log(datos);
            console.log("Si es citas agendar")

        });
    }
});
