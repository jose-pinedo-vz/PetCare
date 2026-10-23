async function codigoPostal(evt)
{
    const postal = document.getElementById("codigo_postal");
    // console.log("codigoPostal");
    const respuesta = await fetch('../controllers/guardar_cliente.php?accion=codigo_postal', {method: "POST", headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({codigo_postal: postal.value })});
    const datos = await respuesta.json();

    try
    {
        if (datos[0].ciudad === "" || datos[0].ciudad === null)
        {
            document.getElementById("ciudad").value = datos[0].municipio;
        }
        else
        {
            document.getElementById("ciudad").value = datos[0].ciudad;
        }

        document.getElementById("estado").value = datos[0].estado;
        const colonia = document.getElementById("colonia");
        colonia.innerHTML = "";

        for (const item of datos)
        {
            const option = document.createElement("option");
            option.value = item.colonia;
            option.textContent = item.colonia;
            colonia.appendChild(option);
        }
    }
    catch (error)
    {
        alert("Ingrese un código postal válido");
    }
}

async function codigoPostalEmpleados(evt)
{
    const postal = document.getElementById("codigo_postal");
    console.log("codigoPostal");

    const respuesta = await fetch('../controllers/guardar_empleado.php?accion=codigo_postal', {method: "POST", headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({codigo_postal: postal.value })});
    const datos = await respuesta.json();

    try
    {
        if (datos[0].ciudad === "" || datos[0].ciudad === null)
        {
            document.getElementById("ciudad").value = datos[0].municipio;
        }
        else
        {
            document.getElementById("ciudad").value = datos[0].ciudad;
        }

        document.getElementById("estado").value = datos[0].estado;
        const colonia = document.getElementById("colonia");
        colonia.innerHTML = "";

        for (const item of datos)
        {
            const option = document.createElement("option");
            option.value = item.colonia;
            option.textContent = item.colonia;
            colonia.appendChild(option);
        }
    }
    catch (error)
    {
        alert("Ingrese un código postal válido");
    }
}

async function codigoPostalProveedores(evt)
{
    const postal = document.getElementById("codigo_postal");
    console.log("codigoPostal");

    const respuesta = await fetch('../controllers/proveedor.php?accion=codigo_postal', {method: "POST", headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({codigo_postal: postal.value })});
    const datos = await respuesta.json();


    try
    {
        if (datos[0].ciudad === "" || datos[0].ciudad === null)
        {
            document.getElementById("ciudad").value = datos[0].municipio;
        }
        else
        {
            document.getElementById("ciudad").value = datos[0].ciudad;
        }

        document.getElementById("estado").value = datos[0].estado;
    }
    catch (error)
    {
        alert("Ingrese un código postal válido");
    }
}