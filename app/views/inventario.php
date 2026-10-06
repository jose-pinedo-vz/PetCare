<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Inventario</title>
    <link rel="stylesheet" href="css/estilos_base.css">
</head>

<body>
    <header>
        <div class="marca">
            <img src="./img/logo3.svg" alt="Veterinaria PetCare">
        </div>
        <div class="sesion">
            <span>Empleado: Nombre del empleado</span>
            <a href="#">Cerrar sesión</a>
        </div>
    </header>
        <nav>
            <ul>
            <li><a href="mascotas.php" class="activo">Mascotas</a></li>
            <li><a href="citas.php">Citas</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="empleados.php">Empleados</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>

            
            <li><a href="#inventario.php" class="activo">Inventario</a></li>
            <li><a href="#" class="deshabilitado">Ventas</a></li>
            <li><a href="#" class="deshabilitado">Servicios</a></li>
            <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
            <li><a href="#" class="deshabilitado">Veterinaria</a></li>
            <li><a href="#" class="deshabilitado">Pagos</a></li>
            <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
            </ul>
        </nav>

    <main>

        <!-- 1. ENCABEZADO SUPERIOR -->
        <header style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; padding-bottom: 15px;">
            <h1 style="margin: 0; color: #333;"> Control de inventario</h1>
            
            <div style="display: flex; gap: 15px; align-items: center;">
                <input type="text" placeholder=" Buscar por código de barra..." size="40" style="padding: 8px;">
                <!-- Botón de alertas destacado -->
                <button style="background-color: #D2691E; color: white; border: none; padding: 10px 15px; border-radius: 5px; font-weight: bold; cursor: pointer;">
                    Alertas de reorden (x)
                </button>
            </div>
        </header>

        <section class="inventario-resumen">
            <!-- Tarjeta de alerta crtica -->
            <div class="inventario-tarjeta inventario-alerta">
                <h3> Riesgo de desabasto</h3>
                <p>-----------</p>
                <button type="button" class="boe" onclick="window.location.href='proveedores.php'">Proveedores</button>
            </div>
            
            <!-- Tarjetas informativas normales -->
            <div class="inventario-tarjeta">
                <h3> Total catálogo</h3>
                <p>x Productos</p>
            </div>
            
            <div class="inventario-tarjeta">
                <h3> Movimientos hoy</h3>
                <p>------------</p>
            </div>
        </section>
</main>
</body>
</html>
