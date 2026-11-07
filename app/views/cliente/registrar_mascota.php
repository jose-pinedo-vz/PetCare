<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar mascota</title>
</head>
<body>
    <header>
        <div>
            <span>🐾</span>
            <span>PetCare</span>
        </div>

        <nav>
            <a href="inicio.php">Inicio</a>
            <a href="mis_mascotas.php" aria-current="page">Mis mascotas</a>
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
        <p><a href="mis_mascotas.php">Mis mascotas</a> › Registrar mascota</p>

        <div>
            <h1>Registrar mascota</h1>
        </div>

        <form action="guardar_mascota.php" method="post" enctype="multipart/form-data">

            <div>
                <p>Foto de tu mascota</p>
                <label for="fotografia">Subir foto</label>
                <input type="file" id="fotografia" name="fotografia" accept="image/*">
            </div>

            <div>
                <div>
                    <label for="nombre">Nombre *</label>
                    <input type="text" id="nombre" name="nombre" placeholder="p. ej. Luna" required>
                </div>

                <div>
                    <label for="especie">Especie *</label>
                    <select id="especie" name="especie" required>
                        <option value="Perro">Perro</option>
                        <option value="Gato">Gato</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>

                <div>
                    <label for="raza">Raza</label>
                    <input type="text" id="raza" name="raza" placeholder="p. ej. Labrador">
                </div>

                <div>
                    <label for="sexo">Sexo *</label>
                    <select id="sexo" name="sexo" required>
                        <option value="Hembra">Hembra</option>
                        <option value="Macho">Macho</option>
                    </select>
                </div>

                <div>
                    <label for="edad">Edad (años) *</label>
                    <input type="number" id="edad" name="edad" placeholder="p. ej. 3" min="0" step="0.1" required>
                </div>

                <div>
                    <label for="peso">Peso (kg) *</label>
                    <input type="number" id="peso" name="peso" placeholder="p. ej. 24" min="0" step="0.1" required>
                </div>

                <div>
                    <label for="color">Color</label>
                    <input type="text" id="color" name="color">
                </div>

                <div>
                    <label for="temperamento">Temperamento</label>
                    <select id="temperamento" name="temperamento">
                        <option value="">No estoy seguro</option>
                        <option value="Tranquilo">Tranquilo</option>
                        <option value="Juguetón">Juguetón</option>
                        <option value="Nervioso">Nervioso</option>
                        <option value="Reactivo">Reactivo o agresivo</option>
                    </select>
                </div>

                <div>
                    <label for="tamanio">Tamaño</label>
                    <select id="tamanio" name="tamanio">
                        <option value="Pequeño">Pequeño</option>
                        <option value="Mediano">Mediano</option>
                        <option value="Grande">Grande</option>
                        <option value="Gigante">Gigante</option>
                    </select>
                </div>

            </div>
                <div>
                    <label for="alergias">Alergias o cuidados</label>
                    <textarea id="alergias" name="alergias" rows="4" placeholder="Cuéntanos si hay algo que debamos saber"></textarea>
                </div>

                <div>
                    <label for="condiciones_especiales">Condiciones especiales</label>
                    <textarea id="condiciones_especiales" name="condiciones_especiales" rows="3"></textarea>
                </div>


            <div>
                <button type="submit">Guardar mascota</button>
                <a href="mis_mascotas.php">Cancelar</a>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>