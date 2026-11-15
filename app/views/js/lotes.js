const dialogoLote = document.getElementById('dialogo-lote');
const busquedaProducto = document.getElementById('lote-producto');
const idProducto = document.getElementById('lote-producto-id');
const campoProveedor = document.getElementById('lote-proveedor');
const listaProductos = document.getElementById('lista_productos_lote');
const productos = listaProductos.getElementsByTagName('li');

busquedaProducto.addEventListener('focus', () => {
    document.querySelectorAll('.lista-resultados').forEach((lista) => {
        lista.style.display = 'none';
    });
    listaProductos.style.display = 'block';
});

busquedaProducto.addEventListener('input', () => {
    idProducto.value = '';
    campoProveedor.value = '';
    const textoFiltro = busquedaProducto.value.toLocaleLowerCase('es');
    listaProductos.style.display = 'block';

    for (const producto of productos) {
        producto.style.display = producto.textContent.toLocaleLowerCase('es').includes(textoFiltro)
            ? ''
            : 'none';
    }
});

for (const producto of productos) {
    producto.addEventListener('click', () => {
        busquedaProducto.value = producto.textContent.trim();
        idProducto.value = producto.dataset.id;
        campoProveedor.value = producto.dataset.proveedor;
        listaProductos.style.display = 'none';
    });
}

document.addEventListener('click', (evento) => {
    if (!evento.target.closest('.contenedor-buscador')) {
        listaProductos.style.display = 'none';
    }
});

document.getElementById('abrir-formulario-lote').addEventListener('click', () => dialogoLote.showModal());
document.getElementById('cerrar-dialogo-lote').addEventListener('click', () => dialogoLote.close());

const productoRestaurado = Array.from(productos).find((producto) => producto.dataset.id === idProducto.value);
if (productoRestaurado) {
    busquedaProducto.value = productoRestaurado.textContent.trim();
    campoProveedor.value = productoRestaurado.dataset.proveedor;
}

if (dialogoLote.dataset.abrir === '1') {
    dialogoLote.showModal();
}
