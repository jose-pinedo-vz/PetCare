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
                            <label class="campo__etiqueta" for="temperamento">Temperamento</label>
                            <textarea class="campo__entrada" id="temperamento" name="temperamento" rows="2" maxlength="100" placeholder="Ej. Dócil, juguetón, tímido"><?= $v('temperamento') ?></textarea>
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
                            <textarea class="campo__entrada" id="alergias" name="alergias" rows="4" placeholder="Ej. Polen, pollo, penicilina"><?= $v('alergias') ?></textarea>
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
    <script>
    // Vista previa: al elegir una foto se muestra de inmediato.
    // Todavía NO se guarda; se sube hasta dar clic en el botón del formulario.
    (function () {
        const campo = document.getElementById('fotografia');
        const original = document.querySelector('.foto-marcador--subir');
        if (!campo || !original) return;

        let vista = null;

        campo.addEventListener('change', function () {
            const archivo = campo.files[0];

            // Si quita la foto o elige algo que no es imagen, regresamos la original
            if (!archivo || !archivo.type.startsWith('image/')) {
                if (vista) { vista.replaceWith(original); vista = null; }
                return;
            }

            if (!vista) {
                vista = document.createElement('img');
                vista.className = original.className;
                vista.alt = 'Vista previa de la foto';
                vista.style.objectFit = 'cover';
                vista.style.padding = '0';
                original.replaceWith(vista);
            } else {
                URL.revokeObjectURL(vista.src);
            }
            vista.src = URL.createObjectURL(archivo);
        });
    })();
    </script>
</body>
</html>
