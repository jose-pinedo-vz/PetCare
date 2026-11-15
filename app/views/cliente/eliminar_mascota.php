<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar mascota</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>

    <main class="contenedor contenedor--angosto">
        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Eliminar mascota</h1>
            <p class="tarjeta__subtitulo">¿Seguro que quieres eliminar a esta mascota?</p> 

            <p class="aviso">Dejará de aparecer en tu cuenta. Su historial médico se conserva en la veterinaria.</p>

            <form action="confirmar_eliminar_mascota.php" method="post">
                <input type="hidden" name="id_mascota" value="1"> 
                <div class="grupo-botones">
                    <button class="boton" type="submit">Sí, eliminar</button>
                    <a class="boton boton--contorno" href="detalle_mascota.php?id_mascota=1">No, volver</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>