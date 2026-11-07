
<!DOCTYPE html>
<html lang="es">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veterinaria PetCare - Servicios</title>
    <link rel="stylesheet" href="css/estilos_base_v2.css">
</head>
 
<body>
    <div class="layout">
        <?php $paginaActiva = 'hotel';
        include __DIR__ . '/layout/sidebar.php'; ?>
 
        <main>
            <div class="migaja">Gestión de hotel /</div>
            <div class="encabezado-pagina">
                <h1>Hotel de mascotas</h1>
            </div>
            
 
            <div class="tarjeta">
                <div class="barra-acciones-busqueda">
                    <h2>Disponibilidad de jaulas</h2>
                    <div class="barra-acciones">
            <a class="btn" href="reservacion_hotel.php">+ Nueva reservación</a>
          </div>
                </div>
                <div class="inventario-resumen">
 
                    <a class="inventario-tarjeta" href="agendarCita.php?id_servicio=1">
                        <h3>Disponibles</h3>
                        <p>8</p>
                    </a>
 
                    <a class="inventario-tarjeta" href="agendarCita.php?id_servicio=2">
                        <h3>Ocupadas</h3>
                        <p>2</p>
                    </a>
 
                    <a class="inventario-tarjeta" href="agendarCita.php?id_servicio=3">
                        <h3>En mantenimiento</h3>
                        <p>1</p>
                    </a>

 
                    
                </div>
                <div class="inventario-tarjeta">
                    <h3>Total de jaulas</h3>
                    <p>11</p>
                </div>
                <br>

                <div class="tabla-envoltura">
                        <table>
                            <thead>
                                <tr>
                                    <th>Jaula</th>
                                    <th>Tamaño</th>
                                    <th>Estado</th>
                                    <th>Mascota actual</th>
                                    <th>Libre a partir de</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>J-01</td>
                                    <td>Grande</td>
                                    <td><span class="historial-estado pagada">Disponible</span></td>
                                    <td>-</td>
                                    <td>Hoy(reservada el 2026-11-05)</td>
                                </tr>
                                <tr>
                                    <td>J-02</td>
                                    <td>Mediana</td>
                                    <td><span class="historial-estado pendiente">Ocupada</span></td>
                                    <td>Luna</td>
                                    <td>2026-10-30</td>
                                </tr>
                                <tr>
                                    <td>J-04</td>
                                    <td>Mediana</td>
                                    <td><span class="historial-estado cancelada">Mantenimiento</span></td>
                                    <td>-</td>
                                    <td>Sin fecha</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <div style="margin-top: 24px;">
                    <h2>Reservaciones</h2>
 
                    <form class="inventario-pedidos-toolbar" action="hotel.php" method="GET">
                        <input type="text" class="campo" name="buscar" placeholder="Buscar por cliente o mascota" aria-label="Buscar por cliente o mascota">
                        <input type="date" class="campo pedidos-filtro-estado" name="fecha" aria-label="Fecha">
                        <select class="campo pedidos-filtro-estado" name="estado" aria-label="Estado">
                            <option value="">Todos los estados</option>
                            <option value="Programada">En estancia</option>
                            <option value="Confirmada">Reservada</option>
                            <option value="Completada">Finalizada</option>
                            <option value="Cancelada">Cancelada</option>
                        </select>
                    </form>
 
                    <div class="tabla-envoltura">
                        <table>
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Cliente</th>
                                    <th>Mascota</th>
                                    <th>Jaula</th>
                                    <th>Entrada</th>
                                    <th>Salida</th>
                                    <th>Noches</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>HT-001</td>
                                    <td>María López</td>
                                    <td>Luna</td>
                                    <td>J-03</td>
                                    <td>2026-10-30</td>
                                    <td>2026-10-30</td>
                                    <td>4</td>
                                    <td>$520.00</td>
                                    <td><span class="historial-estado pagada">En estancia</span></td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>HT-002</td>
                                    <td>Jorge Diaz</td>
                                    <td>Thor</td>
                                    <td>J-01</td>
                                    <td>2026-11-05</td>
                                    <td>2026-10-08</td>
                                    <td>3</td>
                                    <td>$480.00</td>
                                    <td><span class="historial-estado pendiente">Reservada</span></td>
                                    <td>-</td>
                                </tr>
                                <tr>
                                    <td>HT-003</td>
                                    <td>Elena Ruiz</td>
                                    <td>Nala</td>
                                    <td>J-02</td>
                                    <td>2026-10-30</td>
                                    <td>2026-11-01</td>
                                    <td>2</td>
                                    <td>$0.00</td>
                                    <td><span class="historial-estado cancelada">Cancelada</span></td>
                                    <td>-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
 
            </div>
        </main>
    </div>
 
    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
 
</html>
 