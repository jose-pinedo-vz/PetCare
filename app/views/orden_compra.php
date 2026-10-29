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
                    <p><strong>Proveedor:</strong> Nombre del proveedor</p>
                </div>

                <section class="orden-compra-seccion" aria-labelledby="titulo-sugeridos">
                    <h2 id="titulo-sugeridos" class="subtitulo">Productos sugeridos</h2>
                    <p class="orden-compra-ayuda">Productos con existencias bajas y productos agregados directamente a esta solicitud.</p>
                    <!-- Datos de ejemplo: mostrar aquí sugerencias de bajo stock y productos solicitados manualmente. -->
                    <div class="orden-compra-sugerencias">
                        <article class="orden-compra-sugerencia">
                            <div>
                                <span class="orden-compra-etiqueta">Stock bajo</span>
                                <h3>Alimento para perro adulto</h3>
                                <p>Código: ALI-001 · Existencia: 3 kg · Mínimo: 10 kg</p>
                            </div>
                            <button class="btn btn-chico" type="button">Agregar</button>
                        </article>
                        <article class="orden-compra-sugerencia">
                            <div>
                                <span class="orden-compra-etiqueta orden-compra-etiqueta-solicitado">Solicitud directa</span>
                                <h3>Guantes de exploración</h3>
                                <p>Código: MED-014 · Existencia: 24 piezas</p>
                            </div>
                            <button class="btn btn-chico" type="button">Agregar</button>
                        </article>
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
                                <!-- Catálogo ilustrativo: reemplazar por los productos relacionados con el proveedor seleccionado. -->
                                <tr>
                                    <td><button class="btn btn-chico" type="button">Agregar</button></td>
                                    <td><img src="imagen.jpg" alt="Imagen de productos"></td>
                                    <td>ALI-001</td>
                                    <td>Pelotas</td>
                                    <td>3</td>
                                </tr>
                                <tr>
                                    <td><button class="btn btn-chico" type="button">Agregar</button></td>
                                    <td><img src="imagen.jpg" alt="Imagen de productos"></td>
                                    <td>839301</td>
                                    <td>Camas para gato</td>
                                    <td>5</td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </section>

                <section class="orden-compra-seccion" aria-labelledby="titulo-productos-seleccionados">
                    <h2 id="titulo-productos-seleccionados" class="subtitulo">Productos seleccionados para la orden</h2>
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
                                <!-- Selección de ejemplo: editar precio y cantidad; los controles son solo interfaz y no calculan ni guardan datos. -->
                                <tr>
                                    <td><img src="imagen.jpg" alt="Imagen de productos"></td>
                                    <td>ALI-001</td>
                                    <td>Alimento para perro adulto</td>
                                    <td>10 kg</td>
                                    <td><input type="number" min="1" value="2"></td>
                                    <td class="acciones"><button class="btn btn-chico btn-peligro" type="button" aria-label="Eliminar Alimento para perro adulto">Eliminar</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="orden-compra-detalles">
                    <p><strong>Fecha de emisión:</strong> 2024-06-15</p>
                    <p><strong>Fecha de entrega:</strong> 2024-06-20</p>
                    <p><strong>Total:</strong> $150.00</p>
                </div>

                <div class="orden-compra-acciones">
                    <a class="btn btn-peligro btn-chico" href="inventario.php">Cancelar orden</a>
                    <button class="btn btn-chico" type="button">Confirmar orden</button>
                </div>

            </div>

        </main>
</body>

</html>
