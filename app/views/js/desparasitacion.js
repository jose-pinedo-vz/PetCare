document.addEventListener("DOMContentLoaded", async function ()
{
    const producto = document.getElementById('ProductoUtilizado');
    if (!producto) return;

    try
    {
        const respuesta = await fetch('../controllers/servicioDesparacitacion.php?accion=mostrarProductos');
        const datos = await respuesta.json();

        for (const item of datos)
        {
            const option = document.createElement('option');
            option.value = item.id_producto;
            option.textContent = item.nombre;
            producto.appendChild(option);
        }
    }
    catch (error)
    {
        producto.innerHTML = '<option value="">Error al cargar los productos</option>';
    }
});