<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña</title>
</head>
<body>
    <header>
        <div>
            <span>🐾</span>
            <span>PetCare</span>
        </div>

    </header>

    <main>
        <div>
            <h1>Recuperar contraseña</h1>
        </div>

        <form action="validar_recuperacion.php" method="post">
            <div>
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>

            <div>
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
            </div>

            <div>
                <button type="submit">Continuar</button>
                <a href="iniciar_sesion.php">Cancelar</a>
            </div>
        </form>

        <p>¿Recordaste tu contraseña? <a href="iniciar_sesion.php">Inicia sesión</a></p>
    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>