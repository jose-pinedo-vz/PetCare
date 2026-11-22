document.addEventListener("DOMContentLoaded", () => {
    const normalizar = (texto) => (texto || '').normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().trim();
    const buscador = document.querySelector(".buscador-inteligente");
    const filas = document.querySelectorAll(".tabla-envoltura tbody tr");

    if (buscador && filas.length > 0) {
        buscador.addEventListener("input", (e) => {
            const textoBusqueda = normalizar(e.target.value);
            
            filas.forEach(fila => {
                const textoFila = normalizar(fila.textContent);

                if (textoBusqueda === "" || textoFila.includes(textoBusqueda)) {
                    fila.style.display = ""; 
                } else {
                    fila.style.display = "none"; 
                }
            });
        });
    }
});


/*document.addEventListener("DOMContentLoaded", () => {
    const buscador = document.querySelector(".buscador-inteligente");

    if (buscador) {
        buscador.addEventListener("input", (e) => {
            const textoBusqueda = e.target.value.toLowerCase().trim();
            const filas = document.querySelectorAll(".tabla-ancha tbody tr");

            filas.forEach(fila => {
                // Obtenemos únicamente la columna 1 donde se encuentra el ID y la columna 2 donde se encuentra el nombre
                const id = fila.querySelector("td:nth-child(1)")?.textContent.toLowerCase() || "";
                const nombre = fila.querySelector("td:nth-child(2)")?.textContent.toLowerCase() || "";
                
                // Combinamos solo esos dos textos para evaluar la coincidencia
                const textoFila = id + " " + nombre;

                if (textoBusqueda === "" || textoFila.includes(textoBusqueda)) {
                    fila.style.display = ""; 
                } else {
                    fila.style.display = "none"; 
                }
            });
        });
    }
});*/