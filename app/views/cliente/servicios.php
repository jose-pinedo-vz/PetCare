<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestros servicios</title>
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
            <a href="mis_citas.php">Mis citas</a>
            <a href="servicios.php" class="activo">Servicios</a>
            <a href="nuestro_equipo.php">Nuestro equipo</a>
        </nav>

        <div>
            <span>Hola, María</span>
            <a href="salir.php">Salir</a>
        </div>
    </header>

    <main>
        <div>
            <h1>Nuestros servicios</h1>
            <p>Elige el que necesita tu mascota y agenda tu cita. Los precios son de ejemplo.</p>
        </div>

        <section>

            <article>
                <div>Foto: baño</div>

                <div>
                    <h2>Baño</h2>
                    <p>Baño y secado para tu mascota. Puede incluir tratamientos especiales si lo necesita.</p>
                    <span>60 min</span>
                    <span>Estética</span>
                </div>

                <div>
                    <p>$150</p>
                    <a href="agendarCita.php?servicio=bano">Agendar</a>
                </div>
            </article>

            <article>
                <div>Foto: corte de pelo</div>

                <div>
                    <h2>Corte de pelo</h2>
                    <p>Corte según el tipo de pelaje y el estilo que prefieras.</p>
                    <span>90 min</span>
                    <span>Estética</span>
                </div>

                <div>
                    <p>$250</p>
                    <a href="agendarCita.php?servicio=corte_pelo">Agendar</a>
                </div>
            </article>

            <article>
                <div>Foto: corte de uñas</div>

                <div>
                    <h2>Corte de uñas</h2>
                    <p>Corte y limado seguro de uñas.</p>
                    <span>20 min</span>
                    <span>Estética</span>
                </div>

                <div>
                    <p>$60</p>
                    <a href="agendarCita.php?servicio=corte_unas">Agendar</a>
                </div>
            </article>

            <article>
                <div>Foto: desparasitación</div>

                <div>
                    <h2>Desparasitación</h2>
                    <p>Aplicación del desparasitante adecuado a su peso y especie, a cargo de nuestro veterinario.</p>
                    <span>30 min</span>
                    <span>Veterinaria</span>
                </div>

                <div>
                    <p>$120</p>
                    <a href="agendarCita.php?servicio=desparasitacion">Agendar</a>
                </div>
            </article>

            <article>
                <div>Foto: hotel</div>

                <div>
                    <h2>Hotel de mascotas</h2>
                    <p>Hospedaje cómodo y supervisado para perros, gatos y otros mamíferos pequeños.</p>
                    <ul>
                        <li>Esquema de vacunación completo (se revisa al llegar).</li>
                        <li>Baño y cortes se cobran aparte.</li>
                        <li>La reservación la confirma nuestro equipo.</li>
                    </ul>
                    <span>Precio por noche (por definir)</span>
                </div>

                <div>
                    <a href="solicitar_reservacion.php">Solicitar reservación</a>
                </div>
            </article>

        </section>
    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>