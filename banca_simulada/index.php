<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST')// {die('No se recibieron datos de una venta.');}

$id_cliente = !empty($_POST['id_cliente']) ? (int)$_POST['id_cliente'] : null;

$productos_json = $_POST['productos_json'] ?? '[]';
$servicios_json = $_POST['servicios_json'] ?? '[]';

$productos = json_decode($productos_json, true);
$servicios = json_decode($servicios_json, true);

if (!is_array($productos)) {$productos = [];}
if (!is_array($servicios)) {$servicios = [];}

$subtotal = isset($_POST['subtotal']) ? (float)$_POST['subtotal'] : 0;
$iva = isset($_POST['iva']) ? (float)$_POST['iva'] : 0;
$total = isset($_POST['total']) ? (float)$_POST['total'] : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Módulo de Pago con Tarjeta</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <div class="checkout-container">
    <!-- Resumen -->        <!-- Se llenaran con la venta realizada -->
    <div class="left-column">
      <div class="card summary-card">
        <h3>Resumen de Pago</h3>
        <hr class="divider">
          <?php foreach ($productos as $producto): ?>
          <div class="summary-item">
            <span>
              <?php echo htmlspecialchars($producto['nombre'] ?? 'Producto') ?>
              <?php if (($producto['cantidad'] ?? 1) > 1): ?>
                x<?php echo (int)$producto['cantidad'] ?>
              <?php endif; ?>
            </span>
            <span>
              $<?php echo number_format((($producto['precio'] ?? 0) * ($producto['cantidad'] ?? 1))- ($producto['descuento'] ?? 0),2) ?> MXN
            </span>
          </div>
          <?php endforeach; ?>
          <?php foreach ($servicios as $servicio): ?>
            <div class="summary-item">
              <span>
                <?php echo htmlspecialchars($servicio['nombre'] ?? 'Servicio') ?>
              </span>
              <span>$<?php echo number_format((($servicio['precio'] ?? 0) * ($servicio['cantidad'] ?? 1))- ($servicio['descuento'] ?? 0),2) ?> MXN
              </span>
            </div>
          <?php endforeach; ?>
      </div>
      <div class="summary-total">
        <span>Total a Pagar:</span>
        <span class="total-amount">
          $<?php echo number_format($total, 2) ?> MXN
        </span>
      </div>
      <!-- Resumen -->        <!-- Se llenaran con la venta realizada -->

      <!-- Banco -->
      <div class="card bank-card">
        <div class="bank-header">
          <span>🏛️ ESTADO DE LA CUENTA DEL CLIENTE (BANCO EMISOR)</span>
        </div>
        <p class="balance-label">Saldo disponible:</p>
        <div class="balance-amount">$0.00 MXN</div>
        <a href="banca_cliente.php" target="_blank" style="color: #60a5fa; font-size: 0.85rem; display: inline-block; margin-top: 10px; text-decoration: none;">
            🔑 Consultar datos de mi tarjeta (Banca en Línea) →
        </a>
        <label class="input-label" for="bank_response">Respuesta simulada del Banco:</label>
        <select id="bank_response" name="bank_response" class="bank-select" form="payment-form">
          <option value="auto">Validación Automática (Real)</option>
          <option value="decline">Forzar Rechazo (Fondos Insuficientes)</option>
          <option value="error">Forzar Error de Conexión</option>
        </select>
      </div>
      <!-- Banco -->
    </div>

    <!-- Tarjeta -->
    <div class="right-column card payment-card">
      <div class="credit-card-preview">
        <div class="chip"></div>
        <div class="card-number" id="preview-number">•••• •••• •••• ••••</div>
        <div class="card-footer">
          <div class="card-holder-group">
            <span class="card-label">TITULAR</span>
            <span class="card-value" id="preview-name">NOMBRE Y APELLIDO</span>
          </div>
          <div class="card-expires-group">
            <span class="card-label">EXPIRES</span>
            <span class="card-value" id="preview-expiry">MM/AA</span>
          </div>
        </div>
      </div>
      <!-- Tarjeta -->

      <!-- Formulario -->
      <form id="payment-form">
        <input type="hidden" name="monto" value="<?php echo $total ?>">
        <div class="form-group">
          <label for="card_name">Nombre del Titular</label>
          <input type="text" id="card_name" name="card_name" placeholder="" required>
        </div>
        <div class="form-group">
          <label for="card_number">Número de Tarjeta</label>
          <input type="text" id="card_number" name="card_number" placeholder="0000 0000 0000 0000" maxlength="19" required>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="card_expiry">Expiración</label>
            <input type="text" id="card_expiry" name="card_expiry" placeholder="00/00" maxlength="5" required>
          </div>
          <div class="form-group">
            <label for="card_cvv">CVV</label>
            <input type="password" id="card_cvv" name="card_cvv" placeholder="---" maxlength="4" required>
          </div>
        </div>
        <!-- Formulario -->

        <button type="submit" class="pay-btn" id="pay-button">Confirmar Pago</button>
      </form>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>