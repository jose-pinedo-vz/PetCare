<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);

$paginaActiva = 'mascotas';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar mascota</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
</head>

<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <p class="migas-de-pan"><a href="mis_mascotas.php">Mis mascotas</a> › Registrar mascota</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Registrar mascota</h1>
            <p class="tarjeta__subtitulo">Los campos con <span
                    class="campo__obligatorio">*</span> son obligatorios.</p>
            <?php if ($errores): ?>
                <div class="aviso aviso--error">
                    <?php foreach ($errores as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/cliente_guardar_mascota.php" method="post" enctype="multipart/form-data">

                <div class="formulario-con-foto">
                    <div>
                        <div class="foto-marcador foto-marcador--subir" id="foto-actual">Foto de tu mascota</div>
                        <img id="preview-crop" class="foto-marcador foto-marcador--subir" alt="Vista previa de la foto recortada">
                        <div class="campo">
                            <label class="campo__etiqueta" for="fotografia">Subir foto</label>
                            <input class="campo__entrada" type="file" id="fotografia" name="fotografia" accept="image/*">
                            <small class="campo__ayuda">Al elegir una foto se abrirá un cuadro para recortarla en cuadrado.</small>
                        </div>
                    </div>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="nombre">Nombre <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="nombre" name="nombre"
                                placeholder="Ej. Luna" required maxlength="20" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+" title="Solo letras y espacios, sin números ni símbolos"
                                value="<?= htmlspecialchars($old['nombre'] ?? '') ?>">
                        </div>

                        <?php
                        // Especie, raza y color salen de la BD (desplegables)
                        $valEspecie   = (string) ($old['especie'] ?? '');
                        $valRaza      = (string) ($old['raza'] ?? '');
                        $valColor     = (string) ($old['color'] ?? '');
                        $valColorOtro = (string) ($old['color_otro'] ?? '');
                        include __DIR__ . '/_campos_especie_raza_color.php';
                        ?>

                        <div class="campo">
                            <label class="campo__etiqueta" for="sexo">Sexo <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="sexo" name="sexo" required>
                                <option value="">Elige el sexo</option>
                                <?php foreach (['Macho', 'Hembra'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= ($old['sexo'] ?? '') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="edad">Fecha de nacimiento <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="date" id="edad" name="edad" min="<?= date('Y-m-d', strtotime('-40 years')) ?>" max="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars($old['edad'] ?? '') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="peso">Peso (kg) <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="number" id="peso" name="peso" placeholder="Ej. 24" min="0" step="0.1" required value="<?= htmlspecialchars($old['peso'] ?? '') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="tamanio">Tamaño</label>
                            <select class="campo__entrada" id="tamanio" name="tamanio">
                                <?php foreach (['Pequeño', 'Mediano', 'Grande', 'Gigante'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= ($old['tamanio'] ?? '') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>


                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="alergias">Alergias</label>
                            <textarea class="campo__entrada" id="alergias" name="alergias" rows="3" maxlength="500" placeholder="Ej. Polen, pollo, penicilina"><?= htmlspecialchars($old['alergias'] ?? '') ?></textarea>
                        </div>-->

                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="enfermedades">Enfermedades</label>
                            <textarea class="campo__entrada" id="enfermedades" name="enfermedades" rows="3" maxlength="500" placeholder="Ej. Diabetes, displasia de cadera"><?= htmlspecialchars($old['enfermedades'] ?? '') ?></textarea>
                        </div>-->

                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="medicamentos">Medicamentos</label>
                            <textarea class="campo__entrada" id="medicamentos" name="medicamentos" rows="3" maxlength="500" placeholder="Ej. Insulina cada 12 horas"><?= htmlspecialchars($old['medicamentos'] ?? '') ?></textarea>
                        </div>-->

                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="condiciones_especiales">Condiciones especiales</label>
                            <textarea class="campo__entrada" id="condiciones_especiales" name="condiciones_especiales" rows="3" maxlength="500" placeholder="Ej. Ciego de un ojo"><?= htmlspecialchars($old['condiciones_especiales'] ?? '') ?></textarea>
                        </div>-->

                        <div class="campo">
                            <label class="campo__etiqueta" for="ultima_desparasitacion">Última desparasitación</label>
                            <input class="campo__entrada" type="date" id="ultima_desparasitacion" name="ultima_desparasitacion" max="<?= date('Y-m-d') ?>" value="<?= substr(htmlspecialchars($old['ultima_desparasitacion'] ?? ''), 0, 10) ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="temperamento">Temperamento</label>
                            <textarea class="campo__entrada" id="temperamento" name="temperamento" rows="2" maxlength="100" placeholder="Ej. Dócil, juguetón, tímido"><?= htmlspecialchars($old['temperamento'] ?? '') ?></textarea>
                        </div>

                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="restricciones_para_manejo">Restricciones para manejo</label>
                            <textarea class="campo__entrada" id="restricciones_para_manejo" name="restricciones_para_manejo" rows="3" maxlength="500" placeholder="Ej. Usar bozal, no tocar las orejas"><?= htmlspecialchars($old['restricciones_para_manejo'] ?? '') ?></textarea>
                        </div>-->

                        <!--<div class="campo">
                            <label class="campo__etiqueta" for="observaciones">Observaciones</label>
                            <textarea class="campo__entrada" id="observaciones" name="observaciones" rows="3" maxlength="500" placeholder="Ej. Se pone nervioso con otros perros"><?= htmlspecialchars($old['observaciones'] ?? '') ?></textarea>
                        </div>-->
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Guardar mascota</button>
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
