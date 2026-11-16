document.addEventListener("DOMContentLoaded", () => {
    const buscador = document.getElementById("buscar-reservacion");
    const filtroFecha = document.getElementById("filtro-fecha-reservacion");
    const filtroEstado = document.getElementById("filtro-estado-reservacion");
    const filas = document.querySelectorAll("#tabla-reservaciones tbody tr[data-reservacion]");
    const mensajeSinResultados = document.getElementById("sin-reservaciones");

    if (!buscador || !filtroFecha || !filtroEstado || !mensajeSinResultados) return;

    // El buscador compartido filtra primero por texto; este filtro combina fecha y estado.
    const aplicarFiltros = () => {
        const fechaSeleccionada = filtroFecha.value;
        const estadoSeleccionado = filtroEstado.value;
        let filasVisibles = 0;

        filas.forEach((fila) => {
            const coincideTexto = fila.style.display !== "none";
            const coincideFecha = !fechaSeleccionada || fila.dataset.fechaEntrada === fechaSeleccionada;
            const coincideEstado = !estadoSeleccionado || fila.dataset.estado === estadoSeleccionado;
            const mostrar = coincideTexto && coincideFecha && coincideEstado;

            fila.style.display = mostrar ? "" : "none";
            if (mostrar) filasVisibles += 1;
        });

        mensajeSinResultados.hidden = filasVisibles > 0;
    };

    // Se vuelve a ejecutar después del buscador para conservar ambos filtros.
    const actualizarFiltros = () => window.requestAnimationFrame(aplicarFiltros);

    buscador.addEventListener("input", actualizarFiltros);
    filtroFecha.addEventListener("change", () => buscador.dispatchEvent(new Event("input", { bubbles: true })));
    filtroEstado.addEventListener("change", () => buscador.dispatchEvent(new Event("input", { bubbles: true })));
    aplicarFiltros();
});
