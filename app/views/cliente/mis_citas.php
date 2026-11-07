<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis citas</title>
</head>
<body>
    <header>
        <div>
            <span>🐾</span>
            <span>PetCare</span>
        </div>

        <nav>
            <a href="inicio.php">Inicio</a>
            <a href="mis_mascotas.php">Mis mascotas</a>
            <a href="mis_citas.php" class="activo">Mis citas</a>
            <a href="servicios.php">Servicios</a>
            <a href="nuestro_equipo.php">Nuestro equipo</a>
        </nav>

        <div>
            <span>Hola, María</span>
            <a href="salir.php">Salir</a>
        </div>
    </header>

    <main>
        <div>
            <div>
                <h1>Mis citas</h1>
                <p>Consulta o cancela tus citas.</p>
            </div>
            <a href="agendarCita.php">+ Agendar cita</a>
        </div>

        <nav>
            <a href="mis_citas.php?ver=proximas" class="activo">Próximas</a>
            <a href="mis_citas.php?ver=anteriores">Anteriores</a>
        </nav>

        <section>

            <article>
                <div>
                    <p>12</p>
                    <p>NOV</p>
                </div>

                <div>
                    <h2>Baño · Luna</h2>
                    <p>11:00 · 60 min · Atiende: Ana Torres</p>
                    <p>Anticipo pagado: $50.00 · Total: $150.00</p>
                </div>

                <div>
                    <span>Confirmada</span>
                    <a href="reprogramar_cita.php?id_cita=1">Reprogramar</a>
                    <a href="cancelar_cita.php?id_cita=1">Cancelar</a>
                </div>
            </article>

            <article>
                <div>
                    <p>20</p>
                    <p>NOV</p>
                </div>

                <div>
                    <h2>Desparasitación · Michi</h2>
                    <p>15:00 · 30 min · Atiende: Dra. Pérez</p>
                    <p>Anticipo pendiente: $40.00 · Total: $120.00</p>
                </div>

                <div>
                    <span>Falta anticipo</span>
                    <a href="pagar_anticipo.php?id_cita=2">Pagar anticipo</a>
                    <a href="cancelar_cita.php?id_cita=2">Cancelar</a>
                </div>
            </article>

            <article>
                <div>
                    <p>28</p>
                    <p>OCT</p>
                </div>

                <div>
                    <h2>Corte de uñas · Luna</h2>
                    <p>10:00 · 20 min · Atendida por: Luis Medina</p>
                </div>

                <div>
                    <span>Atendida</span>
                </div>
            </article>

        </section>

    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>