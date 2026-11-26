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

// Contador para la portada
$totalMascotas = count(Mascota::obtenerPorCliente($idCliente));

// Valor de un campo del formulario: lo escrito (si hubo error) o lo de la base de datos
function valorPerfil(array $old, array $cliente, string $campo): string
{
    return htmlspecialchars((string) ($old[$campo] ?? $cliente[$campo] ?? ''));
}

// Dato para el modo lectura, con texto por defecto si viene vacío
function datoPerfil(array $cliente, string $campo): string
{
    $valor = trim((string) ($cliente[$campo] ?? ''));
    return $valor === '' ? '<span class="perfil-datos__vacio">Sin registrar</span>' : htmlspecialchars($valor);
}

$nombreCompleto = trim($cliente['nombre'] . ' ' . $cliente['apellido']);
$inicial = mb_strtoupper(mb_substr($cliente['nombre'], 0, 1));

// Fecha de nacimiento para leer: 25/11/1990
$fechaNacimientoTexto = '';
if (!empty($cliente['fecha_nacimiento'])) {
    $fechaNacimientoTexto = date('d/m/Y', strtotime($cliente['fecha_nacimiento']));
}

// Dirección en una sola línea: Calle 12 Int. 3, Col. San Francisco, C.P. 99700, Tlaltenango, Zacatecas
$calleNumero = trim(($cliente['calle'] ?? '') . ' ' . ($cliente['numero_exterior'] ?? ''));
if (!empty($cliente['numero_interior'])) {
    $calleNumero .= ' Int. ' . $cliente['numero_interior'];
}
$partesDireccion = array_filter([
    $calleNumero,
    !empty($cliente['colonia']) ? 'Col. ' . $cliente['colonia'] : '',
    !empty($cliente['codigo_postal']) ? 'C.P. ' . $cliente['codigo_postal'] : '',
    $cliente['ciudad'] ?? '',
    $cliente['estado'] ?? '',
], fn($parte) => trim((string) $parte) !== '');
$direccionTexto = implode(', ', $partesDireccion);

// Fecha mínima para ser mayor de edad
$fechaMaxima = date('Y-m-d', strtotime('-18 years'));

// Reglas de los inputs que se repiten
$soloLetras = 'pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+" title="Solo letras y espacios, sin números ni símbolos"';
$diezDigitos = 'inputmode="numeric" minlength="10" maxlength="10" pattern="[0-9]{10}" title="Escribe 10 dígitos, solo números" placeholder="Ej. 3312345678" oninput="this.value = this.value.replace(/[^0-9]/g, \'\')"';
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
                        <dt>Usuario</dt>
                        <dd><?= datoPerfil($cliente, 'usuario') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Fecha de nacimiento</dt>
                        <dd><?= $fechaNacimientoTexto !== '' ? htmlspecialchars($fechaNacimientoTexto) : datoPerfil($cliente, 'fecha_nacimiento') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Sexo</dt>
                        <dd><?= datoPerfil($cliente, 'sexo') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>RFC</dt>
                        <dd><?= datoPerfil($cliente, 'rfc') ?></dd>
                    </div>
                </dl>

                <h3 class="perfil-seccion__subtitulo">Contacto</h3>
                <dl class="perfil-datos">
                    <div class="perfil-datos__fila">
                        <dt>Teléfono</dt>
                        <dd><?= datoPerfil($cliente, 'telefono') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Teléfono alternativo</dt>
                        <dd><?= datoPerfil($cliente, 'telefono_alternativo') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Correo</dt>
                        <dd><?= datoPerfil($cliente, 'correo') ?></dd>
                    </div>
                    <div class="perfil-datos__fila">
                        <dt>Dirección</dt>
                        <dd><?= $direccionTexto !== '' ? htmlspecialchars($direccionTexto) : '<span class="perfil-datos__vacio">Sin registrar</span>' ?></dd>
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

                    <!-- Datos personales -->
                    <h3 class="perfil-seccion__subtitulo">Datos personales</h3>
                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="nombre">Nombres <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="nombre" name="nombre" maxlength="50" <?= $soloLetras ?> placeholder="Ej. María" value="<?= valorPerfil($old, $cliente, 'nombre') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="apellido">Apellidos <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="apellido" name="apellido" maxlength="100" <?= $soloLetras ?> placeholder="Ej. López García" value="<?= valorPerfil($old, $cliente, 'apellido') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="usuario">Usuario <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="usuario" name="usuario" maxlength="30" pattern="[^ ]+" title="Sin espacios" placeholder="Ej. maria.lopez" value="<?= valorPerfil($old, $cliente, 'usuario') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="fecha_nacimiento">Fecha de nacimiento</label>
                            <input class="campo__entrada" type="date" id="fecha_nacimiento" name="fecha_nacimiento" max="<?= $fechaMaxima ?>" value="<?= substr(valorPerfil($old, $cliente, 'fecha_nacimiento'), 0, 10) ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="sexo">Sexo</label>
                            <?php $sexoActual = $old['sexo'] ?? $cliente['sexo'] ?? ''; ?>
                            <select class="campo__entrada" id="sexo" name="sexo">
                                <option value="">Prefiero no decirlo</option>
                                <?php foreach (['Femenino', 'Masculino', 'Otro'] as $opcion): ?>
                                    <option value="<?= $opcion ?>" <?= $sexoActual === $opcion ? 'selected' : '' ?>><?= $opcion ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="rfc">RFC</label>
                            <input class="campo__entrada" type="text" id="rfc" name="rfc" minlength="12" maxlength="13" pattern="[A-Za-zÑñ&]{3,4}[0-9]{6}[A-Za-z0-9]{3}" title="12 o 13 caracteres, ej. LOGM900101AB1" placeholder="Ej. LOGM900101AB1" style="text-transform: uppercase" value="<?= valorPerfil($old, $cliente, 'rfc') ?>">
                        </div>
                    </div>

                    <!-- Contacto -->
                    <h3 class="perfil-seccion__subtitulo">Contacto</h3>
                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="telefono">Teléfono <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="telefono" name="telefono" <?= $diezDigitos ?> value="<?= valorPerfil($old, $cliente, 'telefono') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="telefono_alternativo">Teléfono alternativo</label>
                            <input class="campo__entrada" type="tel" id="telefono_alternativo" name="telefono_alternativo" <?= $diezDigitos ?> value="<?= valorPerfil($old, $cliente, 'telefono_alternativo') ?>">
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="correo">Correo <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="email" id="correo" name="correo" maxlength="100" placeholder="Ej. maria@correo.com" value="<?= valorPerfil($old, $cliente, 'correo') ?>" required>
                        </div>
                    </div>

                    <!-- Dirección -->
                    <h3 class="perfil-seccion__subtitulo">Dirección (opcional)</h3>
                    <div class="formulario-rejilla">
                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="calle">Calle</label>
                            <input class="campo__entrada" type="text" id="calle" name="calle" maxlength="100" placeholder="Ej. Av. Juárez" value="<?= valorPerfil($old, $cliente, 'calle') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="numero_exterior">Número exterior</label>
                            <input class="campo__entrada" type="text" id="numero_exterior" name="numero_exterior" maxlength="15" pattern="[A-Za-z0-9.\-\/ ]+" title="Números, letras, guiones y diagonal" placeholder="Ej. 123" value="<?= valorPerfil($old, $cliente, 'numero_exterior') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="numero_interior">Número interior</label>
                            <input class="campo__entrada" type="text" id="numero_interior" name="numero_interior" maxlength="15" pattern="[A-Za-z0-9.\-\/ ]+" title="Números, letras, guiones y diagonal" placeholder="Ej. 4-B" value="<?= valorPerfil($old, $cliente, 'numero_interior') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="codigo_postal">Código postal</label>
                            <input class="campo__entrada" type="text" id="codigo_postal" name="codigo_postal" inputmode="numeric" maxlength="5" pattern="[0-9]{5}" title="5 dígitos" placeholder="Ej. 99700" oninput="this.value = this.value.replace(/[^0-9]/g, '')" value="<?= valorPerfil($old, $cliente, 'codigo_postal') ?>">
                            <small class="campo__ayuda" id="cp_mensaje"></small>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="colonia">Colonia</label>
                            <input class="campo__entrada" type="text" id="colonia" name="colonia" maxlength="60" list="lista_colonias" placeholder="Ej. San Francisco" value="<?= valorPerfil($old, $cliente, 'colonia') ?>">
                            <datalist id="lista_colonias"></datalist>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="ciudad">Ciudad</label>
                            <input class="campo__entrada" type="text" id="ciudad" name="ciudad" maxlength="60" placeholder="Ej. Tlaltenango" value="<?= valorPerfil($old, $cliente, 'ciudad') ?>">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="estado">Estado</label>
                            <input class="campo__entrada" type="text" id="estado" name="estado" maxlength="60" placeholder="Ej. Zacatecas" value="<?= valorPerfil($old, $cliente, 'estado') ?>">
                        </div>
                    </div>

                    <!-- Contacto de emergencia -->
                    <h3 class="perfil-seccion__subtitulo">Contacto de emergencia</h3>
                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="contacto_emergencia">Nombre <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="contacto_emergencia" name="contacto_emergencia" maxlength="100" <?= $soloLetras ?> placeholder="Ej. María López" value="<?= valorPerfil($old, $cliente, 'contacto_emergencia') ?>" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="telefono_emergencia">Teléfono de emergencia <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="tel" id="telefono_emergencia" name="telefono_emergencia" <?= $diezDigitos ?> value="<?= valorPerfil($old, $cliente, 'telefono_emergencia') ?>" required>
                        </div>
                    </div>

                    <div class="grupo-botones">
                        <button class="boton" type="submit">Guardar cambios</button>
                        <a class="boton boton--contorno" href="mi_perfil.php">Cancelar</a>
                    </div>
                </form>

                <script>
                // Al escribir los 5 dígitos del código postal se buscan colonia, ciudad y estado
                const cp = document.getElementById('codigo_postal');
                const cpMensaje = document.getElementById('cp_mensaje');

                cp.addEventListener('input', async () => {
                    cpMensaje.textContent = '';
                    if (cp.value.length !== 5) return;

                    const datos = new FormData();
                    datos.append('accion', 'codigo_postal');
                    datos.append('codigo_postal', cp.value);

                    try {
                        const respuesta = await fetch('../../controllers/cliente_actualizar_perfil.php', { method: 'POST', body: datos });
                        const lugares = await respuesta.json();

                        if (lugares.length === 0) {
                            cpMensaje.textContent = 'No encontramos ese código postal. Escribe los datos a mano.';
                            return;
                        }

                        document.getElementById('ciudad').value = lugares[0].ciudad || lugares[0].municipio;
                        document.getElementById('estado').value = lugares[0].estado;

                        const lista = document.getElementById('lista_colonias');
                        lista.innerHTML = '';
                        lugares.forEach(lugar => {
                            const opcion = document.createElement('option');
                            opcion.value = lugar.colonia;
                            lista.appendChild(opcion);
                        });
                        document.getElementById('colonia').value = lugares.length === 1 ? lugares[0].colonia : '';
                        cpMensaje.textContent = lugares.length > 1 ? 'Elige tu colonia de la lista.' : '';
                    } catch (error) {
                        cpMensaje.textContent = 'No se pudo buscar el código postal. Escribe los datos a mano.';
                    }
                });
                </script>

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
