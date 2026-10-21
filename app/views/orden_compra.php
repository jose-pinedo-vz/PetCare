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
            <div class="migaja">Gestión de inventario /</div>
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
                                    <th>Seleccionar</th>
                                    <th>Código</th>
                                    <th>Código de barra</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Contenido / unidad</th>
                                    <th>Precio de compra</th>
                                    <th>Existencia</th>
                                    <th>Lote / caducidad</th>
                                    <th>Ubicación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Catálogo ilustrativo: reemplazar por los productos relacionados con el proveedor seleccionado. -->
                                <tr>
                                    <td><input type="checkbox" aria-label="Seleccionar alimento para perro adulto"></td>
                                    <td>ALI-001</td>
                                    <td>7501234567890</td>
                                    <td>Alimento para perro adulto</td>
                                    <td>Alimento</td>
                                    <td>20 kg / saco</td>
                                    <td>$680.00</td>
                                    <td>3 kg</td>
                                    <td>LT-2408 / 2026-12-15</td>
                                    <td>A-01</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" aria-label="Seleccionar guantes de exploración"></td>
                                    <td>MED-014</td>
                                    <td>7501234567891</td>
                                    <td>Guantes de exploración</td>
                                    <td>Material médico</td>
                                    <td>100 / caja</td>
                                    <td>$95.50</td>
                                    <td>24 cajas</td>
                                    <td>LT-2510 / 2027-05-30</td>
                                    <td>B-03</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" aria-label="Seleccionar shampoo dermatológico"></td>
                                    <td>HIG-008</td>
                                    <td>7501234567892</td>
                                    <td>Shampoo dermatológico</td>
                                    <td>Higiene</td>
                                    <td>500 ml / botella</td>
                                    <td>$82.00</td>
                                    <td>16 botellas</td>
                                    <td>LT-2504 / 2027-01-20</td>
                                    <td>C-02</td>
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
                                    <th>Código</th>
                                    <th>Código de barra</th>
                                    <th>Producto</th>
                                    <th>Contenido / unidad</th>
                                    <th>Existencia</th>
                                    <th>Lote / caducidad</th>
                                    <th>Precio de compra editable</th>
                                    <th>Cantidad editable</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Selección de ejemplo: editar precio y cantidad; los controles son solo interfaz y no calculan ni guardan datos. -->
                                <tr>
                                    <td>ALI-001</td>
                                    <td>7501234567890</td>
                                    <td>Alimento para perro adulto</td>
                                    <td>20 kg / saco</td>
                                    <td>3 kg</td>
                                    <td>LT-2408 / 2026-12-15</td>
                                    <td><label class="visualmente-oculto" for="precio-ali-001">Precio de compra de alimento para perro adulto</label><input id="precio-ali-001" class="campo orden-compra-input" type="number" min="0" step="0.01" value="680.00"></td>
                                    <td><label class="visualmente-oculto" for="cantidad-ali-001">Cantidad de alimento para perro adulto</label><input id="cantidad-ali-001" class="campo orden-compra-input" type="number" min="1" step="1" value="5"></td>
                                </tr>
                                <tr>
                                    <td>MED-014</td>
                                    <td>7501234567891</td>
                                    <td>Guantes de exploración</td>
                                    <td>100 / caja</td>
                                    <td>24 cajas</td>
                                    <td>LT-2510 / 2027-05-30</td>
                                    <td><label class="visualmente-oculto" for="precio-med-014">Precio de compra de guantes de exploración</label><input id="precio-med-014" class="campo orden-compra-input" type="number" min="0" step="0.01" value="95.50"></td>
                                    <td><label class="visualmente-oculto" for="cantidad-med-014">Cantidad de guantes de exploración</label><input id="cantidad-med-014" class="campo orden-compra-input" type="number" min="1" step="1" value="10"></td>
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
                    <button class="btn btn-success" type="button">Confirmar orden</button>
                    <a class= "btn btn-peligro" href="inventario.php">Cancelar orden</a>
                </div>

            </div>

        </main>
</body>

</html>
