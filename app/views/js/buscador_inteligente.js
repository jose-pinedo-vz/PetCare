// Este evento me permite que no ocurra errores si la la pagina de html no carga no se ejecuta
// y revisa que los datos ya existan para realizar la busqueda
document.addEventListener("DOMContentLoaded", () => {
    //Aqui busca el campo con la clasee buscador_inteligente para
    // cargar los datos que tiene esa caja de texto para realizar la busqued
    const buscador = document.querySelector(".buscador-inteligente");
    // Aqui aguarda todas las filas de la tabla para poder filtrar las filas que se busqen
    const filas = document.querySelectorAll(".tabla-envoltura tbody tr");

    //Mi condicion le indica que exista una palabra en el buscador y que las filas existan
    if (buscador && filas.length > 0) {
        //ESto es un escuchador detecta cada vez que el usuario introduce una letra o numero en el buscador
        buscador.addEventListener("input", (e) => {
            // e.target.value esto nos permite tomar todo lo que esta escribiendo el usuario
            // .toLowerCase esto hace que convierte todas las letras en minusculas
            // Quitamos espacios a los lados inmediatamente
            const textoBusqueda = e.target.value.toLowerCase().trim();
            
            // Revisamos cada fila de la tabla
            filas.forEach(fila => {
                //Sacamos cada letra de fila para poder compararlo correctamente
                const textoFila = fila.textContent.toLowerCase();

                // Si el buscador está vacío, muestra todas las filas; si tiene texto, filtra
                if (textoBusqueda === "" || textoFila.includes(textoBusqueda)) {
                    // Di la condicion se cumple dejo visible la fila que el buscador quiere encontrar
                    fila.style.display = ""; 
                } else {
                    // Esconde la fila si no se encuentra lo que el buscador quiere
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