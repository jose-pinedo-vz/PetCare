<?php
session_start();
$paginaActiva = 'mascotas';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar mascota</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>
    <?php include __DIR__ . '/../layout/menu_cliente.php'; ?>

    <main class="contenedor">
        <p class="migas-de-pan"><a href="mis_mascotas.php">Mis mascotas</a> › Editar mascota</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Editar mascota</h1>
            <p class="tarjeta__subtitulo">Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>

            <form action="actualizar_mascota.php" method="post" enctype="multipart/form-data">

                <div class="formulario-con-foto">
                    <div>
                        <div class="foto-marcador foto-marcador--subir">Foto de la mascota</div>
                        <div class="campo">
                            <label class="campo__etiqueta" for="fotografia">Cambiar foto</label>
                            <input class="campo__entrada" type="file" id="fotografia" name="fotografia" accept="image/*">
                            <input type="hidden" name="fotografia_actual" value="">
                        </div>
                    </div>

                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="nombre">Nombre <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="text" id="nombre" name="nombre" placeholder="p. ej. Luna" value="Luna" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="especie">Especie <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="especie" name="especie" required>
                                <option value="Perro" selected>Perro</option>
                                <option value="Gato">Gato</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="raza">Raza</label>
                            <input class="campo__entrada" type="text" id="raza" name="raza" placeholder="p. ej. Labrador" value="Labrador">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="sexo">Sexo <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="sexo" name="sexo" required>
                                <option value="Hembra" selected>Hembra</option>
                                <option value="Macho">Macho</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="edad">Edad (años) <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="number" id="edad" name="edad" placeholder="p. ej. 3" min="0" step="0.1" value="3" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="peso">Peso (kg) <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="number" id="peso" name="peso" placeholder="p. ej. 24" min="0" step="0.1" value="24" required>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="color">Color</label>
                            <input class="campo__entrada" type="text" id="color" name="color">
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="temperamento">Temperamento</label>
                            <select class="campo__entrada" id="temperamento" name="temperamento">
                                <option value="">No estoy seguro</option>
                                <option value="Tranquilo">Tranquilo</option>
                                <option value="Juguetón">Juguetón</option>
                                <option value="Nervioso">Nervioso</option>
                                <option value="Reactivo">Reactivo o agresivo</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="tamanio">Tamaño</label>
                            <select class="campo__entrada" id="tamanio" name="tamanio">
                                <option value="Pequeño">Pequeño</option>
                                <option value="Mediano">Mediano</option>
                                <option value="Grande" selected>Grande</option>
                                <option value="Gigante">Gigante</option>
                            </select>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="alergias">Alergias</label>
                            <textarea class="campo__entrada" id="alergias" name="alergias" rows="4" placeholder="Cuéntanos si hay algo que debamos saber"></textarea>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="condiciones_especiales">Condiciones especiales</label>
                            <textarea class="campo__entrada" id="condiciones_especiales" name="condiciones_especiales" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Guardar cambios</button>
                    <a class="boton boton--contorno" href="mis_mascotas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
