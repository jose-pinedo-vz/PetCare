// Recorte cuadrado para fotos de mascotas (agregar + editar).
(function () {
  var inputFoto = document.getElementById('fotografia');
  var modal = document.getElementById('modal-crop');
  var imgCrop = document.getElementById('imagen-a-recortar');
  var preview = document.getElementById('preview-crop');
  var btnRecortar = document.getElementById('btn-recortar');
  var btnCancelar = document.getElementById('btn-cancelar-crop');

  if (!inputFoto || !modal || !imgCrop || !preview || !btnRecortar || !btnCancelar) return;

  var cropper = null;
  var nombreOriginal = 'mascota.jpg';
  var objectUrlActual = null;

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

  inputFoto.addEventListener('change', function (e) {
    var file = e.target.files && e.target.files[0];
    if (!file) return;

    // Solo imágenes
    if (!file.type || file.type.indexOf('image/') !== 0) {
      alert('Elige un archivo de imagen válido.');
      inputFoto.value = '';
      return;
    }

    nombreOriginal = file.name || 'mascota.jpg';
    limpiarCropper();

    objectUrlActual = URL.createObjectURL(file);
    imgCrop.src = objectUrlActual;
    modal.style.display = 'flex';

    imgCrop.onload = function () {
      // Siempre cuadrado 1:1
      cropper = new Cropper(imgCrop, {
        aspectRatio: 1,
        viewMode: 1,
        autoCropArea: 0.9,
        dragMode: 'move',
        guides: true,
        center: true,
        highlight: true,
        background: false
      });
    };
  });

  btnCancelar.addEventListener('click', function () {
    limpiarCropper();
    imgCrop.removeAttribute('src');
    modal.style.display = 'none';
    inputFoto.value = '';
  });

  // Cerrar modal con click fuera de la caja
  modal.addEventListener('click', function (e) {
    if (e.target === modal) btnCancelar.click();
  });

  btnRecortar.addEventListener('click', function () {
    if (!cropper) return;

    // 600x600
    cropper.getCroppedCanvas({ width: 600, height: 600 }).toBlob(function (blob) {
      if (!blob) {
        alert('No se pudo recortar la imagen. Intenta con otra foto.');
        return;
      }
      var base = (nombreOriginal.split('.').slice(0, -1).join('.') || 'mascota');
      var archivoRecortado = new File([blob], base + '.jpg', { type: 'image/jpeg' });

      var dt = new DataTransfer();
      dt.items.add(archivoRecortado);
      inputFoto.files = dt.files;

      if (preview.src && preview.src.indexOf('blob:') === 0) URL.revokeObjectURL(preview.src);
      preview.src = URL.createObjectURL(archivoRecortado);
      preview.style.display = 'block';

      limpiarCropper();
      imgCrop.removeAttribute('src');
      modal.style.display = 'none';
    }, 'image/jpeg', 0.9);
  });
})();
