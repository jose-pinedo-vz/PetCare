<?php
session_start();

// Página privada: si no hay sesión de cliente, al login
if (empty($_SESSION['id_cliente'])) {
    header('Location: iniciar_sesion.php');
    exit;
}
$paginaActiva = '';

require_once __DIR__ . '/../../models/Cliente.php';
require_once __DIR__ . '/../../models/Mascota.php';

// El cliente SIEMPRE sale de la sesión
$idCliente = (int) $_SESSION['id_cliente'];
$cliente = Cliente::obtenerPorId($idCliente);

// Si no se pudieron leer los datos (cuenta borrada o falla de la base), lo mandamos a inicio
if (!$cliente) {
    header('Location: inicio.php');
    exit;
}

// Mensajes que manda el controlador
$errores = $_SESSION['perfil_errores'] ?? [];
$exito   = $_SESSION['perfil_exito'] ?? '';
$old     = $_SESSION['perfil_old'] ?? [];
unset($_SESSION['perfil_errores'], $_SESSION['perfil_exito'], $_SESSION['perfil_old']);

// Modo edición: mi_perfil.php?editar=1
$editando = isset($_GET['editar']);

// Contadores para la portada
$totalMascotas = count(Mascota::obtenerPorCliente($idCliente));

// Si hay datos escritos (por un error) se usan esos; si no, los de la base de datos
function valorPerfil(array $old, string $campoFormulario, array $cliente, string $campoBD): string
{
    return htmlspecialchars((string) ($old[$campoFormulario] ?? $cliente[$campoBD] ?? ''));
}

// Dato para el modo lectura, con texto por defecto si viene vacío
function datoPerfil(array $cliente, string $campo): string
{
    $valor = trim((string) ($cliente[$campo] ?? ''));
    return $valor === '' ? '<span class="perfil-datos__vacio">Sin registrar</span>' : htmlspecialchars($valor);
}

$nombreCompleto = trim($cliente['nombre'] . ' ' . $cliente['apellido']);
$inicial = mb_strtoupper(mb_substr($cliente['nombre'], 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi perfil</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor contenedor--medio">

        <!-- Portada del perfil -->
        <section class="perfil-portada">
            <div class="perfil-portada__avatar" aria-hidden="true"><?= htmlspecialchars($inicial) ?></div>

            <div class="perfil-portada__info">
                <h1 class="perfil-portada__nombre"><?= htmlspecialchars($nombreCompleto) ?></h1>
                <p class="perfil-portada__usuario"><?= htmlspecialchars($cliente['usuario'] ?? '') ?></p>
            </div>

            <div class="perfil-portada__numeros">
                <a class="perfil-numero" href="mis_mascotas.php">
                    <span class="perfil-numero__valor"><?= $totalMascotas ?></span>
                    <span class="perfil-numero__texto"><?= $totalMascotas === 1 ? 'Mascota' : 'Mascotas' ?></span>
                </a>
            </div>
        </section>

        <!-- Mensajes -->
        <?php if ($exito !== ''): ?>
            <div class="aviso aviso--exito"><p><?= htmlspecialchars($exito) ?></p></div>
        <?php endif; ?>

        <?php if ($errores): ?>
            <div class="aviso aviso--error">
                <?php foreach ($errores as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Datos personales: modo lectura o modo edición -->
        <section class="tarjeta perfil-seccion">
            <div class="perfil-seccion__encabezado">
                <div>
                    <h2 class="perfil-seccion__titulo">Información personal</h2>
                    <p class="perfil-seccion__texto">Así te contactamos para tus citas.</p>
                </div>
                <?php if (!$editando): ?>
                    <a class="boton boton--contorno boton--chico" href="mi_perfil.php?editar=1">Editar datos</a>
                <?php endif; ?>
            </div>

            <?php if (!$editando): ?>

                <dl class="perfil-datos">
                    <div class="perfil-datos__fila">
                        <dt>Nombre</dt>
                        <dd><?= htmlspecialchars($nombreCompleto) ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Teléfono</dt>
                        <dd><?= datoPerfil($cliente, 'telefono') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Correo</dt>
                        <dd><?= datoPerfil($cliente, 'correo') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Dirección</dt>
                        <dd><?= datoPerfil($cliente, 'calle') ?></dd>
                    </div>
                </dl>

                <h3 class="perfil-seccion__subtitulo">Contacto de emergencia</h3>
                <dl class="perfil-datos">
                    <div class="perfil-datos__fila">
                        <dt>Nombre</dt>
                        <dd><?= datoPerfil($cliente, 'contacto_emergencia') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Teléfono</dt>
                        <dd><?= datoPerfil($cliente, 'telefono_emergencia') ?></dd>
                    </div>
                </dl>

            <?php else: ?>

                <form action="../../controllers/cliente_actualizar_perfil.php" method="post">
                    <input type="hidden" name="accion" value="datos">

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="nombres">Nombres <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="nombres" name="nombres" maxlength="50" value="<?= valorPerfil($old, 'nombres', $cliente, 'nombre') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="apellidos">Apellidos <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="apellidos" name="apellidos" maxlength="100" value="<?= valorPerfil($old, 'apellidos', $cliente, 'apellido') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="telefono">Teléfono <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="telefono" name="telefono" inputmode="numeric" minlength="10" maxlength="10" pattern="[0-9]{10}" title="Escribe 10 dígitos, solo números" placeholder="Ej. 3312345678" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="<?= valorPerfil($old, 'telefono', $cliente, 'telefono') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="correo">Correo <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="email" id="correo" name="correo" maxlength="100" value="<?= valorPerfil($old, 'correo', $cliente, 'correo') ?>" required>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="direccion">Dirección (opcional)</label>
                            <input class="campo__entrada" type="text" id="direccion" name="direccion" maxlength="100" value="<?= valorPerfil($old, 'direccion', $cliente, 'calle') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="emergencia_nombre">Contacto de emergencia <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="emergencia_nombre" name="emergencia_nombre" maxlength="100" value="<?= valorPerfil($old, 'emergencia_nombre', $cliente, 'contacto_emergencia') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="emergencia_telefono">Teléfono de emergencia <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="emergencia_telefono" name="emergencia_telefono" inputmode="numeric" minlength="10" maxlength="10" pattern="[0-9]{10}" title="Escribe 10 dígitos, solo números" placeholder="Ej. 3312345678" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="<?= valorPerfil($old, 'emergencia_telefono', $cliente, 'telefono_emergencia') ?>" required>
                        </div>
                    </div>

                    <div class="grupo-botones">
                        <button class="boton" type="submit">Guardar cambios</button>
                        <a class="boton boton--contorno" href="mi_perfil.php">Cancelar</a>
                    </div>
                </form>

            <?php endif; ?>
        </section>

        <!-- Seguridad: cambiar contraseña -->
        <section class="tarjeta perfil-seccion" id="seguridad">
            <div class="perfil-seccion__encabezado">
                <div>
                    <h2 class="perfil-seccion__titulo">Seguridad</h2>
                    <p class="perfil-seccion__texto">Cambia tu contraseña. Debe tener mínimo 8 caracteres.</p>
                </div>
            </div>

            <form action="../../controllers/cliente_actualizar_perfil.php" method="post">
                <input type="hidden" name="accion" value="contrasena">

                <div class="formulario-rejilla">
                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="contrasena_actual">Contraseña actual <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="password" id="contrasena_actual" name="contrasena_actual" autocomplete="current-password" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="contrasena_nueva">Nueva contraseña <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="password" id="contrasena_nueva" name="contrasena_nueva" minlength="8" autocomplete="new-password" required>
                    </div>

                    <div class="campo">
                        <label class="campo__etiqueta" for="confirmar_contrasena">Confirmar nueva contraseña <span class="campo__obligatorio">*</span></label>
                        <input class="campo__entrada" type="password" id="confirmar_contrasena" name="confirmar_contrasena" minlength="8" autocomplete="new-password" required>
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton boton--secundario" type="submit">Cambiar contraseña</button>
                </div>
            </form>
        </section>

        <!-- Cerrar sesión -->
        <section class="tarjeta perfil-seccion perfil-seccion--salir">
            <div>
                <h2 class="perfil-seccion__titulo">Cerrar sesión</h2>
                <p class="perfil-seccion__texto">Sal de tu cuenta en este dispositivo.</p>
            </div>
            <a class="boton boton--contorno" href="../../controllers/cliente_logout.php">Salir</a>
        </section>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
