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

        let timerActualizar = null;
        const debouncedActualizarHorarios = () => {
            clearTimeout(timerActualizar);
            timerActualizar = setTimeout(actualizarHorarios, 300);
        };

        fecha.addEventListener('input', debouncedActualizarHorarios);
        fecha.addEventListener('change', actualizarHorarios);

        responsable.addEventListener('input', debouncedActualizarHorarios);
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

            if (datos && datos.error === 'cliente_no_existe')
            {
                mascota.innerHTML = '<option value="">El cliente no se encuentra registrado</option>';
            }
            else if (datos.length === 0)
            {
                mascota.innerHTML = '<option value="">El cliente no tiene mascotas registradas</option>';
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

        if (cliente.value && cliente.value.trim() !== '')
        {
            elegir_mascota(cliente.value.trim());
        }
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



// mostrar responsable
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



// duracion del servicio
document.addEventListener("DOMContentLoaded", async function()
{
    const servicio_listo = document.getElementById('servicio');
    const duracion = document.getElementById('duracion');

    if (servicio_listo)
    {
        const consultarDuracion = async (evet) =>
        {
            let datos = [];
            try
            {
                duracion.innerHTML = '';
                const respuesta = await fetch(`../controllers/citas.php?accion=mostrarDuracion&servicio=${encodeURIComponent(evet)}`, {
                    method: "GET"
                });
                datos = await respuesta.json();
                console.log("duracino" + datos);
                duracion.value = datos.duracion || '';

            }
            catch (error)
            {
               duracion.value = '';
            }
        }

        servicio_listo.addEventListener('change', (event) =>
        {
            consultarDuracion(event.target.value.trim());
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

        let timerReprogramar = null;
        const debouncedReprogramar = () => {
            clearTimeout(timerReprogramar);
            timerReprogramar = setTimeout(actualizarHorarios, 300);
        };

        fecha.addEventListener('input', debouncedReprogramar);
        fecha.addEventListener('change', actualizarHorarios);

        // responsable.addEventListener('input', actualizarHorarios);
        // responsable.addEventListener('change', actualizarHorarios);
    }
});


async function datosServicio(idServicio)
{
    console.log("se mandan llamar los datos");
    const respuesta = await fetch(`../controllers/citas.php?accion=datos_Servicio&id_Servicio=${encodeURIComponent(idServicio)}`, {
        method: "GET"
    });
    const datos = await respuesta.json();
    console.log(datos);
    return datos;

}



// =============================================================
// elementos del modal

 // Elementos de la pagina
    const inputBuscar = document.getElementById('buscarProducto');
    const tbodyResultados = document.getElementById('resultadosProductos');
    const mensajeBusqueda = document.getElementById('mensajeBusqueda');
    const modalBuscar = document.getElementById('modalBuscarProducto');
    const btnBuscarProducto = document.getElementById('btnBuscarProducto');
    const tbodyCarrito = document.getElementById('cartTableBody');
    const mensajeVenta = document.getElementById('ventaMensaje');
    const inputCodigo = document.getElementById('barcodeInput');
    const inputCliente = document.getElementById('clienteInput');
    const lblItems = document.getElementById('lblItems');
    const lblSubtotal = document.getElementById('lblSubtotal');
    const lblIva = document.getElementById('lblIva');
    const lblTotal = document.getElementById('lblTotal');
    const btnNueva = document.getElementById('btnNuevaVenta');
    const btnCancelar = document.getElementById('btnCancelarVenta');
    const btnCobrar = document.getElementById('btnCobrar');

    // Elementos del buscador de servicios
    const inputBuscarServ = document.getElementById('buscarServicio');
    const tbodyServicios = document.getElementById('resultadosServicios');
    const mensajeBusquedaServ = document.getElementById('mensajeBusquedaServicio');
    const modalBuscarServ = document.getElementById('modalBuscarServicio');
    const btnAgregarServicio = document.getElementById('btnAgregarServicio');

    // Elementos del modal de pago
    const modalPago = document.getElementById('modalPagarVenta');
    const inputMonto = document.getElementById('montoRecibido');
    const btnConfirmar = modalPago.querySelector('.pago-confirmar');
    const tbodyPago = modalPago.querySelector('.pago-tabla tbody');
    const totalesPago = modalPago.querySelectorAll('.pago-totales strong');
    const totalPagar = modalPago.querySelector('.pago-calculo-monto > strong');
    const cambioPago = modalPago.querySelector('.pago-cambio > span');
    const metodos = modalPago.querySelectorAll('.pago-metodo');




// ================================================================================================================
// adelanto
// llamas modal 

document.addEventListener("DOMContentLoaded", () => {
    const modalPagarVenta = document.getElementById('modalPagarVenta');
    if (!modalPagarVenta) return;

    const metodosPago = [...document.querySelectorAll('[data-metodo-pago]')];
    const detallesTarjeta = document.getElementById('detallesTarjeta');
    const detallesTransferencia = document.getElementById('detallesTransferencia');
    const botonAnticipo = document.getElementById('btnAnticipo');

    function seleccionarMetodoPago(metodo) {
        metodosPago.forEach((boton) => {
            const sel = boton.dataset.metodoPago === metodo;
            boton.classList.toggle('seleccionado', sel);
            boton.setAttribute('aria-pressed', String(sel));
        });
        if (detallesTarjeta) detallesTarjeta.hidden = metodo !== 'Tarjeta';
        if (detallesTransferencia) detallesTransferencia.hidden = metodo !== 'Transferencia';
    }

    metodosPago.forEach((boton) => {
        boton.addEventListener('click', () => seleccionarMetodoPago(boton.dataset.metodoPago));
    });

    const idEmpleado = Number(document.getElementById('datosSesion')?.dataset.idEmpleado) || 0;

    const btnConfirmar = modalPagarVenta.querySelector('.pago-confirmar');
    const totalPagar = modalPagarVenta.querySelector('.pago-calculo-monto > strong');
    const inputMonto = document.getElementById('montoRecibido');
    const totalesPago = modalPagarVenta.querySelectorAll('.pago-totales strong');
    let totalIva = 0;


          // datos para mandar a servidor
    btnConfirmar?.addEventListener('click', async () => 
        {
            const metodoSeleccionado = document.querySelector('.pago-metodo.seleccionado')?.dataset.metodoPago;

            // montos elegidos 
            const total = Number(totalPagar.textContent.replace('$', '')) || 0;
            const MontoResivido = Number(inputMonto.value) || 0;

            const idCliente = Number(document.getElementById('claveCliente')?.value) || 0;
            const idEmpleado = Number(document.getElementById('datosSesion')?.dataset.idEmpleado) || 0;

            // estraer datos segun metodo seleccionardo

            let referencia = '';
            let numTarjeta = '';
            let titular = '';

            if (metodoSeleccionado === "Transferencia")
            {
                referencia = document.querySelector('#detallesTransferencia input')?.value.trim() || '';
                //referencia = document.querySelector('#detallesTransferencia input')?.value.text.trim() || '';
            }
            else if (metodoSeleccionado === "Tarjeta")
            { 
                var inputsTarjeta = document.querySelectorAll('#detallesTarjeta input');
                numTarjeta = inputsTarjeta[0]?.value.replace(/\s+/g, '') || '';
                var terminacion = numTarjeta.slice(-4); // Solo últimos 4 dígitos por seguridad
                titular = inputsTarjeta[1]?.value.trim() || '';
                referencia = `${titular} (****${terminacion})`;
            }

            const iva = Number(totalesPago[1].textContent.replace('$', '')) || 0;

            if (total === 0) {alert("Ingrese un total valido por favor."); return;}
            if (MontoResivido === 0) {alert("Ingrese un monto valido por favor."); return;}
            // if (iva === 0) {alert("Ingrese un monto valido por favor."); return;}
            if (MontoResivido < total) { alert("El monto recibido es menor al total."); return; }

            

            if (metodoSeleccionado === "Tarjeta")
            {
                //if (inputsTarjeta === "") {alert("Ingrese una tarjeta por favor."); return;}
                if (numTarjeta === "") {alert("Ingrese una tarjeta por favor."); return;}
                if (titular === "") {alert("Ingrese un titular valido por favor."); return;}
            }
            console.log("Listo para enviar:", {
                idEmpleado,
                idCliente,
                metodoSeleccionado,
                total,
                MontoResivido,
                iva,
                referencia
            });


            // mandar datos 

            const datosPago = new FormData();
            datosPago.append('id_cliente', idCliente);
            datosPago.append('id_empleado', idEmpleado);
            datosPago.append('metodo_pago', metodoSeleccionado ?? '');
            datosPago.append('total', total);
            datosPago.append('monto_recibido', MontoResivido);
            datosPago.append('iva', iva);
            datosPago.append('referencia', referencia);


            try
            {
                const respuesta = await fetch('../controllers/abonoCitas.php', {
                    method: 'POST',
                    body: datosPago
                });

                const data = await respuesta.json();
                console.log("respuerst:", data);

                if (data.ok) {
                    modalPagarVenta.close();
                } else {
                    alert(data.mensaje || "No se pudo registrar el anticipo.");
                }
            }
            catch (error)
            {
                console.log("hubo un error" + error);
                alert("Hubo un error.")
            }
        })

    // mostrar el cambio devuelro de el pago
    inputMonto?.addEventListener('input', () => {
        const recibido = Number(inputMonto.value) || 0;
        const total = Number(totalPagar?.textContent.replace('$', '')) || totalIva || 0;
        const cambio = Math.max(0, recibido - total);
        const cambioEl = document.getElementById('cambioPago');
        if (cambioEl) {
            cambioEl.innerHTML = `$${cambio.toFixed(2)} <small>MXN</small>`;
        }
    });

    botonAnticipo?.addEventListener('click', async () => {
        const idServicio = document.getElementById('servicio').value;

        if (idServicio === '') {
            alert('Selecciona un servicio primero.');
            return;
        }

        const respuestaServicio = await datosServicio(idServicio);
        const precio = Number(respuestaServicio?.precio ?? respuestaServicio);

        // precio ottal iva izqu
        totalIva = precio * 1.16; 
        var iva = precio * 0.16;
        const totalesPago = modalPagarVenta.querySelectorAll('.pago-totales strong');
        if (totalesPago.length >= 3) {
            totalesPago[0].textContent = '$' + precio.toFixed(2); // subotoal 1
            totalesPago[1].textContent = '$' + iva.toFixed(2);
            totalesPago[2].textContent = '$' + totalIva.toFixed(2);
        }

        // const totalPagar = modalPagarVenta.querySelector('.pago-calculo-monto > strong');
        // const inputMonto = document.getElementById('montoRecibido');

        if (totalPagar) totalPagar.textContent = '$' + totalIva.toFixed(2);
        if (inputMonto) inputMonto.value = totalIva.toFixed(2);

        // precio de inva en tabla 
        // elimina campos incesesarios para productos
        document.querySelectorAll('#tablaResumenPago thead th:nth-child(2), #tablaResumenPago thead th:nth-child(3)')
            .forEach(th => th.style.display = 'none');

        document.querySelectorAll('#tablaResumenPago tbody td:nth-child(2), #tablaResumenPago tbody td:nth-child(3)')
            .forEach(td => td.style.display = 'none');
        // ocultar el "Cód. 12345"
        const codigo = document.querySelector('#tablaResumenPago .pago-producto-detalle small');
        if (codigo) codigo.style.display = 'none';

        // datos
        const selectServicio = document.getElementById('servicio');
        const nombreServicio = selectServicio.options[selectServicio.selectedIndex].text;
        document.querySelector('#tablaResumenPago .pago-producto-detalle strong').textContent = nombreServicio;
        document.querySelector('#tablaResumenPago tbody td:nth-child(4)').textContent = '$' + precio;
        modalPagarVenta.showModal();
    });
    document.getElementById('cerrarModalPago')?.addEventListener('click', () => modalPagarVenta.close());
    document.getElementById('cancelarPago')?.addEventListener('click', () => modalPagarVenta.close());
    modalPagarVenta.querySelector('.pago-cerrar')?.addEventListener('click', () => modalPagarVenta.close());
    modalPagarVenta.querySelector('.pago-cancelar')?.addEventListener('click', () => modalPagarVenta.close());
});



