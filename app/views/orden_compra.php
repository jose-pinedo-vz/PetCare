<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    session_start();
    require_once __DIR__ . "/../models/Productos.php";

    $productoSeleccionado = null;
    $id_proveedor = null;

    if (isset($_GET['id']) && $_GET['id'] != "") {
        $id_producto = (int) $_GET['id'];
        $productoSeleccionado = Productos::obtenerProductoPorId($id_producto);
    }

    if ($productoSeleccionado != null && isset($productoSeleccionado['id_proveedor'])) {
        $id_proveedor = (int) $productoSeleccionado['id_proveedor'];

    } elseif (isset($_GET['proveedor']) && $_GET['proveedor'] != "") {
        $id_proveedor = (int) $_GET['proveedor'];
    }

    if (!isset($_SESSION['orden_proveedor']) || $_SESSION['orden_proveedor'] !== $id_proveedor) {
        $_SESSION['orden_proveedor'] = $id_proveedor;
        $_SESSION['orden_productos'] = [];
        $_SESSION['orden_cantidades'] = [];
    }

    if (isset($_GET['cancelar'])) {
        $_SESSION['orden_productos'] = [];
        $_SESSION['orden_cantidades'] = [];
        header("Location: inventario.php");
        exit;
    }

    if (isset($_GET['quitar'])) {
        $quitar = (int) $_GET['quitar'];
        unset($_SESSION['orden_productos'][$quitar]);
        unset($_SESSION['orden_cantidades'][$quitar]);
        header("Location: orden_compra.php?proveedor=" . $id_proveedor);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cantidad'])) {
        foreach ($_POST['cantidad'] as $id => $cant) {
            $id = (int) $id;
            if (isset($_SESSION['orden_productos'][$id])) {
                $_SESSION['orden_cantidades'][$id] = max(1, (int) $cant);
            }
        }
        header("Location: orden_compra.php?proveedor=" . $id_proveedor);
        exit;
    }

    if ($productoSeleccionado != null) {
        $id_prod = $productoSeleccionado['id_producto'];
        $_SESSION['orden_productos'][$id_prod] = $productoSeleccionado;
        if (!isset($_SESSION['orden_cantidades'][$id_prod])) {
            $_SESSION['orden_cantidades'][$id_prod] = 1;
        }
        header("Location:orden_compra.php?proveedor=" . $id_proveedor);
        exit;
    } 

    $productosOrden = $_SESSION['orden_productos'];

    $diasEntrega = 0;
    $productosProveedor = [];
    if ($id_proveedor != null) {
        $productosProveedor = Productos::obtenerProductosPorProveedor($id_proveedor);
        $diasEntrega = Productos::obtenerTiempoEntregaProveedor($id_proveedor);
    }

    $fechaEmision = date('Y-m-d');
    $fechaEntrega = date('Y-m-d', strtotime("+$diasEntrega days"));

    $total = 0;
    foreach ($productosOrden as $id_prod => $p) {
        $cant = $_SESSION['orden_cantidades'][$id_prod] ?? 1;
        $total += Productos::obtenerPrecioCompra((int) $id_prod) * $cant;
    }

    $existencia_Minima = Productos::obtenerExistenciaMinima($id_proveedor);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Orden de Compra</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
    <div class="layout">
    <?php $paginaActiva = 'inventario'; include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja"><a href="inventario.php" class="link-tenue">Gestión de inventario</a> / Orden de compra</div>
            <div class="encabezado-pagina">
                <h1>Orden de compra</h1>
            </div>

            <div class="tarjeta">
                <h2 class="subtitulo">Detalles de la orden</h2>

                <div class="orden-compra-detalles">
                    <p><strong>Proveedor:</strong>
                        <?php
                            if ($id_proveedor != null) {
                                $nombreProveedor = Productos::obtenerNombreProveedor($id_proveedor);

                                if ($nombreProveedor != null) {
                                    echo htmlspecialchars($nombreProveedor);
                                } else {
                                    echo "Sin proveedor asignado";
                                }
                            } else {
                                echo "Sin proveedor asignado";
                            }
                        ?>
                    </p>
                </div>

                <section class="orden-compra-seccion" aria-labelledby="titulo-sugeridos">
                    <h2 id="titulo-sugeridos" class="subtitulo">Productos sugeridos</h2>
                    <p class="orden-compra-ayuda">Productos con existencias bajas generales</p>
                    <div class="orden-compra-sugerencias">
                        <?php if (count($existencia_Minima) > 0) { ?>
                            <?php foreach ($existencia_Minima as $exis_mini) { ?>
                                <article class="orden-compra-sugerencia">
                                    <div>
                                        <span class="orden-compra-etiqueta">Stock bajo</span>
                                        <h3> <?php echo htmlspecialchars($exis_mini['nombre']); ?> </h3>
                                        <p> Codigo: <?php echo htmlspecialchars($exis_mini['codigo']); ?> · Existencia: <?php echo htmlspecialchars($exis_mini['existencia']); ?> <?php echo htmlspecialchars($exis_mini['unidad_medida']) ?>; · Minimo: <?php echo htmlspecialchars($exis_mini['existencia_minima']) ?>  <?php echo htmlspecialchars($exis_mini['unidad_medida']); ?></p>
                                    </div>
                                    <a class="btn btn-chico" href= "orden_compra.php?proveedor=<?php echo $id_proveedor; ?>&id=<?php echo (int) $exis_mini['id_producto']; ?>">Agregar</a>
                                </article>
                            <?php } ?>
                        <?php } else { ?>
                            <p> No hay productos con stock bajo en este momento </p>
                        <?php } ?>
                    </div>
                </section>

                <section class="orden-compra-seccion" aria-labelledby="titulo-catalogo-proveedor">
                    <h2 id="titulo-catalogo-proveedor" class="subtitulo">Productos vinculados al proveedor</h2>
                    <div class="tabla-envoltura">
                        <table class="orden-compra-tabla-catalogo">
                            <thead>
                                <tr>
                                    <th>Acción</th>
                                    <th>Imagen</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Existencia</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($productosProveedor) > 0) {?>
                                    <?php foreach ($productosProveedor as $prod) { ?>

                                <tr>
                                    <td><a class="btn btn-chico" href= "orden_compra.php?proveedor=<?php echo $id_proveedor; ?>&id=<?php echo (int) $prod['id_producto']; ?>">Agregar</a></td>
                                    <td><img class="producto-imagen" src="../../<?php echo htmlspecialchars($prod['imagen']); ?>" alt="<?php echo htmlspecialchars($prod['nombre']); ?>"></td>
                                    <td><?php echo htmlspecialchars($prod['codigo'])?></td>
                                    <td><?php echo htmlspecialchars($prod['nombre'])?></td>
                                    <td><?php echo htmlspecialchars($prod['existencia'])?></td>
                                </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr> 
                                    <td colspan="5">Este proveedor no tiene productos registrados</td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="orden-compra-seccion" aria-labelledby="titulo-productos-seleccionados">
                    <h2 id="titulo-productos-seleccionados" class="subtitulo">Productos seleccionados para la orden</h2>
                    <form method="post" action="orden_compra.php?proveedor=<?php echo $id_proveedor; ?>">
                        <div class="tabla-envoltura">
                            <table class="orden-compra-tabla-seleccionados">
                                <thead>
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Código</th>
                                        <th>Producto</th>
                                        <th>Contenido / unidad</th>
                                        <th>Cantidad editable</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($productosOrden) > 0) { ?>
                                        <?php foreach ($productosOrden as $id_prod => $prodSelec) { ?>
                                            <tr>
                                                <td><img class="producto-imagen "src="../../<?php echo htmlspecialchars($prodSelec['imagen']); ?>" alt="<?php echo htmlspecialchars($prodSelec['nombre']); ?>"></td>
                                                <td><?php echo htmlspecialchars($prodSelec['codigo']); ?></td>
                                                <td><?php echo htmlspecialchars($prodSelec['nombre']); ?></td>
                                                <td><?php echo htmlspecialchars($prodSelec['contenido'] . ' ' . $prodSelec['unidad_medida']); ?></td>
                                                <td><input type="number" min="1" name="cantidad[<?php echo (int) $id_prod; ?>]" value="<?php echo (int) ($_SESSION['orden_cantidades'][$id_prod] ?? 1); ?>"></td>
                                                <td class="acciones">
                                                <a class="btn btn-chico btn-peligro" href="orden_compra.php?proveedor=<?php echo $id_proveedor; ?>&quitar=<?php echo (int) $id_prod; ?>">Eliminar</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="6">No hay ningun producto seleccionado para esta orden</td>
                                        </tr>
                                    <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($productosOrden) > 0) { ?>
                            <button class="btn btn-chico" type="submit">Actualizar cantidades</button>
                        <?php } ?>
                    </form>
                </section>

                <div class="orden-compra-detalles">
                    <p><strong>Fecha de emisión:</strong> <?php echo $fechaEmision; ?></p>
                    <p><strong>Fecha de entrega:</strong> <?php echo $fechaEntrega; ?></p>
                </div>

                <div class="orden-compra-acciones">
                    <a class="btn btn-peligro btn-chico" href="orden_compra.php?proveedor=<?php echo $id_proveedor; ?>&cancelar=1">Cancelar orden</a>

                    <?php if (count($productosOrden) > 0 ) { ?>
                        <form method= "POST" action= "../controllers/enviar_correo.php" style= "display:inline">
                            <button class= "btn btn-chico" type= "submit">Confirmar orden</button>
                        </form> 
                    <?php } ?>
                </div>

            </div>

        </main>
</body>

</html>
