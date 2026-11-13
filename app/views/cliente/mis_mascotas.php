<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis mascotas</title>
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
                <h1>Mis mascotas</h1>
                <p>Aquí están las mascotas que has registrado.</p>
            </div>

        <div>

            <div>
                <div>Foto de la mascota</div>
                <h3>Luna</h3>
                <p>Perro · Labrador · Hembra</p>
                <p>3 años · 24 kg</p>
                <span>Vacunas al día</span>
                <div>
                    <a href="agendarCita.php?id_mascota=1">Agendar cita</a>
                    <a href="editar_mascota.php?id_mascota=1">Editar</a>
                </div>
            </div>

            <div>
                <div>Foto de la mascota</div>
                <h3>Michi</h3>
                <p>Gato · Siamés · Macho</p>
                <p>1 año · 4 kg</p>
                <span>Sin vacunas registradas</span>
                <div>
                    <a href="agendarCita.php?id_mascota=2">Agendar cita</a>
                    <a href="editar_mascota.php?id_mascota=2">Editar</a>
                </div>
            </div>

            <a href="registrar_mascota.php">
                <span>+</span>
                <h3>Registrar otra mascota</h3>
                <p>Solo necesitamos sus datos básicos.</p>
            </a>

        </div>

        <p>El historial médico y las vacunas los registra nuestro veterinario en la clínica.</p>
    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>