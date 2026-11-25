const modalCategoria = document.getElementById('modal-nueva-categoria');
const botonAbrirModalCategoria = document.getElementById('btn-abrir-modal-categoria');

if (modalCategoria && botonAbrirModalCategoria) {
    const nombreCategoria = document.getElementById('modal-nombre-categoria');
    const descripcionCategoria = document.getElementById('modal-descripcion-categoria');
    const estadoCategoria = document.getElementById('modal-estado-categoria');
    const mensajeCategoria = document.getElementById('mensaje-modal-categoria');
    const erroresCategoria = document.getElementById('errores-modal-categoria');
    const listaErroresCategoria = document.getElementById('lista-errores-modal-categoria');
    const botonGuardarCategoria = document.getElementById('btn-guardar-categoria');
    const tokenCSRF = modalCategoria.dataset.csrf;
    const inputCategoria = document.getElementById('id_categoria');
    const categoriaIdOculto = document.getElementById('categoria_id_oculto');
    const listaCategorias = document.getElementById('lista_categorias');

    const mostrarMensaje = (mensaje) => {
        mensajeCategoria.textContent = mensaje;
        mensajeCategoria.hidden = false;
    };

    const mostrarErrores = (errores) => {
        listaErroresCategoria.replaceChildren();
        errores.forEach((error) => {
            const elemento = document.createElement('li');
            elemento.textContent = error;
            listaErroresCategoria.appendChild(elemento);
        });
        erroresCategoria.hidden = errores.length === 0;
        mensajeCategoria.hidden = true;
    };

    botonAbrirModalCategoria.addEventListener('click', () => {
        mostrarErrores([]);
        mensajeCategoria.hidden = true;
        modalCategoria.showModal();
        nombreCategoria.focus();
    });

    modalCategoria.querySelectorAll('[data-cerrar-modal-categoria]').forEach((boton) => {
        boton.addEventListener('click', () => modalCategoria.close());
    });

    if (!erroresCategoria.hidden) {
        modalCategoria.showModal();
    }

    botonGuardarCategoria.addEventListener('click', async () => {
        const nombre = nombreCategoria.value.trim();
        if (!nombre) {
            mostrarErrores(['El nombre de la categoría es obligatorio.']);
            nombreCategoria.focus();
            return;
        }

        botonGuardarCategoria.disabled = true;
        mostrarErrores([]);
        mostrarMensaje('Guardando categoría...');

        try {
            const respuesta = await fetch('../controllers/categoria.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    csrf: tokenCSRF,
                    nombre,
                    descripcion: descripcionCategoria.value.trim(),
                    estado: estadoCategoria.value
                })
            });
            const resultado = await respuesta.json();

            if (!respuesta.ok || !resultado.exito) {
                const errores = Array.isArray(resultado.errores) && resultado.errores.length
                    ? resultado.errores
                    : [resultado.mensaje || 'No fue posible guardar la categoría.'];
                mostrarErrores(errores);
                return;
            }

            const categoria = resultado.categoria;
            if (categoria.estado === 'Activo') {
                const opcion = document.createElement('li');
                opcion.dataset.id = categoria.id_categoria;
                opcion.dataset.nombre = categoria.nombre;
                opcion.textContent = `Id: ${categoria.id_categoria} - ${categoria.nombre}`;
                listaCategorias.appendChild(opcion);
                inputCategoria.value = opcion.textContent;
                categoriaIdOculto.value = categoria.id_categoria;
                opcion.dispatchEvent(new Event('click', { bubbles: true }));
            }

            mostrarMensaje(categoria.estado === 'Activo'
                ? 'Categoría guardada y seleccionada.'
                : 'Categoría guardada como inactiva.');
            nombreCategoria.value = '';
            descripcionCategoria.value = '';
            estadoCategoria.value = 'Activo';
            window.setTimeout(() => modalCategoria.close(), 700);
        } catch (error) {
            mostrarErrores([error.message || 'No fue posible guardar la categoría.']);
        } finally {
            botonGuardarCategoria.disabled = false;
        }
    });
}
