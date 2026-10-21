
// funcion encarfada de mostrar y ocultar los elemtos con el nombre espesificado
// resice 2 datos, nombre a buscar y mostrar y las tarjetas o divs de los nombres
function mostrarNombre(nombre,tarjetas)
{
    if(nombre)
    {
        nombre.addEventListener('input', (evento) => 
        {
            const valor = evento.target.value.toLowerCase().trim();
            console.log('Valor ingresado:', valor);
            tarjetas.forEach(tarjeta =>
                {
                    const nombreDuenio = tarjeta.getAttribute('data-nombre');


                    if (nombreDuenio && nombreDuenio.toLowerCase().includes(valor))
                    {
                        tarjeta.classList.remove("oculto");
                    }
                    else
                    {
                        tarjeta.classList.add('oculto');
                    }
                }
            );
        });
    }
    else
    {
        console.log("No se enconctro el elemento");
    }
}



// escuchardor de el nombre dentro de citas
// manda 2 datos nombres a mostrart y los divs donde estan
document.addEventListener('DOMContentLoaded', function()
{
    const nombre = document.getElementById('buscarNombre');
    const tarjetas = document.querySelectorAll('.Card_cita');

    mostrarNombre(nombre, tarjetas)
});



// ocultar citas entre atendidas y en espera
function mostrarSeccion(tipo, evt)
{
    const seccionEspera = document.getElementById("seccion_espera");
    const seccionAtendidos = document.getElementById('seccion_atendidos');
    const botones = document.querySelectorAll('.btn_active');

    botones.forEach(btn => btn.classList.remove('active'));
    evt.target.classList.add('active');

    if (tipo === 'espera') {
        seccionEspera.classList.remove('oculto');
        seccionAtendidos.classList.add('oculto');
    } else if (tipo === 'atendidas') {
        seccionAtendidos.classList.remove('oculto');
        seccionEspera.classList.add('oculto');
    }
}


document.addEventListener('DOMContentLoaded', function() 
{
    const fecha = document.getElementById('fecha');

    if (fecha)
    {
        fecha.addEventListener("change", (evento) => 
        {
            evento.target.value.toLowerCase().trim();
            // const valor = document.getElementById('fecha');
            const tarjetas = document.querySelectorAll('.Card_cita');

            const option = evento.target.value;

            switch (option) 
            {
                case 'hoy':
                    console.log('hoy');
                    tarjetas.forEach(tarjeta =>
                        {
                            const nombreDuenio = tarjeta.getAttribute('data-fecha');
                            const fechaActual = new Date();
                            const anio = fechaActual.getFullYear();
                            const mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
                            const dia = String(fechaActual.getDate()).padStart(2, '0');

                            const hoy = `${anio}-${mes}-${dia}`;
                            if (nombreDuenio && nombreDuenio.substring(0,10) === hoy)
                            {
                                tarjeta.classList.remove("oculto");
                            }
                            else
                            {
                                tarjeta.classList.add('oculto');
                            }
                        }
                    );
                    break;


                case 'semana':
                    console.log('semana');
                    const curr = new Date();
                    const primerDia = new Date(curr.setDate(curr.getDate() - curr.getDay() + (curr.getDay() === 0 ? -6 : 1)));
                    const ultimoDia = new Date(primerDia);
                    ultimoDia.setDate(primerDia.getDate() + 6);

                    const formatearFecha = (d) => {
                        const anio = d.getFullYear();
                        const mes = String(d.getMonth() + 1).padStart(2, '0');
                        const dia = String(d.getDate()).padStart(2, '0');
                        return `${anio}-${mes}-${dia}`;
                    };

                    const inicioStr = formatearFecha(primerDia);
                    const finStr = formatearFecha(ultimoDia);


                    tarjetas.forEach(tarjeta => {
                        const fechaTarjeta = tarjeta.getAttribute('data-fecha');
                        if (fechaTarjeta) {
                            const soloFechaTarjeta = fechaTarjeta.substring(0, 10);
                            
                            if (soloFechaTarjeta >= inicioStr && soloFechaTarjeta <= finStr) {
                                tarjeta.classList.remove("oculto");
                            } else {
                                tarjeta.classList.add('oculto');
                            }
                        }
                    });
                    break;



                case 'mes':
                    console.log('mes');
                    const fechaActual = new Date();
                    const anio = fechaActual.getFullYear();
                    const mes = String(fechaActual.getMonth() + 1).padStart(2, '0');
                    const mesActualStr = `${anio}-${mes}`;

                    tarjetas.forEach(tarjeta => {
                        const fechaTarjeta = tarjeta.getAttribute('data-fecha');
                        if (fechaTarjeta) {
                            const mesTarjeta = fechaTarjeta.substring(0, 7);
                            
                            if (mesTarjeta === mesActualStr) {
                                tarjeta.classList.remove("oculto");
                            } else {
                                tarjeta.classList.add('oculto');
                            }
                        }
                    });
                    break;


                    
                case 'todos':
                    tarjetas.forEach(tarjeta => {
                        tarjeta.classList.remove("oculto");
                    });
                    break;

                default:
                    break;
            } 
        }); 
    }
});


