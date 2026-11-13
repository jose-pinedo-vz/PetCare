// Recorte y gestión de imágenes para radiografías en ficha médica (permite selección múltiple).
(function () {
  var inputRadiografias = document.getElementById('radiografias');
  var modal = document.getElementById('modal-crop');
  var imgCrop = document.getElementById('imagen-a-recortar');
  var tituloCrop = document.getElementById('crop-titulo');
  var contenedorPreviews = document.getElementById('contenedor-previews-radiografias');
  var btnRecortar = document.getElementById('btn-recortar');
  var btnCancelar = document.getElementById('btn-cancelar-crop');

  if (!inputRadiografias || !modal || !imgCrop || !btnRecortar || !btnCancelar) return;

  var cropper = null;
  var objectUrlActual = null;
  var nombreOriginal = 'radiografia.jpg';

  // Cola de archivos seleccionados pendientes de recortar
  var archivosCola = [];
  var indiceActual = 0;

  // DataTransfer acumulador con los archivos finales ya recortados
  var dtFinal = new DataTransfer();

  function limpiarCropper() {
    if (cropper) {
      cropper.destroy();
      cropper = null;
    }
    if (objectUrlActual) {
      URL.revokeObjectURL(objectUrlActual);
      objectUrlActual = null;
    }
  }

  function cerrarModal() {
    limpiarCropper();
    imgCrop.removeAttribute('src');
    modal.style.display = 'none';
    archivosCola = [];
    indiceActual = 0;
  }

  function cargarSiguienteEnCola() {
    if (indiceActual >= archivosCola.length) {
      cerrarModal();
      actualizarInputYPreviews();
      return;
    }

    limpiarCropper();

    var file = archivosCola[indiceActual];
    nombreOriginal = file.name || ('radiografia_' + (dtFinal.files.length + 1) + '.jpg');

    if (tituloCrop) {
      if (archivosCola.length > 1) {
        tituloCrop.textContent = 'Recortar radiografía (' + (indiceActual + 1) + ' de ' + archivosCola.length + ')';
      } else {
        tituloCrop.textContent = 'Recortar radiografía';
      }
    }

    objectUrlActual = URL.createObjectURL(file);
    modal.style.display = 'flex';
    imgCrop.src = objectUrlActual;

    function iniciarCropper() {
      if (cropper) {
        cropper.destroy();
        cropper = null;
      }
      cropper = new Cropper(imgCrop, {
        viewMode: 1,
        autoCropArea: 0.95,
        dragMode: 'move',
        guides: true,
        center: true,
        highlight: true,
        background: false
      });
    }

    imgCrop.onload = iniciarCropper;

    if (imgCrop.complete && imgCrop.naturalWidth > 0) {
      iniciarCropper();
    }
  }

  function actualizarInputYPreviews() {
    // Sincronizar el input con los archivos recortados
    inputRadiografias.files = dtFinal.files;

    if (!contenedorPreviews) return;

    contenedorPreviews.innerHTML = '';

    if (dtFinal.files.length === 0) {
      contenedorPreviews.style.display = 'none';
      return;
    }

    contenedorPreviews.style.display = 'flex';

    Array.from(dtFinal.files).forEach(function (file, index) {
      var itemCard = document.createElement('div');
      itemCard.className = 'preview-radio-card';
      itemCard.style.cssText = 'position: relative; width: 110px; background: rgba(0,0,0,0.3); padding: 6px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.18); text-align: center;';

      var thumb = document.createElement('img');
      thumb.style.cssText = 'width: 100%; height: 85px; object-fit: cover; border-radius: 5px; display: block;';
      thumb.src = URL.createObjectURL(file);
      thumb.alt = file.name;

      var nameLabel = document.createElement('div');
      nameLabel.style.cssText = 'font-size: 0.72rem; color: #cfd8dc; margin-top: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;';
      nameLabel.textContent = file.name;
      nameLabel.title = file.name;

      var btnQuitar = document.createElement('button');
      btnQuitar.type = 'button';
      btnQuitar.style.cssText = 'position: absolute; top: -6px; right: -6px; background: #e53935; color: #ffffff; border: 2px solid #ffffff; border-radius: 50%; width: 22px; height: 22px; cursor: pointer; font-size: 13px; line-height: 18px; padding: 0; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.5);';
      btnQuitar.innerHTML = '&times;';
      btnQuitar.title = 'Quitar esta imagen';

      btnQuitar.addEventListener('click', function (ev) {
        ev.stopPropagation();
        eliminarArchivo(index);
      });

      itemCard.appendChild(thumb);
      itemCard.appendChild(nameLabel);
      itemCard.appendChild(btnQuitar);
      contenedorPreviews.appendChild(itemCard);
    });
  }

  function eliminarArchivo(indexAEliminar) {
    var nuevoDt = new DataTransfer();
    for (var i = 0; i < dtFinal.files.length; i++) {
      if (i !== indexAEliminar) {
        nuevoDt.items.add(dtFinal.files[i]);
      }
    }
    dtFinal = nuevoDt;
    actualizarInputYPreviews();
  }

  inputRadiografias.addEventListener('change', function (e) {
    var files = e.target.files;
    if (!files || files.length === 0) return;

    archivosCola = [];
    for (var i = 0; i < files.length; i++) {
      if (files[i].type && files[i].type.indexOf('image/') === 0) {
        archivosCola.push(files[i]);
      }
    }

    if (archivosCola.length === 0) {
      alert('Por favor selecciona archivos de imagen válidos (JPG, PNG, WEBP o GIF).');
      inputRadiografias.files = dtFinal.files;
      return;
    }

    indiceActual = 0;
    cargarSiguienteEnCola();
  });

  btnRecortar.addEventListener('click', function () {
    if (!cropper) return;

    cropper.getCroppedCanvas({ maxWidth: 1600, maxHeight: 1600 }).toBlob(function (blob) {
      if (!blob) {
        alert('No se pudo recortar la imagen. Intenta con otra foto.');
        return;
      }

      var base = (nombreOriginal.split('.').slice(0, -1).join('.') || 'radiografia');
      var archivoRecortado = new File([blob], base + '.jpg', { type: 'image/jpeg' });

      dtFinal.items.add(archivoRecortado);
      actualizarInputYPreviews();

      indiceActual++;
      limpiarCropper();

      if (indiceActual < archivosCola.length) {
        cargarSiguienteEnCola();
      } else {
        cerrarModal();
      }
    }, 'image/jpeg', 0.92);
  });

  btnCancelar.addEventListener('click', function () {
    // Omite la imagen actual de la cola y pasa a la siguiente si existe
    indiceActual++;
    limpiarCropper();

    if (indiceActual < archivosCola.length) {
      cargarSiguienteEnCola();
    } else {
      cerrarModal();
      actualizarInputYPreviews();
    }
  });

  // Cerrar al hacer click fuera del cuadro modal
  modal.addEventListener('click', function (e) {
    if (e.target === modal) {
      cerrarModal();
      actualizarInputYPreviews();
    }
  });
})();
