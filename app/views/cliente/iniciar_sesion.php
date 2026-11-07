<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
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
            <h1>Iniciar sesión</h1>
        </div>

        <form action="validar_login.php" method="post">
            <div>
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>

            <div>
                <label for="contrasena">Contraseña</label>
                <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" required>
            </div>

            <button type="submit">Entrar</button>
        </form>

        <p>¿Olvidaste tu contraseña? <a href="recuperar_contrasena.php">Recupérala</a></p>

        <p>¿Aún no tienes cuenta? <a href="crear_cuenta.php">Crea tu cuenta</a></p>

    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>