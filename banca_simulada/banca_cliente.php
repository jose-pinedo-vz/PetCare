<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Banca en Línea - CVV Dinámico</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .portal-container { max-width: 500px; margin: 40px auto; }
    .data-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e2e8f0; font-size: 0.95rem; }
    .toggle-btn { background: none; border: none; color: #2563eb; cursor: pointer; font-weight: 600; font-size: 0.85rem; margin-left: 8px; }
    .timer-badge { font-size: 0.75rem; background: #fef3c7; color: #d97706; padding: 2px 8px; border-radius: 12px; font-weight: bold; }
  </style>
</head>
<body>

  <div class="portal-container card">
    <h3>🏛️ Portal Banca en Línea</h3>
    <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 15px;">Consulta tus credenciales y tu CVV Dinámico (se renueva cada 5 min).</p>

    <form id="search-card-form">
      <div class="form-group">
        <label for="search_number">Número de Tarjeta (16 dígitos)</label>
        <input type="text" id="search_number" name="card_number" placeholder="0000 0000 0000 0000" required>
      </div>
      <button type="submit" class="pay-btn">Consultar Mi Tarjeta</button>
    </form>

    <div id="card-details" style="display: none; margin-top: 25px;">
      <hr class="divider">
      
      <div class="data-row">
        <span>Titular:</span>
        <strong id="res-titular">-</strong>
      </div>
      <div class="data-row">
        <span>Número de Tarjeta:</span>
        <strong id="res-numero">-</strong>
      </div>
      <div class="data-row">
        <span>Saldo Disponible:</span>
        <strong id="res-saldo" style="color: #10b981;">-</strong>
      </div>
      <div class="data-row">
        <span>Fecha de Expiración:</span>
        <div>
          <strong id="res-expiry">••/••</strong>
          <button type="button" class="toggle-btn" onclick="toggleField('res-expiry', 'expiry')">Mostrar</button>
        </div>
      </div>
      <div class="data-row">
        <span>CVV Dinámico:</span>
        <div style="text-align: right;">
          <strong id="res-cvv">•••</strong>
          <button type="button" class="toggle-btn" onclick="toggleField('res-cvv', 'cvv')">Mostrar</button>
          <br>
          <span class="timer-badge" id="timer-text">Expira en: --:--</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    let realData = { expiry: '', cvv: '' };
    let isVisible = { expiry: false, cvv: false };
    let countdownInterval;
    let secondsLeft = 0;

    document.getElementById('search-card-form').addEventListener('submit', async function(e) {
      e.preventDefault();
      consultarTarjeta();
    });

    async function consultarTarjeta() {
      const form = document.getElementById('search-card-form');
      const formData = new FormData(form);
      const res = await fetch('obtener_datos_tarjeta.php', { method: 'POST', body: formData });
      const data = await res.json();

      if (data.status === 'success') {
        document.getElementById('res-titular').textContent = data.titular;
        document.getElementById('res-numero').textContent = data.numero_tarjeta;
        document.getElementById('res-saldo').textContent = `$${data.saldo} MXN`;
        
        realData.expiry = data.expiracion;
        realData.cvv = data.cvv;
        secondsLeft = data.segundos_restantes;

        if (isVisible.cvv) {
          document.getElementById('res-cvv').textContent = realData.cvv;
        }

        document.getElementById('card-details').style.display = 'block';
        iniciarContador();
      } else {
        alert(data.mensaje);
      }
    }

    function toggleField(elementId, field) {
      const element = document.getElementById(elementId);
      const btn = event.target;

      if (!isVisible[field]) {
        element.textContent = realData[field];
        btn.textContent = 'Ocultar';
        isVisible[field] = true;
      } else {
        element.textContent = field === 'expiry' ? '••/••' : '•••';
        btn.textContent = 'Mostrar';
        isVisible[field] = false;
      }
    }

    function iniciarContador() {
      clearInterval(countdownInterval);
      
      countdownInterval = setInterval(() => {
        if (secondsLeft <= 0) {
          clearInterval(countdownInterval);
          consultarTarjeta(); // Auto-renovar CVV cuando el tiempo llegue a cero
          return;
        }

        secondsLeft--;
        const mins = Math.floor(secondsLeft / 60);
        const secs = secondsLeft % 60;
        document.getElementById('timer-text').textContent = `Expira en: ${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
      }, 1000);
    }
  </script>
</body>
</html>