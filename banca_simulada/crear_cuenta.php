<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear cuenta - Banco simulado</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .portal-container { width: 100%; max-width: 500px; margin: 30px auto; }
    .portal-container h2 { color: #0f172a; margin-bottom: 8px; }
    .subtitle { color: #64748b; font-size: .9rem; margin-bottom: 24px; }
    .form-row { display: flex; gap: 12px; }
    .form-row .form-group { flex: 1; min-width: 0; }
    .form-group input { width: 100%; }
    .back-link { display: block; text-align: center; margin-top: 16px; color: #2563eb; text-decoration: none; font-weight: 600; }
    .back-link:hover { text-decoration: underline; }
    .message { display: none; padding: 12px; margin-bottom: 16px; border-radius: 8px; font-size: .9rem; }
    .message.success { display: block; background: #dcfce7; color: #166534; }
    .message.error { display: block; background: #fee2e2; color: #991b1b; }
    button:disabled { opacity: .65; cursor: wait; }
    @media(max-width: 420px) { .form-row { flex-direction: column; gap: 0; } }
  </style>
</head>
<body>
  <main class="portal-container card">
    <h2>Crear cuenta bancaria</h2>
    <p class="subtitle">Registra una tarjeta ficticia para utilizarla en el simulador bancario.</p>
    <div id="mensaje" class="message" role="status" aria-live="polite"></div>
    <form id="crear-cuenta-form" autocomplete="off">
      <div class="form-group">
        <label for="titular">Titular</label>
        <input id="titular" name="titular" type="text" maxlength="100" placeholder="Nombre completo" required>
      </div>
      <div class="form-group">
        <label for="numero_tarjeta">Número de tarjeta (16 dígitos)</label>
        <input id="numero_tarjeta" name="numero_tarjeta" type="text" inputmode="numeric" maxlength="16" pattern="[0-9]{16}" placeholder="0000000000000000" required>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="expiracion">Expiración (MM/AA)</label>
          <input id="expiracion" name="expiracion" type="text" maxlength="5" pattern="(0[1-9]|1[0-2])/[0-9]{2}" placeholder="12/29" required>
        </div>
        <div class="form-group">
          <label for="cvv">CVV ficticio (3 o 4 dígitos)</label>
          <input id="cvv" name="cvv" type="text" inputmode="numeric" maxlength="4" pattern="[0-9]{3,4}" placeholder="123" required>
        </div>
      </div>
      <div class="form-group">
        <label for="saldo">Saldo inicial (MXN)</label>
        <input id="saldo" name="saldo" type="number" min="0" max="99999999.99" step="0.01" value="0.00" required>
      </div>
      <button class="pay-btn" id="guardar" type="submit">Guardar cuenta</button>
    </form>
    <a href="banca_cliente.php" class="back-link">Volver al portal bancario</a>
  </main>
  <script>
    const formulario = document.getElementById('crear-cuenta-form');
    const mensaje = document.getElementById('mensaje');
    const boton = document.getElementById('guardar');
    formulario.addEventListener('submit', async (evento) => {
      evento.preventDefault();
      mensaje.className = 'message';
      mensaje.textContent = '';
      boton.disabled = true;
      boton.textContent = 'Guardando...';
      try {
        const respuesta = await fetch('guardar_cuenta.php', { method: 'POST', body: new FormData(formulario) });
        const datos = await respuesta.json();
        mensaje.className = 'message ' + (datos.status === 'success' ? 'success' : 'error');
        mensaje.textContent = datos.mensaje || 'No se pudo procesar la solicitud.';
        if (datos.status === 'success') formulario.reset();
      } catch (error) {
        mensaje.className = 'message error';
        mensaje.textContent = 'No se pudo conectar con el servidor.';
      } finally {
        boton.disabled = false;
        boton.textContent = 'Guardar cuenta';
      }
    });
  </script>
</body>
</html>