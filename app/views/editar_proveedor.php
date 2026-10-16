<?php
require_once __DIR__ . '/../controllers/proveedor.php';
require_once __DIR__ . '/../models/proveedores.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

$idProveedor = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)
    ?: filter_input(INPUT_POST, 'id_proveedor', FILTER_VALIDATE_INT);

if ($idProveedor === false || $idProveedor === null) {
    header("Location: proveedores.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $datos = [
        'razon_social'     => trim($_POST['razon_social'] ?? ''),
        'nombre_comercial' => trim($_POST['nombre_comercial'] ?? ''),
        'rfc'              => trim($_POST['rfc'] ?? ''),
        'telefono'         => trim($_POST['telefono'] ?? ''),
        'correo'           => trim($_POST['correo'] ?? ''),
        'domicilio'        => trim($_POST['domicilio'] ?? ''),
        'ciudad'           => trim($_POST['ciudad'] ?? ''),
        'estado'           => trim($_POST['estado'] ?? ''),
        'codigo_postal'    => trim($_POST['codigo_postal'] ?? ''),
        'condicion_pago'   => trim($_POST['condicion_pago'] ?? ''),
        'tiempo_entrega'   => trim($_POST['tiempo_entrega'] ?? ''),
        'observaciones'    => trim($_POST['observaciones'] ?? '')
    ];
    actualizar_proveedor($idProveedor, $datos); 
}

$proveedor = obtenerProveedorPorId($idProveedor);

if (!$proveedor) {
    header("Location: proveedores.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Veterinaria PetCare - Editar proveedores</title>
  <link rel="stylesheet" href="css/estilos_base.css">
</head>

<body>

    <header>
        <div class="marca">
            <img src="./img/logo3.svg" alt="Veterinaria PetCare">
        </div>

        <div>
            <span>Empleado: Nombre de empleado</span>
            <a href="#">Cerrar sesión</a>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="mascotas.php">Mascotas</a></li>
            <li><a href="citas.php">Citas</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="empleados.php">Empleados</a></li>
            <li><a href="proveedores.php" class="activo">Proveedores</a></li>

            <li><a href="inventario.php">Inventario</a></li>
            <li><a href="#" class="deshabilitado">Ventas</a></li>
            <li><a href="#" class="deshabilitado">Servicios</a></li>
            <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
            <li><a href="#" class="deshabilitado">Veterinaria</a></li>
            <li><a href="#" class="deshabilitado">Pagos</a></li>
            <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
        </ul>
    </nav>

    <main>

        <h1>Editar proveedor</h1>

        <?php if (!empty($errores)): ?>
            <div class="alerta-errores">
              <h4>Por favor corrige los siguientes errores:</h4>
              <ul>
                <?php foreach ($errores as $error): ?>
                  <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
        <?php endif; ?>

        <form action="editar_proveedor.php" method="POST">

            <input type="hidden" name="id_proveedor" value="<?= (int)$proveedor['id_proveedor'] ?>">

            <fieldset>

                <legend>Datos generales</legend>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                    <div>
                        <label class="label-campo" for="razon_social">
                            Razón social *:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="razon_social"
                            name="razon_social"
                            maxlength="100"
                            value="<?= htmlspecialchars($proveedor['razon_social']) ?>"
                            required>
                    </div>

                    <div>
                        <label class="label-campo" for="nombre_comercial">
                            Nombre Comercial *:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="nombre_comercial"
                            name="nombre_comercial"
                            maxlength="100"
                            value="<?= htmlspecialchars($proveedor['nombre_comercial']) ?>"
                            required>
                    </div>

                    <div>
                        <label class="label-campo" for="rfc">
                            RFC *:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="rfc"
                            name="rfc"
                            maxlength="13"
                            value="<?= htmlspecialchars($proveedor['rfc']) ?>"
                            required>
                    </div>

                </div>

            </fieldset>

            <fieldset>

                <legend>Datos de contacto</legend>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                    <div>
                        <label class="label-campo" for="telefono">
                            Teléfono *:
                        </label>

                        <input
                            type="tel"
                            class="campo"
                            id="telefono"
                            name="telefono"
                            maxlength="15"
                            value="<?= htmlspecialchars($proveedor['telefono']) ?>"
                            required>
                    </div>

                    <div>
                        <label class="label-campo" for="correo">
                            Correo Electrónico:
                        </label>

                        <input
                            type="email"
                            class="campo"
                            id="correo"
                            name="correo"
                            maxlength="100"
                            value="<?= htmlspecialchars($proveedor['correo'] ?? '') ?>">
                    </div>

                </div>

            </fieldset>

            <fieldset>

                <legend>Datos de ubicación</legend>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                    <div>
                        <label class="label-campo" for="domicilio">
                            Domicilio:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="domicilio"
                            name="domicilio"
                            maxlength="100"
                            value="<?= htmlspecialchars($proveedor['domicilio'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="label-campo" for="ciudad">
                            Ciudad:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="ciudad"
                            name="ciudad"
                            maxlength="60"
                            value="<?= htmlspecialchars($proveedor['ciudad'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="label-campo" for="estado">
                            Estado:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="estado"
                            name="estado"
                            maxlength="60"
                            value="<?= htmlspecialchars($proveedor['estado'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="label-campo" for="codigo_postal">
                            Código Postal:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="codigo_postal"
                            name="codigo_postal"
                            maxlength="10"
                            value="<?= htmlspecialchars($proveedor['codigo_postal'] ?? '') ?>">
                    </div>

                </div>

            </fieldset>

            <fieldset>

                <legend>Información comercial</legend>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">

                    <div>
                        <label class="label-campo" for="condicion_pago">
                            Condición de pago:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="condicion_pago"
                            name="condicion_pago"
                            maxlength="60"
                            value="<?= htmlspecialchars($proveedor['condicion_pago'] ?? '') ?>">
                    </div>

                    <div>
                        <label class="label-campo" for="tiempo_entrega">
                            Tiempo de entrega *:
                        </label>

                        <input
                            type="text"
                            class="campo"
                            id="tiempo_entrega"
                            name="tiempo_entrega"
                            maxlength="60"
                            value="<?= htmlspecialchars((string)$proveedor['tiempo_entrega']) ?>"
                            required>
                    </div>

                    <div style="grid-column: 1 / -1;">
                        <label class="label-campo" for="observaciones">
                            Observaciones:
                        </label>

                        <textarea
                            class="campo"
                            id="observaciones"
                            name="observaciones"
                            rows="3"
                            maxlength="255"><?= htmlspecialchars($proveedor['observaciones'] ?? '') ?></textarea>
                    </div>

                </div>

            </fieldset>

            <button type="submit">Guardar cambios</button>
            <a class="btn" href="proveedores.php">Cancelar</a>

        </form>

    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>

</body>
</html>