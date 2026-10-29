<?php
require_once __DIR__ . '/../controllers/adopcion_venta.php';
$animales = listar_adopciones_ventas();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Adopción y venta</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
    <style>
        .foto-miniatura-celda {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .foto-miniatura {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid rgba(107, 79, 47, 0.25);
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            background-color: #fff;
        }

        .sin-foto {
            color: #8c7b6c;
            font-size: 0.85rem;
            font-style: italic;
        }

        /* Modal visor de pantalla completa */
        .modal-visor {
            display: none;
            position: fixed;
            z-index: 99999;
            inset: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.88);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-sizing: border-box;
            padding: 24px;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }

        .modal-visor.activo {
            display: flex;
            opacity: 1;
        }

        .visor-contenido {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            max-width: 95vw;
            max-height: 90vh;
            position: relative;
        }

        .visor-contenido img {
            max-width: 92vw;
            max-height: 84vh;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.8);
            cursor: zoom-out;
        }

        .pie-visor {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 14px;
            text-align: center;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);
        }

        .btn-cerrar-visor {
            position: absolute;
            top: 20px;
            right: 25px;
            color: #ffffff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.18);
            transition: background 0.2s, transform 0.2s;
            user-select: none;
            z-index: 100000;
        }

        .btn-cerrar-visor:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.1);
        }
    </style>
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
<<<<<<< HEAD
                <div class="barra-acciones-busqueda">
                    <div class="busqueda-barra" role="search">
                        <label class="visualmente-oculto" for="buscarAdopcionVenta">Buscar adopciones o ventas</label>
                        <input class="campo" id="buscarAdopcionVenta" type="search" placeholder="Buscar por especie, raza o estado..." autocomplete="off">
                    </div>
=======
                <div class="barra-acciones">
>>>>>>> c86ed63934a26c7e68b08838998072db02aa8825
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
<<<<<<< HEAD
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
=======
                        <tbody>
                            <?php if (!empty($animales)): ?>
                                <?php foreach ($animales as $animal): ?>
                                    <tr>
                                        <td><?= htmlspecialchars((string)$animal['id_animal']) ?></td>
                                        <td><?= htmlspecialchars((string)($animal['especie'] ?? '-')) ?></td>
                                        <td><?= htmlspecialchars((string)($animal['raza'] ?? '-')) ?></td>
                                        <td><?= htmlspecialchars((string)($animal['sexo'] ?? '-')) ?></td>
                                        <td><?= htmlspecialchars(substr((string)$animal['fecha_de_nacimiento'], 0, 10)) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['color']) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['peso']) ?> kg</td>
                                        <td><?= htmlspecialchars((string)$animal['caracteristicas']) ?></td>
                                        <td>
                                            <?php
                                            $foto = trim((string)($animal['fotografia'] ?? ''));
                                            if ($foto !== '' && $foto !== 'Sin foto'):
                                                if (str_starts_with($foto, 'http://') || str_starts_with($foto, 'https://') || str_starts_with($foto, '/')) {
                                                    $rutaFoto = $foto;
                                                } elseif (str_starts_with($foto, '../../')) {
                                                    $rutaFoto = $foto;
                                                } elseif (str_starts_with($foto, '../')) {
                                                    $rutaFoto = '../' . $foto;
                                                } else {
                                                    $rutaFoto = '../../' . ltrim($foto, '/');
                                                }
                                                $nombreAnimal = trim(($animal['especie'] ?? '') . ' ' . ($animal['raza'] ?? ''));
                                                if ($nombreAnimal === '') {
                                                    $nombreAnimal = 'Mascota #' . $animal['id_animal'];
                                                }
                                            ?>
                                                <div class="foto-miniatura-celda">
                                                    <img src="<?= htmlspecialchars($rutaFoto) ?>"
                                                         alt="<?= htmlspecialchars($nombreAnimal) ?>"
                                                         class="foto-miniatura"
                                                         title="Click para ampliar imagen"
                                                         data-full="<?= htmlspecialchars($rutaFoto) ?>"
                                                         data-title="<?= htmlspecialchars($nombreAnimal) ?>"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                                                    <span class="sin-foto" style="display:none;">Imagen no disponible</span>
                                                </div>
                                            <?php else: ?>
                                                <span class="sin-foto">Sin foto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars((string)$animal['procedencia']) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['fecha_de_ingreso']) ?></td>
                                        <td><?= ($animal['costo_de_adquisicion'] !== null && $animal['costo_de_adquisicion'] !== '') ? '$' . number_format((float)$animal['costo_de_adquisicion'], 2) : '-' ?></td>
                                        <td><?= ($animal['precio_de_venta'] > 0) ? '$' . number_format((float)$animal['precio_de_venta'], 2) : 'No aplica (adopción)' ?></td>
                                        <td><?= htmlspecialchars((string)($animal['estado'] ?? 'Disponible')) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['vacunas']) ?></td>
                                        <td><?= htmlspecialchars(substr((string)$animal['desparasitacion'], 0, 10)) ?></td>
                                        <td><?= htmlspecialchars((string)$animal['observaciones']) ?></td>
                                        <td>
                                            <a class="btn" href="../views/editar_adopcion_venta.php?id=<?= $animal['id_animal'] ?>">Editar</a>
                                            <form action="../controllers/procesar_venta_adopcion.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Deseas confirmar la transacción de este animal?');">
                                                <input type="hidden" name="id_animal" value="<?= $animal['id_animal'] ?>">
                                                <input type="hidden" name="precio_de_venta" value="<?= $animal['precio_de_venta'] ?>">
                                                <input type="hidden" name="especie" value="<?= htmlspecialchars((string)($animal['especie'] ?? '-')) ?>">
                                                <input type="hidden" name="raza" value="<?= htmlspecialchars((string)($animal['raza'] ?? '-')) ?>">
                                                <button type="submit" class="btn-secundario">Vender</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="18" style="text-align: center;">No hay registros de adopción y venta.</td>
                                </tr>
                            <?php endif; ?>
>>>>>>> c86ed63934a26c7e68b08838998072db02aa8825
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

    </div>

        <footer>
            <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
        </footer>
<<<<<<< HEAD
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
=======

    <!-- Modal para ver imagen ampliada / pantalla completa -->
    <div id="modal-visor-imagen" class="modal-visor" role="dialog" aria-modal="true" aria-label="Visor de imagen">
        <span class="btn-cerrar-visor" title="Cerrar (Esc)">&times;</span>
        <div class="visor-contenido">
            <img id="imagen-ampliada" src="" alt="Imagen ampliada" title="Haz clic para cerrar">
            <div id="pie-imagen-ampliada" class="pie-visor"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modal = document.getElementById('modal-visor-imagen');
            var imgAmpliada = document.getElementById('imagen-ampliada');
            var pie = document.getElementById('pie-imagen-ampliada');
            var btnCerrar = modal.querySelector('.btn-cerrar-visor');

            function abrirModal(src, titulo) {
                imgAmpliada.src = src;
                pie.textContent = titulo || '';
                modal.classList.add('activo');
                document.body.style.overflow = 'hidden';
            }

            function cerrarModal() {
                modal.classList.remove('activo');
                document.body.style.overflow = '';
                imgAmpliada.src = '';
            }

            // Click en cualquier miniatura con la clase .foto-miniatura
            document.addEventListener('click', function (e) {
                var target = e.target;
                if (target && target.classList.contains('foto-miniatura')) {
                    var src = target.getAttribute('data-full') || target.src;
                    var titulo = target.getAttribute('data-title') || '';
                    abrirModal(src, titulo);
                }
            });

            // Botón cerrar (X)
            btnCerrar.addEventListener('click', function (e) {
                e.stopPropagation();
                cerrarModal();
            });

            // Cerrar al hacer clic en el fondo oscuro o en la imagen grande
            modal.addEventListener('click', function (e) {
                if (e.target === modal || e.target === imgAmpliada || e.target.classList.contains('visor-contenido')) {
                    cerrarModal();
                }
            });

            // Cerrar con la tecla Escape
            document.addEventListener('keydown', function (e) {
                if ((e.key === 'Escape' || e.key === 'Esc') && modal.classList.contains('activo')) {
                    cerrarModal();
                }
            });
        });
    </script>
>>>>>>> c86ed63934a26c7e68b08838998072db02aa8825
</body>

</html>                             
