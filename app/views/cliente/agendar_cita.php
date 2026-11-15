<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar cita</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>

    <main class="contenedor">
        <p class="migas-de-pan"><a href="mis_citas.php">Mis citas</a> › Agendar cita</p>

        <div class="columnas">
            <div class="columnas__principal tarjeta tarjeta--formulario">
                <h1 class="tarjeta__titulo">Agendar cita</h1>
                <p class="tarjeta__subtitulo">Los campos con <span class="campo__obligatorio">*</span> son obligatorios.</p>

                <form action="guardar_cita.php" method="post">
                    <div class="formulario-rejilla">
                        <div class="campo">
                            <label class="campo__etiqueta" for="id_servicio">Servicio <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="id_servicio" name="id_servicio" required>
                                <option value="">Elige un servicio</option>
                                <!-- aquí irán los servicios reales -->
                                <option value="1">Baño</option>
                                <option value="2">Corte de uñas</option>
                                <option value="3">Desparasitación</option>
                            </select>
                        </div>

                        <div class="campo">
                            <label class="campo__etiqueta" for="id_mascota">Mascota <span class="campo__obligatorio">*</span></label>
                            <select class="campo__entrada" id="id_mascota" name="id_mascota" required>
                                <option value="">Elige una mascota</option>
                 
                                <option value="1">Luna</option>
                                <option value="2">Michi</option>
                            </select>
                        </div>

                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="fecha">Fecha <span class="campo__obligatorio">*</span></label>
                            <input class="campo__entrada" type="date" id="fecha" name="fecha" required>
                        </div>
                    </div>

                    <p class="selector-horario__titulo">Hora <span class="campo__obligatorio">*</span></p>
                    <div class="selector-horario">
            
                        <button class="selector-horario__hora" type="button">09:00</button>
                        <button class="selector-horario__hora es-seleccionado" type="button">10:00</button>
                        <button class="selector-horario__hora es-ocupado" type="button" disabled>11:00</button>
                        <button class="selector-horario__hora" type="button">12:00</button>
                        <button class="selector-horario__hora" type="button">15:00</button>
                        <button class="selector-horario__hora" type="button">16:00</button>
                    </div>
             
                    <input type="hidden" name="hora" value="10:00">

                    <div class="formulario-rejilla">
                        <div class="campo campo--ancho-completo">
                            <label class="campo__etiqueta" for="motivo">Motivo o comentarios</label>
                            <textarea class="campo__entrada" id="motivo" name="motivo" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="grupo-botones">
                        <button class="boton" type="submit">Confirmar cita</button>
                        <a class="boton boton--contorno" href="mis_citas.php">Cancelar</a>
                    </div>
                </form>
            </div>

            <aside class="columnas__lateral tarjeta resumen-cita">
                <h2 class="resumen-cita__titulo">Resumen</h2>
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Servicio</dt>
                    <dd class="resumen-cita__valor">Baño</dd> 

                    <dt class="resumen-cita__dato">Mascota</dt>
                    <dd class="resumen-cita__valor">Luna</dd> 

                    <dt class="resumen-cita__dato">Fecha y hora</dt>
                    <dd class="resumen-cita__valor">12 nov · 10:00</dd> 

                    <dt class="resumen-cita__dato">Duración</dt>
                    <dd class="resumen-cita__valor">60 min</dd> 
                </dl>
                <p class="resumen-cita__total"><span>Total</span> <span>$150.00</span></p>
            </aside>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>