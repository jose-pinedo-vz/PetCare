<?php
session_start();
$paginaActiva = 'citas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis citas</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <div class="encabezado-pagina">
            <div>
                <h1 class="titulo-pagina">Mis citas</h1>
                <p class="subtitulo-pagina">Consulta o cancela tus citas.</p>
            </div>
            <a class="boton" href="agendarCita.php">+ Agendar cita</a>
        </div>

        <nav class="pestanas">
            <a class="pestanas__opcion es-activo" href="mis_citas.php?ver=proximas">Próximas</a>
            <a class="pestanas__opcion" href="mis_citas.php?ver=anteriores">Anteriores</a>
        </nav>

        <section>

            <article class="tarjeta-cita">
                <div class="tarjeta-cita__fecha">
                    <span class="tarjeta-cita__dia">12</span>
                    <span class="tarjeta-cita__mes">NOV</span>
                </div>

                <div class="tarjeta-cita__informacion">
                    <h2 class="tarjeta-cita__titulo">Baño · Luna</h2>
                    <p class="tarjeta-cita__detalle">11:00 · 60 min · Atiende: Ana Torres</p>
                    <p class="tarjeta-cita__detalle">Anticipo pagado: $50.00 · Total: $150.00</p>
                </div>

                <span class="estado-cita estado-cita--confirmada">Confirmada</span>

                <div class="tarjeta-cita__acciones">
                    <a class="boton boton--contorno boton--chico" href="reprogramar_cita.php?id_cita=1">Reprogramar</a>
                    <a class="boton boton--contorno boton--chico" href="cancelar_cita.php?id_cita=1">Cancelar</a>
                </div>
            </article>

            <article class="tarjeta-cita">
                <div class="tarjeta-cita__fecha">
                    <span class="tarjeta-cita__dia">20</span>
                    <span class="tarjeta-cita__mes">NOV</span>
                </div>

                <div class="tarjeta-cita__informacion">
                    <h2 class="tarjeta-cita__titulo">Desparasitación · Michi</h2>
                    <p class="tarjeta-cita__detalle">15:00 · 30 min · Atiende: Dra. Pérez</p>
                    <p class="tarjeta-cita__detalle">Anticipo pendiente: $40.00 · Total: $120.00</p>
                </div>

                <span class="estado-cita estado-cita--pendiente">Falta anticipo</span>

                <div class="tarjeta-cita__acciones">
                    <a class="boton boton--chico" href="pagar_anticipo.php?id_cita=2">Pagar anticipo</a>
                    <a class="boton boton--contorno boton--chico" href="cancelar_cita.php?id_cita=2">Cancelar</a>
                </div>
            </article>

            <article class="tarjeta-cita tarjeta-cita--pasada">
                <div class="tarjeta-cita__fecha">
                    <span class="tarjeta-cita__dia">28</span>
                    <span class="tarjeta-cita__mes">OCT</span>
                </div>

                <div class="tarjeta-cita__informacion">
                    <h2 class="tarjeta-cita__titulo">Corte de uñas · Luna</h2>
                    <p class="tarjeta-cita__detalle">10:00 · 20 min · Atendida por: Luis Medina</p>
                </div>

                <span class="estado-cita estado-cita--atendida">Atendida</span>
            </article>

        </section>

    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
