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
            <img src="img/logo3.svg" alt="Veterinaria PetCare">
        </div>
        <div class="sesion">
            <span>Empleado: Nombre del empleado</span>
            <a href="#">Cerrar sesión</a>
        </div>
    </header>

    <nav>
        <ul>
            <li><a href="mascotas.php">Mascotas</a></li>
            <li><a href="citas.php">Citas</a></li>
            <li><a href="clientes.php">Clientes</a></li>
            <li><a href="empleados.php">Empleados</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li><a href="inventario.php" class="activo">Inventario</a></li>
            <li><a href="#" class="deshabilitado">Ventas</a></li>
            <li><a href="#" class="deshabilitado">Servicios</a></li>
            <li><a href="#" class="deshabilitado">Adopción y venta</a></li>
            <li><a href="#" class="deshabilitado">Veterinaria</a></li>
            <li><a href="#" class="deshabilitado">Pagos</a></li>
            <li><a href="#" class="deshabilitado">Pagos con tarjeta</a></li>
        </ul>
    </nav>

    <main>
        <div class="inventario-header">
            <h1>Control de inventario</h1>

            
        </div>

        <section class="inventario-resumen">
            <div class="inventario-tarjeta">
                <h3>Riesgo de desabasto</h3>
                <p>-----------</p>
                <button class="btn inventario-btn-contactar" type="button" data-abrir-agotados>detalles</button>
            </div>

            <div class="inventario-tarjeta">
                <h3>Total catálogo</h3>
                <p>x Productos</p>
            </div>

            <div class="inventario-tarjeta">
                <h3>Movimientos hoy</h3>
                <p>------------</p>
            </div>
        </section>

        <!-- productos -->
        <header>
        <h1>Productos</h1>
        <div style="display: flex; gap: 15px; align-items: center;">
                <input type="text" placeholder=" Buscar por código de barra..." size="40" style="padding: 8px;">
                
            </div>
        </header>
        <br>
        <div style="margin-bottom: 20px;">
            <a class="btn" href="agregar_producto.php">+Agregar producto</a>  
            
        </div>
        
        <div style="overflow-x: auto; border: 1px solid var(--border-light); border-radius: 6px;">
        <table border="1" style="width: 100%; min-width: 1400px; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Codigo de barra</th>
                    <th>Nombre</th>
                    <th>Existencia</th>
                    <th>Precio venta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><img src="ruta/a/imagen1.jpg" alt="Producto 1" style="width: 100px; height: auto;"></td>
                    <td>1234567890</td>
                    <td>Pelota</td>
                    <td>2 (bajo)</td>
                    <td>30.50</td>
                    <td style="padding: 15px; white-space: nowrap;">
                        <a class="btn" href="enespera.php?id=1" style="margin-left: 3px";>Pedir</a>
                        <a class="btn" href="editar_producto.php?id=1" style="margin-left: 3px;">Editar</a>
                        <a class="btn" href="enespera.php?id=1" style="margin-left: 3px">Eliminar</a>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </main>

    

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>

    <dialog id="ventana-agotados" class="inventario-dialog" aria-labelledby="titulo-agotados">
        <h2 id="titulo-agotados">Productos agotados</h2>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Existencia</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pelota</td>
                        <td>2 (bajo)</td>
                        <td style="padding: 15px; white-space: nowrap;">
                            <a class="btn" href="enespera.php?id=1">Pedir</a>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
        <div class="inventario-dialog-acciones">
            <button id="cerrar-agotados" class="btn" type="button">Cerrar</button>
        </div>
    </dialog>

    <script>
        const ventanaAgotados = document.getElementById('ventana-agotados');
        document.querySelectorAll('[data-abrir-agotados]').forEach((boton) => {
            boton.addEventListener('click', () => ventanaAgotados.showModal());
        });
        document.getElementById('cerrar-agotados').addEventListener('click', () => ventanaAgotados.close());
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && ventanaAgotados.open) ventanaAgotados.close();
        });
    </script>
</body>
</html>

