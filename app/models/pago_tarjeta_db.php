<?php
declare(strict_types=1);
if (file_exists(__DIR__ . '/conexion_db.php'))
{
    require_once __DIR__ . '/conexion_db.php';
}
elseif (file_exists(__DIR__ . '/../../conexion_db.php'))
{
    require_once __DIR__ . '/../../conexion_db.php';
}
class PasarelaBancariaSimulada {

    /**
     * Valida el número de tarjeta usando el Algoritmo de Luhn
     */
    private static function validarLuhn($numero) {$numero = preg_replace('/\D/', '', $numero);$total = 0;
        $i = strlen($numero);$par = false;

        while ($i--) {$ch = $numero[$i];
            if ($par) {$ch *= 2;
                if ($ch > 9) {$ch -= 9;
                }
            }
            $total +=$ch;
            $par = !$par;
        }

        return ($total % 10 === 0);
    }

    private static function validarVencimiento($fechaMMYY) {
        if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $fechaMMYY,$matches)) {
            return false;
        }

        $mes = (int)$matches[1];
        $anio = (int)("20" . $matches[2]);

        $mesActual = (int)date('m');$anioActual = (int)date('Y');

        if ($anio <$anioActual) {
            return false;
        }
        if ($anio ===$anioActual && $mes <$mesActual) {
            return false;
        }

        return true;
    }

    /**
     * Procesa la transacción simulada
     */
    public static function procesarPago($numeroTarjeta, $nombreTitular,$fechaVencimiento, $cvv,$monto) {
        $tarjetaLimpia = preg_replace('/\D/', '',$numeroTarjeta);

        // 1. Validar longitud de tarjeta (13 a 19 dígitos)
        if (strlen($tarjetaLimpia) < 13 || strlen($tarjetaLimpia) > 19) {
            return [
                'estatus' => 'Rechazado',
                'motivo' => 'Longitud de tarjeta inválida'
            ];
        }

        // 2. Validar Luhn
        if (!self::validarLuhn($tarjetaLimpia)) {
            return [
                'estatus' => 'Rechazado',
                'motivo' => 'Número de tarjeta inválido (error de checksum)'
            ];
        }

        // 3. Validar Vencimiento
        if (!self::validarVencimiento($fechaVencimiento)) {
            return [
                'estatus' => 'Rechazado',
                'motivo' => 'Tarjeta vencida o fecha con formato incorrecto'
            ];
        }

        // 4. Validar CVV
        if (!preg_match('/^[0-9]{3,4}$/',$cvv)) {
            return [
                'estatus' => 'Rechazado',
                'motivo' => 'Código CVV inválido (debe tener 3 o 4 dígitos)'
            ];
        }

        // 5. Simular aprobación / rechazo (ejemplo: si termina en 00 rechazar por fondos insuficientes)
        if (substr($tarjetaLimpia, -2) === '00') {
            return [
                'estatus' => 'Rechazado',
                'motivo' => 'Fondos insuficientes (Simulado)'
            ];
        }

        // 6. Pago Aprobado: Generar Folio de Autorización
        $folioAutorizacion = "AUTH-" . strtoupper(bin2hex(random_bytes(4))) . "-" . date('Ymd');

        return [
            'estatus' => 'Aprobado',
            'referencia_pago' => $folioAutorizacion,
            'ultimos_digitos' => substr($tarjetaLimpia, -4)
        ];
    }

    public static function guardarPago()
    {
        $conexion = ConexionDB::obtenerConexion();
        $consulta = $conexion -> prepare(
            "INSERT INTO pagos (
                id_venta, id_cita, id_reservacion, id_cliente, fecha, 
                tipo_pago, porcentaje_anticipo, importe, forma_pago, 
                ultimos_digitos, referencia_pago, id_empleado_receptor, 
                estado, motivo_rechazo
            ) values( 
                :id_venta, :id_cita, :id_reservacion, :id_cliente, NOW(),
                :tipo_pago, :porcentaje_anticipo, :importe, 'Tarjeta',
                :ultimos_digitos, :referencia_pago, :id_empleado_receptor,
                :estado_pago, :motivo_rechazo
            );"
        ); 
    }
}