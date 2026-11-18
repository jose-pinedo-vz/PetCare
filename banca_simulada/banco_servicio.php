<?php
// Banco emisor simulado, para usarlo desde PetCare (Ventas) sin pasar por HTTP.
// Usa las mismas tablas y el mismo CVV dinamico que api_banco.php y banca_cliente.php.

const BANCO_CLAVE_CVV = 'ClaveSecretaBanco123';

// Conexion propia al banco (autocommit), separada de la transaccion de la venta
function bancoConexion(): PDO
{
    $leer = function (string $clave, string $defecto): string {
        $v = $_ENV[$clave] ?? $_SERVER[$clave] ?? getenv($clave);
        return ($v === false || $v === null || $v === '') ? $defecto : (string)$v;
    };
    $dsn = 'mysql:host=' . $leer('DB_HOST', 'localhost') . ';port=' . $leer('DB_PORT', '3306')
         . ';dbname=' . $leer('DB_NAME', 'petcare') . ';charset=utf8mb4';
    return new PDO($dsn, $leer('DB_USER', 'root'), $leer('DB_PASS', ''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
}

// CVV dinamico de una tarjeta para un bloque de 5 minutos
function bancoCvvDelBloque(string $tarjeta, int $bloque): string
{
    $hash = hash_hmac('sha256', $tarjeta . $bloque, BANCO_CLAVE_CVV);
    return str_pad((string)(hexdec(substr($hash, 0, 4)) % 1000), 3, '0', STR_PAD_LEFT);
}

// Acepta el CVV del bloque actual y el anterior (por si cambia justo al cobrar)
function bancoCvvValido(string $tarjeta, string $cvv): bool
{
    $bloque = (int)floor(time() / 300);
    return hash_equals(bancoCvvDelBloque($tarjeta, $bloque), $cvv)
        || hash_equals(bancoCvvDelBloque($tarjeta, $bloque - 1), $cvv);
}

// Guarda la operacion en la bitacora del banco
function bancoRegistrar(PDO $pdo, string $folio, string $tarjeta, float $monto, string $comercio, string $codigo, string $estado): void
{
    $stmt = $pdo->prepare(
        "INSERT INTO transacciones_bancarias (folio, numero_tarjeta, monto, comercio, codigo_respuesta, estado_transaccion)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$folio, $tarjeta, $monto, $comercio, $codigo, $estado]);
}

// Autoriza un cobro. $vencimiento llega como AAAA-MM (campo month de la pantalla).
// Devuelve ['ok'=>true,'folio'=>..] o ['ok'=>false,'error'=>..,'codigo'=>..]
function bancoAutorizarCobro(string $tarjeta, string $vencimiento, string $cvv, float $monto, string $comercio): array
{
    $monto = round($monto, 2);
    if ($monto <= 0 || !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $vencimiento)) {
        return ['ok' => false, 'codigo' => '14', 'error' => 'Datos de tarjeta o monto invalidos.'];
    }
    $expiracion = substr($vencimiento, 5, 2) . '/' . substr($vencimiento, 2, 2); // MM/AA

    try {
        $pdo = bancoConexion();
        $pdo->beginTransaction();

        // Bloquea la cuenta mientras se decide, para que dos cobros no gasten el mismo saldo
        $stmt = $pdo->prepare("SELECT id, saldo, estado, expiracion FROM cuentas_bancarias WHERE numero_tarjeta = ? FOR UPDATE");
        $stmt->execute([$tarjeta]);
        $cuenta = $stmt->fetch();

        $codigo = '00';
        $mensaje = '';
        if (!$cuenta) {
            $codigo = '14';
            $mensaje = 'Tarjeta no encontrada en el banco.';
        } elseif ($cuenta['estado'] !== 'activa') {
            $codigo = '05';
            $mensaje = 'Tarjeta bloqueada por el banco.';
        } elseif ($cuenta['expiracion'] !== $expiracion || !bancoCvvValido($tarjeta, $cvv)) {
            $codigo = '55';
            $mensaje = 'Datos de la tarjeta incorrectos o el CVV dinamico ha expirado.';
        } elseif ((float)$cuenta['saldo'] < $monto) {
            $codigo = '51';
            $mensaje = 'Transaccion rechazada: fondos insuficientes.';
        }

        $autorizado = ($codigo === '00');
        $folio = ($autorizado ? 'AUT-' : 'REC-') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        if ($autorizado) {
            $stmt = $pdo->prepare("UPDATE cuentas_bancarias SET saldo = saldo - ? WHERE id = ? AND saldo >= ?");
            $stmt->execute([$monto, $cuenta['id'], $monto]);
            if ($stmt->rowCount() !== 1) {
                $pdo->rollBack();
                return ['ok' => false, 'codigo' => '51', 'error' => 'Transaccion rechazada: fondos insuficientes.'];
            }
        }
        bancoRegistrar($pdo, $folio, $tarjeta, $monto, $comercio, $codigo, $autorizado ? 'Autorizado' : 'Rechazado');
        $pdo->commit();

        if (!$autorizado) {
            return ['ok' => false, 'codigo' => $codigo, 'error' => $mensaje];
        }
        return ['ok' => true, 'codigo' => '00', 'folio' => $folio];
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error en bancoAutorizarCobro: ' . $e->getMessage());
        return ['ok' => false, 'codigo' => '96', 'error' => 'No se pudo comunicar con el banco. Intenta de nuevo.'];
    }
}

// Devuelve el dinero de un cobro autorizado (si la venta no se pudo guardar)
function bancoReversarCobro(string $folio): bool
{
    try {
        $pdo = bancoConexion();
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "SELECT numero_tarjeta, monto FROM transacciones_bancarias
             WHERE folio = ? AND estado_transaccion = 'Autorizado' FOR UPDATE"
        );
        $stmt->execute([$folio]);
        $tx = $stmt->fetch();
        if (!$tx) {
            $pdo->rollBack();
            return false;
        }

        $stmt = $pdo->prepare("UPDATE cuentas_bancarias SET saldo = saldo + ? WHERE numero_tarjeta = ?");
        $stmt->execute([$tx['monto'], $tx['numero_tarjeta']]);
        $stmt = $pdo->prepare("UPDATE transacciones_bancarias SET estado_transaccion = 'Reversado' WHERE folio = ?");
        $stmt->execute([$folio]);

        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Error en bancoReversarCobro (' . $folio . '): ' . $e->getMessage());
        return false;
    }
}
