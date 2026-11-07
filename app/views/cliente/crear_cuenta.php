<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear mi cuenta</title>
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
            <h1>Crear mi cuenta</h1>
        </div>

        <form action="registrar_cuenta.php" method="post">

            <fieldset>
                <legend>Datos de acceso</legend>

                <div>
                    <label for="usuario">Usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="p. ej. maria.lopez" required>
                </div>

                <div>
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                </div>

                <div>
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" required>
                </div>

                <div>
                    <label for="confirmar_contrasena">Confirmar contraseña</label>
                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="••••••••" required>
                </div>
            </fieldset>

            <fieldset>
                <legend>Tus datos</legend>

                <div>
                    <label for="nombres">Nombre(s)</label>
                    <input type="text" id="nombres" name="nombres" required>
                </div>

                <div>
                    <label for="apellidos">Apellidos</label>
                    <input type="text" id="apellidos" name="apellidos" required>
                </div>

                <div>
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" placeholder="10 dígitos" maxlength="10" pattern="[0-9]{10}" required>
                </div>

                <div>
                    <label for="direccion">Dirección (opcional)</label>
                    <input type="text" id="direccion" name="direccion">
                </div>
            </fieldset>

            <fieldset>
                <legend>Contacto de emergencia</legend>

                <div>
                    <label for="emergencia_nombre">Nombre</label>
                    <input type="text" id="emergencia_nombre" name="emergencia_nombre" required>
                </div>

                <div>
                    <label for="emergencia_telefono">Teléfono</label>
                    <input type="tel" id="emergencia_telefono" name="emergencia_telefono" maxlength="10" pattern="[0-9]{10}" required>
                </div>
            </fieldset>


            <div>
                <button type="submit">Crear cuenta</button>
                <a href="inicio.php">Cancelar</a>
            </div>
        </form>

        <p>¿Ya tienes cuenta? <a href="iniciar_sesion.php">Inicia sesión</a></p>
    </main>

    <footer>
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>