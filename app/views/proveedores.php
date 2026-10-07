<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Proveedores</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
<body>
    <div class="layout">
        <?php $paginaActiva = 'proveedores'; include __DIR__ . '/layout/sidebar.php';?>

    <main>
        <div class="migaja">Gestión de Proveedores /</div>
        <div class="encabezado-pagina">
            <h1>Proveedores</h1>
        <div>
            <a class="btn" href="agregar_proveedor.php">+Agregar proveedor</a>  
        </div>
        <div>
        <table border="1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Razón social</th>
                    <th>Nombre comercial</th>
                    <th>RFC</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Domicilio</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th>Código postal</th>
                    <th>Condición de pago</th>
                    <th>Tiempo de entrega</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Distribuidora Veterinaria del Norte S.A. de C.V.</td>
                    <td>VetNorte</td>
                    <td>DVN230415AB1</td>
                    <td>4921234567</td>
                    <td>contacto@vetnorte.com</td>
                    <td>Av. Universidad 125</td>
                    <td>Zacatecas</td>
                    <td>Zacatecas</td>
                    <td>98000</td>
                    <td>Crédito a 30 días</td>
                    <td>5 días hábiles</td>
                    <td>Entrega de productos veterinarios.</td>
                    <td>
                        <a class="btn" href="editar_proveedor.php?id=1">Editar</a>
                        <a class="btn" href="eliminar_proveedor.php?id=1">Eliminar</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Productos Médicos y Veterinarios S.A. de C.V.</td>
                    <td>ProVet</td>
                    <td>PMV210820CD2</td>
                    <td>4927654321</td>
                    <td>ventas@provet.com</td>
                    <td>Calle Hidalgo 240</td>
                    <td>Guadalupe</td>
                    <td>Zacatecas</td>
                    <td>98600</td>
                    <td>Contado</td>
                    <td>3 días hábiles</td>
                    <td>Realiza entregas de medicamentos.</td>
                    <td>
                        <a class="btn" href="editar_proveedor.php?id=1">Editar</a>
                        <a class="btn" href="eliminar_proveedor.php?id=1">Eliminar</a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Comercializadora Animal del Centro S.A. de C.V.</td>
                    <td>Animal Center</td>
                    <td>CAC240312EF3</td>
                    <td>4929876543</td>
                    <td>info@animalcenter.com</td>
                    <td>Av. México 350</td>
                    <td>Fresnillo</td>
                    <td>Zacatecas</td>
                    <td>99000</td>
                    <td>Crédito a 15 días</td>
                    <td>7 días hábiles</td>
                    <td>Proveedor de alimentos y accesorios.</td>
                    <td>
                        <a class="btn" href="editar_proveedor.php?id=1">Editar</a>
                        <a class="btn" href="eliminar_proveedor.php?id=1">Eliminar</a>
                    </td>
                </tr>
            </tbody>
        </table>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>