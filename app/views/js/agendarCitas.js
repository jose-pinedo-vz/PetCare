document.addEventListener("DOMContentLoaded", async function()
{
    const fecha = document.getElementById('fecha');

    if (fecha)
    {
        fecha.addEventListener('input', (evento) => 
        {
            // extraccion de fecha
            evento.target.value.toLowerCase().trim();
            const seleccionFecha = evento.target.value;

        });
    }
});
