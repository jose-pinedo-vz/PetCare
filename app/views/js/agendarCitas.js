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

            const respuesta = await fetch('../controllers/inicio.php?inicio=inicio', {method: "POST", headers: {'Content-Type': 'application/json'},
                body:JASON.stringify()});
            const datos = await respuesta.json();

            console.log(datos);

        });
    }
});
