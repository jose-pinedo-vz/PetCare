document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal-nueva-jaula');
    const botonAbrirModal = document.getElementById('btn-abrir-modal-jaula');
    const mensajeAcciones = document.getElementById('mensaje-acciones-hotel');
    const csrf = modal ? modal.dataset.csrf : '';

    const mostrarError = (mensaje) => {
        if (mensajeAcciones) {
            mensajeAcciones.textContent = mensaje;
            mensajeAcciones.hidden = false;
            mensajeAcciones.className = 'msg-error';
        }
    };

    const enviarAccion = async (datos, boton) => {
        if (boton) {
            boton.disabled = true;
        }

        try {
            const respuesta = await fetch('../controllers/hotel_acciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ...datos, csrf })
            });
            const resultado = await respuesta.json();
            if (!respuesta.ok || !resultado.exito) {
                throw new Error(resultado.mensaje || 'No fue posible completar la acción.');
            }
            window.location.reload();
        } catch (error) {
            mostrarError(error.message || 'No fue posible completar la acción.');
            if (boton) {
                boton.disabled = false;
            }
        }
    };

    if (modal && botonAbrirModal) {
        const tamano = document.getElementById('modal-tamano-jaula');
        const botonGuardar = document.getElementById('btn-guardar-jaula');
        const errores = document.getElementById('errores-modal-jaula');
        const listaErrores = document.getElementById('lista-errores-modal-jaula');
        const mensaje = document.getElementById('mensaje-modal-jaula');

        const mostrarErrores = (textos) => {
            listaErrores.replaceChildren();
            textos.forEach((texto) => {
                const item = document.createElement('li');
                item.textContent = texto;
                listaErrores.appendChild(item);
            });
            errores.hidden = textos.length === 0;
            mensaje.hidden = true;
        };

        botonAbrirModal.addEventListener('click', () => {
            mostrarErrores([]);
            tamano.value = '';
            modal.showModal();
            tamano.focus();
        });
        modal.querySelectorAll('[data-cerrar-modal-jaula]').forEach((boton) => {
            boton.addEventListener('click', () => modal.close());
        });

        botonGuardar.addEventListener('click', async () => {
            if (!tamano.value) {
                mostrarErrores(['Selecciona un tamaño de jaula.']);
                tamano.focus();
                return;
            }
            botonGuardar.disabled = true;
            mostrarErrores([]);
            mensaje.textContent = 'Guardando jaula...';
            mensaje.hidden = false;
            try {
                const respuesta = await fetch('../controllers/hotel_acciones.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ accion: 'jaula_crear', tamano: tamano.value, csrf })
                });
                const resultado = await respuesta.json();
                if (!respuesta.ok || !resultado.exito) {
                    throw new Error(resultado.mensaje || 'No fue posible guardar la jaula.');
                }
                window.location.reload();
            } catch (error) {
                mostrarErrores([error.message || 'No fue posible guardar la jaula.']);
                botonGuardar.disabled = false;
            }
        });
    }

    document.addEventListener('click', (evento) => {
        const boton = evento.target.closest('[data-hotel-accion]');
        if (!boton || boton.disabled) {
            return;
        }

        const accion = boton.dataset.hotelAccion;
        const id = Number(boton.dataset.id);
        const confirmaciones = {
            jaula_eliminar: '¿Eliminar definitivamente esta jaula?',
            reservacion_cancelar: '¿Cancelar esta reservación?',
            reservacion_no_asistio: '¿Marcar esta reservación como No asistió?',
            reservacion_finalizar: '¿Finalizar esta reservación?'
        };
        if (confirmaciones[accion] && !window.confirm(confirmaciones[accion])) {
            return;
        }
        enviarAccion({ accion, id }, boton);
    });
});
