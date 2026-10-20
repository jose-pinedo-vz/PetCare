function mostrarDivs(datos)
{
    const tarjetaAlertas = document.querySelector('#targeta');
    let hayAlertar = false;

    const stockBajo = Number(datos.numProductosStockBajo[0][0] ?? datos.numProductosStockBajo[0].productoStockBajo);
    if (stockBajo > 0)
    {
        hayAlertar = true;
        const divAlertaProductos = document.createElement('div');
        divAlertaProductos.classList.add('alerta-item');
        divAlertaProductos.textContent = stockBajo + " productos por debajo del stock mínimo.";
        tarjetaAlertas.appendChild(divAlertaProductos);
    }

    const citasPagos = Number(datos.numCitasPagos[0][0] ?? datos.numCitasPagos[0].numCitasPagos);
    if (citasPagos > 0)
    { 
        hayAlertar = true;
        const dibAlertaCitas = document.createElement('div');
        dibAlertaCitas.classList.add('alerta-item');
        dibAlertaCitas.textContent = citasPagos + " citas atendidas pendientes de pago.";
        tarjetaAlertas.appendChild(dibAlertaCitas);
    }

    if (hayAlertar === false)
    {
        const divSinAlerts = document.createElement('div');
        divSinAlerts.classList.add('alerta-item');
        divSinAlerts.textContent = "Sin alertas.";
        tarjetaAlertas.appendChild(divSinAlerts);
    }
}


document.addEventListener("DOMContentLoaded", async function() 
{
    console.log("Si se llama");
    const respuesta = await fetch('../controllers/inicio.php?inicio=inicio', {method: "POST", headers: {'Content-Type': 'application/json'},
       body: JSON.stringify()});
    const datos = await respuesta.json();

    console.log(datos);

    document.getElementById('citas-hoy').textContent = datos.citas[0][0] ?? datos.citas[0].citas;
    document.getElementById('citasPorCobrar').textContent = datos.numCitasPagos[0][0] ?? datos.numCitasPagos[0].numCitasPagos;
    document.getElementById('numMascotas').textContent = datos.numMascotas[0][0] ?? datos.numMascotas[0].numMascotas;
    document.getElementById('StockBajo').textContent = datos.numProductosStockBajo[0][0] ?? datos.numProductosStockBajo[0].productoStockBajo;

    const tbody = document.querySelector('#tablaCitas');
    tbody.innerHTML = '';

    // tabla de citas 
    let i = 0;
    for (const item of datos.citasHoy)
    {
        const tr = document.createElement('tr');
        const tdfecha = document.createElement('td');
        const tdDuenio = document.createElement('td');
        const tdMotivo = document.createElement('td');
        const tdAccion = document.createElement('td');
        const spanChep = document.createElement('span');

        tdfecha.textContent = item.fecha;
        tdDuenio.textContent = item.nombre;
        tdMotivo.textContent = item.motivo_de_consulta;
        spanChep.textContent = item.atendido;

        spanChep.classList.add('estado-chip');

        if (Number(item.atendido) == 1)
        {
            spanChep.textContent = "Atendido";
            spanChep.classList.add("estado-atendida");
        }
        else
        {
            spanChep.textContent = "Pendiente";
            spanChep.classList.add('estado-espera');
        }

        if (i % 2 === 0)
        {
            tr.classList.add('fila-destacada');
        }

        tdAccion.appendChild(spanChep);
        tr.appendChild(tdfecha);
        tr.appendChild(tdDuenio);
        tr.appendChild(tdMotivo);
        tr.appendChild(tdAccion); 
        tbody.appendChild(tr);
        i++;
    }


    mostrarDivs(datos);
});