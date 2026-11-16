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

//la BD guarda fecha de nacimiento; el formulario pide años
$edadAnios = $old['edad'] ?? round((new DateTime($mascota['edad']))->diff(new DateTime('today'))->days / 365.25, 1);

$foto = rutaFotoMascota($mascota['fotografia']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar mascota</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
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
                            <img class="foto-marcador foto-marcador--subir" src="../../../<?= htmlspecialchars($foto) ?>" alt="Foto de <?= $v('nombre') ?>">
                        <?php else: ?>
                            <div class="foto-marcador foto-marcador--subir">Foto de la mascota</div>
                        <?php endif; ?>
                        <div class="campo">
                            <label class="campo__etiqueta" for="fotografia">Cambiar foto</label>
                            <input class="campo__entrada" type="file" id="fotografia" name="fotografia" accept="image/*">
                        </div>
                    </div>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="nombre">Nombre <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="nombre" name="nombre" placeholder="p. ej. Luna" value="<?= $v('nombre') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="especie">Especie <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="especie" name="especie" required>
                                <?php foreach (['Perro', 'Gato', 'Otro'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $v('especie') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="raza">Raza</label>
                            <input class="campo__entrada" type="text" id="raza" name="raza" placeholder="p. ej. Labrador" value="<?= $v('raza') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="sexo">Sexo <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="sexo" name="sexo" required>
                                <?php foreach (['Hembra', 'Macho'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $v('sexo') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="edad">Edad (años) <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="number" id="edad" name="edad" placeholder="p. ej. 3" min="0" step="0.1" value="<?= htmlspecialchars((string) $edadAnios) ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="peso">Peso (kg) <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="number" id="peso" name="peso" placeholder="p. ej. 24" min="0" step="0.1" value="<?= $v('peso') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="color">Color</label>
                            <input class="campo__entrada" type="text" id="color" name="color" value="<?= $v('color') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="temperamento">Temperamento</label>
                            <select class="campo__entrada" id="temperamento" name="temperamento">
                                <?php foreach (['' => 'No estoy seguro', 'Tranquilo' => 'Tranquilo', 'Juguetón' => 'Juguetón', 'Nervioso' => 'Nervioso', 'Reactivo' => 'Reactivo o agresivo'] as $valor => $texto): ?>
                                    <option value="<?= $valor ?>" <?= $v('temperamento') === $valor ? 'selected' : '' ?>><?= $texto ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="tamanio">Tamaño</label>
                            <select class="campo__entrada" id="tamanio" name="tamanio">
                                <?php foreach (['Pequeño', 'Mediano', 'Grande', 'Gigante'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $v('tamanio') === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="alergias">Alergias</label>
                            <textarea class="campo__entrada" id="alergias" name="alergias" rows="4" placeholder="Cuéntanos si hay algo que debamos saber"><?= $v('alergias') ?></textarea>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="condiciones_especiales">Condiciones especiales</label>
                            <textarea class="campo__entrada" id="condiciones_especiales" name="condiciones_especiales" rows="3"><?= $v('condiciones_especiales') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Guardar cambios</button>
                    <a class="boton boton--contorno" href="mis_mascotas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
