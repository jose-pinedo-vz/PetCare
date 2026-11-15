<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar cita</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>

    <main class="contenedor contenedor--angosto">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Cancelar cita</h1>
            <p class="tarjeta__subtitulo">¿Seguro que quieres cancelar esta cita?</p>

            <div class="tarjeta resumen-cita">
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Servicio</dt>
                    <dd class="resumen-cita__valor">Baño · Luna</dd> 

                    <dt class="resumen-cita__dato">Fecha y hora</dt>
                    <dd class="resumen-cita__valor">12 nov · 11:00</dd> 
                </dl>
            </div>

            <form action="cancelar_cita.php" method="post">
                <input type="hidden" name="id_cita" value="1"> 
                <div class="grupo-botones">
                    <button class="boton" type="submit">Sí, cancelar cita</button>
                    <a class="boton boton--contorno" href="mis_citas.php">No, volver</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>