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