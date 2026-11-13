const inputBusqueda = document.getElementById('id_proveedor');
const listaResultados = document.getElementById('lista_proveedores');
const items = listaResultados.getElementsByTagName('li');
const valorOcultoServidor = document.getElementById('proveedor_id_oculto');

// Mostrar la lista al hacer clic o enfocar el input
inputBusqueda.addEventListener('focus', () => {
    //  Cierra TODAS las listas que existan en la página antes de abrir esta
    document.querySelectorAll('.lista-resultados').forEach(lista => {
        lista.style.display = 'none';
    });

    // Muestra únicamente la lista de proveedores
    listaResultados.style.display = 'block';
});

// Filtrar en tiempo real mientras el usuario escribe
inputBusqueda.addEventListener('input', () => {
    valorOcultoServidor.value = '';
    const textoFiltro = inputBusqueda.value.toLowerCase();
    listaResultados.style.display = 'block';

    for (let item of items) {
        let textoItem = item.textContent.toLowerCase();
        
        if (textoItem.includes(textoFiltro)) {
            item.style.display = ""; 
        } else {
            item.style.display = "none"; 
        }
    }
});

// Seleccionar un elemento al hacer clic
for (let item of items) {
    item.addEventListener('click', () => {
        inputBusqueda.value = item.textContent.trim(); 
        
        valorOcultoServidor.value = item.getAttribute('data-id');
        listaResultados.style.display = 'none';
    });
};

// Ocultar la lista si se hace clic fuera del buscador
document.addEventListener('click', (e) => {
    if (!e.target.closest('.contenedor-buscador')) {
        listaResultados.style.display = 'none';
    }
});