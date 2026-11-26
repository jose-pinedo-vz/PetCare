<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = 'mascotas';

require_once __DIR__ . '/../../models/Mascota.php';

//la mascota viene en la URL: editar_mascota.php?id_mascota=3
$idMascota = (int) ($_GET['id_mascota'] ?? 0);
$mascota = Mascota::obtenerPorId($idMascota);

//si no existe, esta borrada o es de otro cliente, se regresa a la lista
if (!$mascota || !$mascota['esta_activo'] || (int) $mascota['id_cliente'] !== (int) $_SESSION['id_cliente']) {
    $_SESSION['errores'] = ['No se encontró la mascota.'];
    header('Location: mis_mascotas.php');
    exit;
}

//errores y datos escritos 
$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);

//si hay datos escritos se usan esos; si no, los de la base de datos
$v = function (string $campo) use ($old, $mascota) {
    return htmlspecialchars((string) ($old[$campo] ?? $mascota[$campo] ?? ''));
};

$foto = rutaFotoMascota($mascota['fotografia']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar mascota</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <p class="migas-de-pan"><a href="mis_mascotas.php">Mis mascotas</a> › Editar mascota</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Editar mascota</h1>
            <p class="tarjeta__subtitulo">Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>

            <?php if ($errores): ?>
                <div class="aviso aviso--error">
                    <?php foreach ($errores as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/cliente_editar_mascota.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_mascota" value="<?= (int) $mascota['id_mascota'] ?>">

                <div class="formulario-con-foto">
                    <div>
                        <?php if ($foto): ?>
                            <img class="foto-marcador foto-marcador--subir" id="foto-actual" src="../../../<?= htmlspecialchars($foto) ?>" alt="Foto de <?= $v('nombre') ?>">
                        <?php else: ?>
                            <div class="foto-marcador foto-marcador--subir" id="foto-actual">Foto de la mascota</div>
                        <?php endif; ?>
                        <img id="preview-crop" class="foto-marcador foto-marcador--subir" alt="Vista previa de la foto recortada">
                        <div class="campo">
                            <label class="campo__etiqueta" for="fotografia">Cambiar foto</label>
                            <input class="campo__entrada" type="file" id="fotografia" name="fotografia" accept="image/*">
                            <small class="campo__ayuda">Al elegir una foto se abrirá un cuadro para recortarla en cuadrado.</small>
                        </div>
                    </div>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="nombre">Nombre <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="nombre" name="nombre" placeholder="Ej. Luna" value="<?= $v('nombre') ?>" required maxlength="20" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+" title="Solo letras y espacios, sin números ni símbolos">
                        </div>

                        <?php
                        // Especie, raza y color salen de la BD (desplegables)
                        $valEspecie   = (string) ($old['especie'] ?? $mascota['especie'] ?? '');
                        $valRaza      = (string) ($old['raza'] ?? $mascota['raza'] ?? '');
                        $valColor     = (string) ($old['color'] ?? $mascota['color'] ?? '');
                        $valColorOtro = (string) ($old['color_otro'] ?? '');
                        include __DIR__ . '/_campos_especie_raza_color.php';
                        ?>

                        <div class="campo">
                            <label class="campo__etiqueta" for="sexo">Sexo <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="sexo" name="sexo" required>
                                <?php foreach (['Hembra', 'Macho'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $v('sexo') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="edad">Fecha de nacimiento <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="date" id="edad" name="edad" min="<?= date('Y-m-d', strtotime('-40 years')) ?>" max="<?= date('Y-m-d') ?>" value="<?= substr($v('edad'), 0, 10) ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="peso">Peso (kg) <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="number" id="peso" name="peso" placeholder="Ej. 24" min="0" step="0.1" value="<?= $v('peso') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="tamanio">Tamaño</label>
                            <select class="campo__entrada" id="tamanio" name="tamanio">
                                <?php foreach (['Pequeño', 'Mediano', 'Grande', 'Gigante'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $v('tamanio') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    

                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="alergias">Alergias</label>
                            <textarea class="campo__entrada" id="alergias" name="alergias" rows="3" maxlength="500" placeholder="Ej. Polen, pollo, penicilina"><?= $v('alergias') ?></textarea>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="enfermedades">Enfermedades</label>
                            <textarea class="campo__entrada" id="enfermedades" name="enfermedades" rows="3" maxlength="500" placeholder="Ej. Diabetes, displasia de cadera"><?= $v('enfermedades') ?></textarea>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="medicamentos">Medicamentos</label>
                            <textarea class="campo__entrada" id="medicamentos" name="medicamentos" rows="3" maxlength="500" placeholder="Ej. Insulina cada 12 horas"><?= $v('medicamentos') ?></textarea>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="condiciones_especiales">Condiciones especiales</label>
                            <textarea class="campo__entrada" id="condiciones_especiales" name="condiciones_especiales" rows="3" maxlength="500" placeholder="Ej. Ciego de un ojo"><?= $v('condiciones_especiales') ?></textarea>
                        </div>-->

                        <div class="campo">
                            <label class="campo__etiqueta" for="ultima_desparasitacion">Última desparasitación</label>
                            <input class="campo__entrada" type="date" id="ultima_desparasitacion" name="ultima_desparasitacion" max="<?= date('Y-m-d') ?>" value="<?= substr($v('ultima_desparasitacion'), 0, 10) ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="temperamento">Temperamento</label>
                            <textarea class="campo__entrada" id="temperamento" name="temperamento" rows="2" maxlength="100" placeholder="Ej. Dócil, juguetón, tímido"><?= $v('temperamento') ?></textarea>
                        </div>

                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="restricciones_para_manejo">Restricciones para manejo</label>
                            <textarea class="campo__entrada" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="3" maxlength="500" placeholder="Ej. Usar bozal, no tocar las orejas"><?= $v('restricciones_para_manejo') ?></textarea>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="observaciones">Observaciones</label>
                            <textarea class="campo__entrada" id="observaciones" name="observaciones" rows="3" maxlength="500" placeholder="Ej. Se pone nervioso con otros perros"><?= $v('observaciones') ?></textarea>
                        </div>-->
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Guardar cambios</button>
                    <a class="boton boton--contorno" href="mis_mascotas.php">Cancelar</a>
                </div>
                <!-- Recorte cuadrado de la foto (el mismo del lado del empleado) -->
                <div id="modal-crop">
                    <div class="caja">
                        <h3>Recortar foto (cuadrado)</h3>
                        <img id="imagen-a-recortar" alt="Imagen a recortar">
                        <div class="crop-acciones">
                            <button type="button" id="btn-cancelar-crop" class="boton boton--contorno boton--chico">Cancelar</button>
                            <button type="button" id="btn-recortar" class="boton boton--chico">Recortar y usar</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
    <!-- Recorte de la foto: mismo código que el formulario del empleado -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="../js/crop-mascota.js"></script>
    <script>
    // Cuando ya hay foto recortada se oculta la foto anterior (o el recuadro vacío)
    (function () {
        const recortada = document.getElementById('preview-crop');
        const anterior = document.getElementById('foto-actual');
        if (!recortada || !anterior) return;
        new MutationObserver(function () {
            anterior.style.display = recortada.getAttribute('src') ? 'none' : '';
        }).observe(recortada, { attributes: true, attributeFilter: ['src'] });
    })();
    </script>
</body>
</html>
