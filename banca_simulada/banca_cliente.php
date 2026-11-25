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
    <h3>Portal banca en línea</h3>
    <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 15px;">Consulta tus credenciales y tu CVV Dinámico (se renueva cada 5 min).</p>

    <form id="search-card-form">
      <div class="form-group">
        <label for="search_number">Número de tarjeta (16 dígitos)</label>
        <input type="text" id="search_number" name="card_number" placeholder="0000 0000 0000 0000" required>
      </div>
      <button type="submit" class="pay-btn">Consultar mi tarjeta</button>
      <a href="crear_cuenta.php" class="create-btn">Crear cuenta</a>
    </form>

    <div id="card-details" style="display: none; margin-top: 25px;">
      <hr class="divider">
      
      <div class="data-row">
        <span>Titular:</span>
        <strong id="res-titular">-</strong>
      </div>
      <div class="data-row">
        <span>Número de tarjeta:</span>
        <strong id="res-numero">-</strong>
      </div>
      <div class="data-row">
        <span>Saldo disponible:</span>
        <strong id="res-saldo" style="color: #10b981;">-</strong>
      </div>
      <div class="data-row">
        <span>Fecha de expiración:</span>
        <div>
          <strong id="res-expiry">••/••</strong>
          <button type="button" class="toggle-btn" onclick="toggleField('res-expiry', 'expiry', this)">Mostrar</button>
        </div>
      </div>
      <div class="data-row">
        <span>CVV Dinámico:</span>
        <div style="text-align: right;">
          <strong id="res-cvv">•••</strong>
          <button type="button" class="toggle-btn" onclick="toggleField('res-cvv', 'cvv', this)">Mostrar</button>
          <br>
        </div>
      </div>
    </div>
  </div>

  <script>
    let realData = { expiry: '', cvv: '' };
    let isVisible = { expiry: false, cvv: false };
    
    document.getElementById('search-card-form')
        .addEventListener('submit', function(e) {
            e.preventDefault();
            consultarTarjeta();
        });
    
    async function consultarTarjeta() {
        const form = document.getElementById('search-card-form');
    
        try {
            const res = await fetch('obtener_datos_tarjeta.php', {
                method: 'POST',
                body: new FormData(form)
            });
    
            const data = await res.json();
    
            if (data.status !== 'success') {
                alert(data.mensaje);
                return;
            }
    
            document.getElementById('res-titular').textContent = data.titular;
            document.getElementById('res-numero').textContent = data.numero_tarjeta;
            document.getElementById('res-saldo').textContent = `$${data.saldo} MXN`;
    
            realData.expiry = data.expiracion;
            realData.cvv = data.cvv;
    
            isVisible.expiry = false;
            isVisible.cvv = false;
    
            document.getElementById('res-expiry').textContent = '••/••';
            document.getElementById('res-cvv').textContent = '•••';
    
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.textContent = 'Mostrar';
            });
    
            document.getElementById('card-details').style.display = 'block';
    
        } catch (error) {
            alert('No se pudo consultar la tarjeta.');
        }
    }
    
    function toggleField(elementId, field, btn) {
        const element = document.getElementById(elementId);
    
        isVisible[field] = !isVisible[field];
    
        element.textContent = isVisible[field]
            ? realData[field]
            : field === 'expiry' ? '••/••' : '•••';
    
        btn.textContent = isVisible[field] ? 'Ocultar' : 'Mostrar';
    }
  </script>
</body>
</html>