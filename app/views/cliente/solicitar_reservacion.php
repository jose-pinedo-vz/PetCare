<?php
session_start();
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$errores = $_SESSION['errores'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errores'], $_SESSION['old']);

$paginaActiva = 'servicios';

require_once __DIR__ . '/../../models/hotel_cliente.php';
$mascotas = HotelCliente::mascotasDelCliente((int) $_SESSION['id_cliente']);

// Valor anterior del formulario, ya escapado
$v = function (string $clave) use ($old): string {
    return htmlspecialchars(is_string($old[$clave] ?? null) ? $old[$clave] : '');
};
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitar reservación de hotel</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>


    <main class="contenedor contenedor--medio">
        <p class="migas-de-pan"><a href="servicios.php">Servicios</a> › Hotel para mascotas</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Solicitar reservación de hotel</h1>
            <p class="tarjeta__subtitulo">Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>

            <?php if ($errores): ?>
                <div class="aviso aviso--error">
                    <?php foreach ($errores as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="../../controllers/cliente_reservar_hotel.php" method="post">
                <div class="formulario-rejilla">
                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="id_mascota">Mascota <span class="campo__obligatorio">*</span></label>
                        <select class="campo__entrada" id="id_mascota" name="id_mascota" required>
                            <option value="">Elige una mascota</option>
                            <?php foreach ($mascotas as $mascota): ?>
                                <option value="<?= (int) $mascota['id_mascota'] ?>" <?= $v('id_mascota') === (string) $mascota['id_mascota'] ? 'selected' : '' ?>><?= htmlspecialchars($mascota['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="fecha_entrada">Fecha de entrada <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="date" id="fecha_entrada" name="fecha_entrada" value="<?= $v('fecha_entrada') ?>" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="fecha_salida">Fecha de salida <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="date" id="fecha_salida" name="fecha_salida" value="<?= $v('fecha_salida') ?>" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="persona_recoge">Persona que recoge <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="text" id="persona_recoge" name="persona_recoge" minlength="3" maxlength="100" value="<?= $v('persona_recoge') ?>" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="telefono_emergencia">Teléfono de emergencia <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="tel" id="telefono_emergencia" name="telefono_emergencia" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" value="<?= $v('telefono_emergencia') ?>" required>
                    </div>

                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="servicios_adicionales">Servicios adicionales</label>
                        <textarea class="campo__entrada" id="servicios_adicionales" name="servicios_adicionales" rows="3" maxlength="1000" placeholder="Ej. baño antes de la salida"><?= $v('servicios_adicionales') ?></textarea>
                    </div>
                </div>

                <p class="aviso">El total de la estancia se paga en recepción. Si tu mascota no tiene vacunas registradas, lleva su cartilla.</p>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Enviar solicitud</button>
                    <a class="boton boton--contorno" href="servicios.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>