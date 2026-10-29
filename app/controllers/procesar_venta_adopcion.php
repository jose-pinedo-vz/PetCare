<?php
require_once __DIR__ . '/../models/adopcion_venta.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $id_animal = $_POST['id_animal'] ?? null;
    $precio_venta = isset($_POST['precio_de_venta']) ? (float)$_POST['precio_de_venta'] : 0;
    $especie = $_POST['especie'] ?? 'Desconocido';
    $raza = $_POST['raza'] ?? 'Desconocido';

    if (!$id_animal)
    {
        die("ID de animal no válido.");
    }
    $esAdopcion = ($precio_venta <= 0);
    $tipoTransaccion = $esAdopcion ? "ANIMAL ADOPTADO" : "ANIMAL VENDIDO";
    
    vendido($id_animal,$esAdopcion);
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Ticket de Transacción - PetCare</title>
        <style>
            body
            {
                font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
                background: #f8f4eb;
                margin: 0;
                padding: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                color: #3d2b1f;
            }
            .ticket-container
            {
                background: #ffffff;
                width: 320px;
                padding: 24px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(61, 43, 31, 0.08);
                border: 1px solid #e8dfd1;
            }
            .centro
            {
                text-align: center;
            }
            .logo-text
            {
                font-size: 18px;
                font-weight: 700;
                color: #3d2b1f;
                margin: 0 0 4px 0;
                letter-spacing: 0.5px;
            }
            .sub-titulo
            {
                font-size: 12px;
                color: #8c7355;
                text-transform: uppercase;
                margin: 0 0 8px 0;
            }
            .fecha
            {
                font-size: 11px;
                color: #a39281;
                margin-bottom: 15px;
            }
            .badge
            {
                background-color: #f3ede2;
                color: #3d2b1f;
                padding: 8px 12px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                margin: 15px 0;
                letter-spacing: 0.3px;
                border: 1px dashed #cbb279;
            }
            .badge.adoptado
            {
                background-color: #f3ede2;
                color: #3d2b1f;
                border-color: #cbb279;
            }
            .badge.vendido
            {
                background-color: #3d2b1f;
                color: #ffffff;
                border-color: #2a1d14;
            }
            .detalles
            {
                margin: 16px 0;
                font-size: 13px;
                color: #4a382a;
            }
            .fila-detalle
            {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
                border-bottom: 1px solid #f3ede2;
                padding-bottom: 6px;
            }
            .fila-detalle span:last-child
            {
                font-weight: 600;
                color: #3d2b1f;
            }
            .linea
            {
                border-top: 1px solid #e8dfd1;
                margin: 14px 0;
            }
            .footer-texto
            {
                font-size: 11px;
                color: #8c7355;
                line-height: 1.4;
                margin-bottom: 20px;
            }
            .btn-grupo
            {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .btn-imprimir
            {
                width: 100%;
                padding: 10px;
                background: #cbb279;
                color: #ffffff;
                border: none;
                text-align: center;
                cursor: pointer;
                font-weight: 600;
                font-size: 13px;
                border-radius: 6px;
                transition: background 0.2s;
                text-decoration: none;
                box-sizing: border-box;
            }
            .btn-imprimir:hover
            {
                background: #b59b62;
            }
            .btn-regresar
            {
                display: block;
                text-align: center;
                color: #8c7355;
                text-decoration: none;
                font-size: 12px;
                font-weight: 500;
                padding: 6px;
            }
            .btn-regresar:hover
            {
                color: #3d2b1f;
            }
            @media print
            {
                body
                {
                    background: none;
                    padding: 0;
                }
                .ticket-container
                {
                    box-shadow: none;
                    border: none;
                    width: 100%;
                    padding: 0;
                }
                .no-imprimir
                {
                    display: none;
                }
            }
        </style>
    </head>
    <body>
        <div class="ticket-container">
            <div class="centro">
                <p class="logo-text">VETERINARIA PETCARE</p>
                <p class="sub-titulo">Comprobante de Transacción</p>
                <p class="fecha"><?= date("Y-m-d H:i:s") ?></p>
            </div>
            
            <div class="linea"></div>

            <div class="centro">
                <div class="badge <?= $esAdopcion ? 'adoptado' : 'vendido' ?>">
                    <?= $tipoTransaccion ?>
                </div>
            </div>

            <div class="detalles">
                <div class="fila-detalle">
                    <span>ID Animal:</span>
                    <span>#<?= htmlspecialchars($id_animal) ?></span>
                </div>
                <div class="fila-detalle">
                    <span>Especie:</span>
                    <span><?= htmlspecialchars($especie) ?></span>
                </div>
                <div class="fila-detalle">
                    <span>Raza:</span>
                    <span><?= htmlspecialchars($raza) ?></span>
                </div>
                <div class="fila-detalle">
                    <span>Precio:</span>
                    <span><?= $esAdopcion ? '$0.00 (Adopción)' : '$' . number_format($precio_venta, 2) ?></span>
                </div>
            </div>

            <div class="linea"></div>

            <div class="centro footer-texto">
                <p>¡Gracias por darle un hogar y amor a nuestros pacientes!</p>
                <strong>PetCare System</strong>
            </div>

            <div class="no-imprimir btn-grupo">
                <a href="javascript:window.print();" class="btn-imprimir">Imprimir Ticket</a>
                <a href="../views/adopcion_venta.php" class="btn-regresar">Regresar al panel</a>
            </div>
        </div>

    </body>
    </html>
<?php
}
?>