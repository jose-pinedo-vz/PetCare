<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar anticipo</title>
    <link rel="stylesheet" href="../css/v2/cliente.css">
</head>
<body>

    <main class="contenedor contenedor--medio">
        <p class="migas-de-pan"><a href="mis_citas.php">Mis citas</a> › Pagar anticipo</p>

        <div class="tarjeta tarjeta--formulario">
            <h1 class="tarjeta__titulo">Pagar anticipo</h1>
            <p class="tarjeta__subtitulo">Desparasitación · Michi · 20 nov, 15:00</p> 

            <div class="tarjeta resumen-cita">
                <dl class="resumen-cita__lista">
                    <dt class="resumen-cita__dato">Total del servicio</dt>
                    <dd class="resumen-cita__valor">$120.00</dd> 
                </dl>
                <p class="resumen-cita__total"><span>Anticipo a pagar</span> <span>$40.00</span></p> 
            </div>

            <form action="pagar_anticipo.php" method="post">
                <input type="hidden" name="id_cita" value="2"> 

                <div class="formulario-rejilla">
                    <div class="campo campo--ancho-completo">
                        <label class="campo__etiqueta" for="metodo_pago">Método de pago <span class="campo__obligatorio">*</span></label>
                        <select class="campo__entrada" id="metodo_pago" name="metodo_pago" required>
                            <option value="">Elige un método</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Efectivo">Efectivo en sucursal</option>
                        </select>
                    </div>
                </div>

                <div class="grupo-botones">
                    <button class="boton" type="submit">Pagar anticipo</button>
                    <a class="boton boton--contorno" href="mis_citas.php">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <footer class="pie-pagina">
        <p>&copy; 2026 Veterinaria PetCare. Todos los derechos reservados.</p>
    </footer>
</body>
</html>