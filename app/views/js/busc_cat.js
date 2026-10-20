const inputCategoria = document.getElementById('id_categoria');
const listaCategorias = document.getElementById('lista_categorias');
const itemsCategoria = listaCategorias.getElementsByTagName('li');
const categoriaIdOculto = document.getElementById('categoria_id_oculto');

// Mostrar la lista al hacer clic o enfocar el input
inputCategoria.addEventListener('focus', () => {
    //  Cierra TODAS las listas que existan en la página antes de abrir esta
    document.querySelectorAll('.lista-resultados').forEach(lista => {
        lista.style.display = 'none';
    });

    // Muestra únicamente la lista de categorías
    listaCategorias.style.display = 'block';
});

// Filtrar en tiempo real mientras el usuario escribe
inputCategoria.addEventListener('input', () => {
    const textoFiltro = inputCategoria.value.toLowerCase();
    listaCategorias.style.display = 'block';

    for (let item of itemsCategoria) {
        let textoItem = item.textContent.toLowerCase();
        
        if (textoItem.includes(textoFiltro)) {
            item.style.display = ""; 
        } else {
            item.style.display = "none"; 
        }
    }
});

// Seleccionar un elemento al hacer clic
for (let item of itemsCategoria) {
    item.addEventListener('click', () => {
        inputCategoria.value = item.textContent.trim(); 
        
        categoriaIdOculto.value = item.getAttribute('data-id');
        listaCategorias.style.display = 'none';
    });
};

// Ocultar la lista si se hace clic fuera del buscador
document.addEventListener('click', (e) => {
    if (!e.target.closest('.contenedor-buscador')) {
        listaCategorias.style.display = 'none';
    }
});