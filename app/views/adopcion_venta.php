<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Adopción y venta</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>

<body>
    <div class="layout">
        <?php $paginaActiva = 'adopcion';
        include __DIR__ . '/layout/sidebar.php'; ?>

        <main>
            <div class="migaja">Gestión de adopción y venta /</div>
            <div class="encabezado-pagina">
                <h1>Adopción y venta</h1>
            </div>
            <div class="tarjeta">
                <div class="agregar-buscar">
                    <label class="visualmente-oculto" for="buscarAdopcionVenta">Buscar adopciones o ventas</label>
                    <input class="campo buscador-inteligente" id="buscarAdopcionVenta" type="search" placeholder="Buscar por especie, raza o estado..." autocomplete="off">
                    <a class="btn" href="agregar_adopcion_venta.php">+ Agregar adopción y venta</a>
                </div>
                <div class="tabla-envoltura">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Especie</th>
                                <th>Raza</th>
                                <th>Sexo</th>
                                <th>Fecha de nacimiento</th>
                                <th>Color</th>
                                <th>Peso</th>
                                <th>Características</th>
                                <th>Fotografía</th>
                                <th>Procedencia</th>
                                <th>Fecha de ingreso</th>
                                <th>Costo de adquisición</th>
                                <th>Precio de venta</th>
                                <th>Estado</th>
                                <th>Vacunas</th>
                                <th>Desparasitación</th>
                                <th>Observaciones</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="registrosAdopcionVenta">
                            <tr>
                                <td>1</td>
                                <td>Perro</td>
                                <td>Labrador</td>
                                <td>Hembra</td>
                                <td>2025-03-12</td>
                                <td>Dorado</td>
                                <td>18 kg</td>
                                <td>Dócil, juguetona, sociable con niños</td>
                                <td>luna.jpg</td>
                                <td>Rescate</td>
                                <td>2026-08-02</td>
                                <td>$0</td>
                                <td>No aplica (adopción)</td>
                                <td>Disponible</td>
                                <td>Rabia, Parvovirus, Moquillo</td>
                                <td>2026-09-15</td>
                                <td>Esterilizada</td>
                                <td class="acciones">
                                    <a class="btn btn-chico" href="editar_adopcion_venta.php?id=1">Editar</a>
                                    <a class="btn btn-chico btn-peligro" href="eliminar_adopcion_venta.php?id=1">Eliminar</a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Gato</td>
                                <td>Siamés</td>
                                <td>Macho</td>
                                <td>2026-01-20</td>
                                <td>Crema y café</td>
                                <td>3.2 kg</td>
                                <td>Tranquilo, ojos azules</td>
                                <td>michi.jpg</td>
                                <td>Criadero autorizado</td>
                                <td>2026-08-10</td>
                                <td>$1,200</td>
                                <td>$2,500</td>
                                <td>Disponible</td>
                                <td>Triple felina</td>
                                <td>2026-09-01</td>
                                <td>Pelaje en buen estado</td>
                                <td class="acciones">
                                    <a class="btn btn-chico" href="editar_adopcion_venta.php?id=1">Editar</a>
                                    <a class="btn btn-chico btn-peligro" href="eliminar_adopcion_venta.php?id=1">Eliminar</a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Perro</td>
                                <td>Mestizo</td>
                                <td>Macho</td>
                                <td>2024-11-05</td>
                                <td>Café con blanco</td>
                                <td>14 kg</td>
                                <td>Activo, aprende rápido</td>
                                <td>rocky.jpg</td>
                                <td>Donación</td>
                                <td>2026-07-28</td>
                                <td>$0</td>
                                <td>No aplica (adopción)</td>
                                <td>En proceso</td>
                                <td>Rabia, Quíntuple</td>
                                <td>2026-08-20</td>
                                <td>Adopción pendiente de firma</td>
                                <td class="acciones">
                                    <a class="btn btn-chico" href="editar_adopcion_venta.php?id=1">Editar</a>
                                    <a class="btn btn-chico btn-peligro" href="eliminar_adopcion_venta.php?id=1">Eliminar</a>
                                </td>
                            </tr>
                            <tr id="sinResultados" hidden>
                                <td colspan="18">No se encontraron registros.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

    </div>

        <footer>
            <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
        </footer>
        <script>
            const buscadorAdopcionVenta = document.getElementById('buscarAdopcionVenta');
            const filasAdopcionVenta = document.querySelectorAll('#registrosAdopcionVenta tr:not(#sinResultados)');
            const mensajeSinResultados = document.getElementById('sinResultados');

            buscadorAdopcionVenta.addEventListener('input', () => {
                const termino = buscadorAdopcionVenta.value.trim().toLocaleLowerCase();
                let resultados = 0;

                filasAdopcionVenta.forEach((fila) => {
                    const texto = Array.from(fila.cells)
                        .slice(0, -1)
                        .map((celda) => celda.textContent)
                        .join(' ')
                        .toLocaleLowerCase();
                    const coincide = texto.includes(termino);
                    fila.hidden = !coincide;
                    resultados += coincide ? 1 : 0;
                });

                mensajeSinResultados.hidden = resultados > 0;
            });
        </script>
</body>

</html>                             