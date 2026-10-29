<?php
require_once '../../PHPMailer/src/Exception.php';
require_once '../../PHPMailer/src/PHPMailer.php';
require_once '../../PHPMailer/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../models/usuarios_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');

    if (empty($correo)) {
        $error = "Por favor, ingresa tu correo electrónico.";
        include '../views/recuperar_contraseña.php';
        exit();
    }

    $usuario = UsuariosDB::buscarCorreo($correo);

    if ($usuario) {
        $token = bin2hex(random_bytes(32));
        $expiracion = date("Y-m-d H:i:s", strtotime("+1 hour"));

        $update = UsuariosDB::tipoUsuario($usuario['tipo'],$token,$expiracion,$correo);

        $protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";

        $host = $_SERVER['HTTP_HOST'];

        $ruta_proyecto = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/\\');

        $enlace = $protocolo . "://" . $host . $ruta_proyecto . "/app/views/restablecer_contraseña.php?token=" . $token;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';             
            $mail->SMTPAuth   = true;                         
            $mail->Username   = 'veterinariapetcare8@gmail.com';
            $mail->Password   = 'yqrf wjql wfed bvqm';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587;    

            $mail->SMTPOptions = array('ssl' => array('verify_peer' => false,
                                                    'verify_peer_name' => false,
                                                    'allow_self_signed' => true));

            $mail->setFrom('veterinariapetcare8@gmail.com', 'Soporte PetCare');   // CONTRASEÑA DEL CORREO: contrasenapetcare (Y usa mi numero)
            $mail->addAddress($correo, $usuario['nombre']);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña - PetCare';
            $mail->Body    = "Hola, <b>" . htmlspecialchars($usuario['nombre']) . "</b>.<br>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para cambiarla (expira en 1 hora):<br><br><a href='" . $enlace . "'>" . $enlace . "</a>";
            $mail->CharSet = 'UTF-8';

            $mail->send();
            $mensaje = "Se han enviado las instrucciones a tu correo electrónico.";
        } catch (Exception $e) {
            $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "El correo electrónico no está registrado.";
    }

    include '../views/recuperar_contraseña.php';
}