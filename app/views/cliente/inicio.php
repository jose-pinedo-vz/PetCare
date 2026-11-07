<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetCare | Inicio</title>
</head>
<body>
    <header>
        <div>
            <span>🐾</span>
            <span>PetCare</span>
        </div>

        <nav>
            <a href="inicio.php" aria-current="page">Inicio</a>
            <a href="mis_mascotas.php">Mis mascotas</a>
            <a href="mis_citas.php">Mis citas</a>
            <a href="servicios.php">Servicios</a>
            <a href="nuestro_equipo.php">Nuestro equipo</a>
        </nav>

        <div>
            <a href="iniciar_sesion.php">Iniciar sesión</a>
            <a href="crear_cuenta.php">Crear cuenta</a>
        </div>
    </header>

    <main>

        <section>
            <div>
                <span>Veterinaria · Estética · Hotel de mascotas</span>
                <h1>Cuidamos a tu mascota como parte de la familia</h1>
                <p>Agenda su cita en línea, consulta nuestros servicios y conoce al equipo que la va a atender.</p>
                <div>
                    <a href="agendarCita.php">Agendar una cita</a>
                    <a href="servicios">Ver servicios</a>
                </div>
            </div>

            <div>
                <p>Foto del negocio o de una mascota</p>
            </div>
        </section>

        <section>
            <h2>¿Qué necesitas hoy?</h2>
            <p>Entra a tu cuenta para ver tus mascotas y tus citas.</p>

            <div>
                <a href="agendarCita.php">
                    <h3>Agendar una cita</h3>
                    <p>Elige el servicio, el día y el horario.</p>
                </a>

                <a href="registrar_mascota.php">
                    <h3>Registrar mi mascota</h3>
                    <p>Guarda sus datos básicos para agilizar tu visita.</p>
                </a>

                <a href="servicios">
                    <h3>Ver servicios</h3>
                    <p>Conoce duración y precio de cada uno.</p>
                </a>

                <a href="#equipo">
                    <h3>Conocer al equipo</h3>
                    <p>Veterinarios y estilistas que cuidan a tu mascota.</p>
                </a>
            </div>
        </section>

        <section id="servicios">
            <h2>Nuestros servicios</h2>
            <p>Precios de ejemplo; el administrador puede cambiarlos.</p>

            <div>
                <article>
                    <div>Foto: baño</div>
                    <h3>Baño</h3>
                    <p>Baño y secado.</p>
                    <p>60 min · $150.00</p>
                    <a href="agendarCita.php?servicio=bano">Agendar</a>
                </article>

                <article>
                    <div>Foto: corte de pelo</div>
                    <h3>Corte de pelo</h3>
                    <p>Corte según el tipo de pelaje y lo que prefieras.</p>
                    <p>90 min · $250.00</p>
                    <a href="agendarCita.php?servicio=corte_pelo">Agendar</a>
                </article>

                <article>
                    <div>Foto: corte de uñas</div>
                    <h3>Corte de uñas</h3>
                    <p>Corte y limado de uñas.</p>
                    <p>20 min · $60.00</p>
                    <a href="agendarCita.php?servicio=corte_unas">Agendar</a>
                </article>

                <article>
                    <div>Foto: desparasitación</div>
                    <h3>Desparasitación</h3>
                    <p>Aplicación a cargo de nuestro veterinario.</p>
                    <p>30 min · $120.00</p>
                    <a href="agendarCita.php?servicio=desparasitacion">Agendar</a>
                </article>

                <article>
                    <h3>Hotel de mascotas</h3>
                    <p>Hospedaje para perros, gatos y otros mamíferos pequeños. Pedimos el esquema de vacunación completo. El baño y los cortes se cobran aparte.</p>
                    <p>Consulta disponibilidad con nosotros</p>
                    <a href="hotel_requisitos.php">Ver requisitos</a>
                </article>
            </div>
        </section>

        <!-- Equipo -->
        <section id="equipo">
            <h2>Nuestro equipo</h2>
            <p>Profesionales que se encargan de tu mascota.</p>

            <div>
                <article>
                    <div>Foto</div>
                    <h3>Dra. Pérez</h3>
                    <p>Veterinaria · [Especialidad]</p>
                </article>

                <article>
                    <div>Foto</div>
                    <h3>Ana Torres</h3>
                    <p>Estilista</p>
                </article>

                <article>
                    <div>Foto</div>
                    <h3>Luis Medina</h3>
                    <p>Estilista</p>
                </article>
            </div>

            <a href="nuestro_equipo.php">Ver a todo el equipo</a>
        </section>

        <section id="conocenos">
            <h2>Conócenos</h2>

            <div>
                <article>
                    <h3>Quiénes somos</h3>

                    <h4>Misión</h4>
                    <p>[Texto de la misión del negocio]</p>

                    <h4>Visión</h4>
                    <p>[Texto de la visión del negocio]</p>
                </article>

                <article>
                    <h3>Contacto</h3>

                    <h4>Teléfono</h4>
                    <p>[Teléfono]</p>

                    <h4>WhatsApp</h4>
                    <p>[WhatsApp]</p>

                    <h4>Dirección</h4>
                    <p>[Dirección]</p>

                    <h4>Horario</h4>
                    <p>9:00 a 19:00</p>
                </article>
            </div>
        </section>

    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>