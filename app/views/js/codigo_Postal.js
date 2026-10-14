async function codigoPostal(evt)
{
    const postal = document.getElementById("codigo_postal");
    console.log("codigoPostal");

    const respuesta = await fetch('../controllers/guardar_cliente.php?accion=codigo_postal', {method: "POST", headers: {'Content-Type': 'application/json'}, 
        body: JSON.stringify({codigo_postal: postal.value })});

    const datos = await respuesta.json();
    // console.log(datos);

    if (datos[0].ciudad === "" || datos[0].ciudad === null)
    {
        // console.log("ciudad", datos[0].municipio);
        document.getElementById("ciudad").value = datos[0].municipio;
    }
    else
    {
        // console.log("ciudad", datos[0].ciudad);
        document.getElementById("ciudad").value = datos[0].ciudad;
    }

    // console.log("Estado", datos[0].estado);
    document.getElementById("estado").value = datos[0].estado;
        
    const colonia = document.getElementById("colonia");
    colonia.innerHTML = "";
    // nesesito recorrer con un for colonia lo demas queda igual
    for (const item of datos)
    {
        const option = document.createElement("option");
        option.value = item.colonia;
        option.textContent = item.colonia;
        colonia.appendChild(option);

        //console.log("colonia", item.colonia);
    }

    
}