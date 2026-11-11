const modalCategoria = document.getElementById('modal-nueva-categoria');
const botonAbrirModalCategoria = document.getElementById('btn-abrir-modal-categoria');

if (modalCategoria && botonAbrirModalCategoria) {
    botonAbrirModalCategoria.addEventListener('click', () => modalCategoria.showModal());

    modalCategoria.querySelectorAll('[data-cerrar-modal-categoria]').forEach((boton) => {
        boton.addEventListener('click', () => modalCategoria.close());
    });
}
