<?php
session_start();

require_once __DIR__ . '/../../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../models/Cliente.php';

const VISTA_RECUPERAR = '../views/cliente/recuperar_contrasena.php';
const MENSAJE_GENERICO = 'Si el correo existe, te enviamos un enlace para restablecer tu contraseña.';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . VISTA_RECUPERAR);
    exit;
}

$correo = is_string($_POST['correo'] ?? null) ? trim($_POST['correo']) : '';

// Validacion de formato (error normal)
$errores = [];
if ($correo === '') {
    $errores[] = 'El correo es obligatorio.';
} elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'Formato de correo inválido.';
} elseif (strlen($correo) > 100) {
    $errores[] = 'El correo no puede tener más de 100 carácteres.';
}

if (count($errores) > 0) {
    $_SESSION['errores'] = $errores;
    $_SESSION['old'] = ['correo' => $correo];
    header('Location: ' . VISTA_RECUPERAR);
    exit;
}

// Oculta el correo en los logs: j***@dominio.com
function correoParaLog(string $correo): string
{
    $partes = explode('@', $correo, 2);
    return substr($partes[0], 0, 1) . '***@' . ($partes[1] ?? '');
}

$cliente = Cliente::buscarPorCorreoActivo($correo);

if ($cliente) {
    $token = bin2hex(random_bytes(32));

    // false si ya se genero uno en el ultimo minuto (anti-abuso): no se envia otro
    if (Cliente::guardarTokenRecuperacion((int) $cliente['id_cliente'], $token)) {
        enviarCorreoRecuperacion($cliente, $correo, $token);
    }
    unset($token);
}

// Siempre el mismo mensaje, exista o no el correo
$_SESSION['exito'] = MENSAJE_GENERICO;
header('Location: ' . VISTA_RECUPERAR);
exit;


function enviarCorreoRecuperacion(array $cliente, string $correo, string $token): void
{
    $rutaConfig = __DIR__ . '/../config/correo.local.php';
    if (!file_exists($rutaConfig)) {
        error_log('Falta app/config/correo.local.php');
        return;
    }
    $config = require $rutaConfig;
    if (!is_array($config)) {
        error_log('Falta app/config/correo.local.php');
        return;
    }

    // Enlace: protocolo + SERVER_NAME (no HTTP_HOST) + ruta del proyecto sacada de SCRIPT_NAME
    $esHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $protocolo = $esHttps ? 'https' : 'http';
    $servidor = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $puertoWeb = (int) ($_SERVER['SERVER_PORT'] ?? 0);
    if ($puertoWeb > 0 && $puertoWeb !== ($esHttps ? 443 : 80)) {
        $servidor .= ':' . $puertoWeb;
    }
    // SCRIPT_NAME = /<proyecto>/app/controllers/cliente_recuperar.php
    $rutaProyecto = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 3)), '/');
    $enlace = $protocolo . '://' . $servidor . $rutaProyecto
        . '/app/views/cliente/restablecer_contrasena.php?token=' . urlencode($token);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = (string) ($config['host'] ?? '');
        $mail->Port = (int) ($config['puerto'] ?? 587);
        $mail->SMTPAuth = true;
        $mail->Username = (string) ($config['usuario'] ?? '');
        $mail->Password = (string) ($config['password'] ?? '');
        $mail->SMTPSecure = (($config['cifrado'] ?? 'tls') === 'ssl')
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->SMTPDebug = 0;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom((string) ($config['remitente_correo'] ?? ''), (string) ($config['remitente_nombre'] ?? 'PetCare'));
        $mail->addAddress($correo, (string) $cliente['nombre']);
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña - PetCare';
        $enlaceHtml = htmlspecialchars($enlace, ENT_QUOTES, 'UTF-8');
        $mail->Body = 'Hola, <b>' . htmlspecialchars((string) $cliente['nombre'], ENT_QUOTES, 'UTF-8') . '</b>.<br>'
            . 'Solicitaste restablecer tu contraseña de PetCare. Haz clic en el siguiente enlace para cambiarla (expira en 1 hora):<br><br>'
            . '<a href="' . $enlaceHtml . '">' . $enlaceHtml . '</a><br><br>'
            . 'Si no fuiste tú, ignora este correo; tu contraseña no cambiará.';
        $mail->AltBody = 'Para restablecer tu contraseña de PetCare abre este enlace (expira en 1 hora): ' . $enlace;

        // Primero se conecta con la verificacion SSL activada
        $conectado = false;
        try {
            $conectado = $mail->smtpConnect();
        } catch (\Exception $e) {
            $conectado = false;
        }

        if (!$conectado) {
            $smtp = $mail->getSMTPInstance();
            $detalle = $mail->ErrorInfo . ' ' . json_encode($smtp->getError());
            $esLocal = in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1'], true);
            $fallaCertificado = stripos($detalle, 'certificate') !== false || stripos($detalle, 'SSL') !== false;

            if ($esLocal && $fallaCertificado) {
                // SOLO en desarrollo local (XAMPP): algunos PHP de Windows no traen el paquete de
                // certificados raiz (cacert.pem) y la verificacion TLS falla aunque el servidor SMTP
                // sea legitimo. En ese caso unico se relaja la verificacion para poder probar.
                // En cualquier otro servidor la verificacion se queda activada.
                $mail->smtpClose();
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ],
                ];
                $conectado = $mail->smtpConnect();
            }
        }

        if (!$conectado) {
            throw new \Exception('No se pudo conectar al servidor SMTP.');
        }

        $mail->send();
        $mail->smtpClose();
    } catch (\Exception $e) {
        // Sin token ni correo completo en el log
        $error = $mail->ErrorInfo !== '' ? $mail->ErrorInfo : $e->getMessage();
        $error = str_replace([$token, $correo], ['[token]', correoParaLog($correo)], $error);
        error_log('Error al enviar correo de recuperacion de cliente (' . correoParaLog($correo) . '): ' . $error);
    }
}
