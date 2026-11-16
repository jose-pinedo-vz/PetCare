<?php
// Plantilla de credenciales SMTP.
// Copia este archivo como correo.local.php (misma carpeta) y pon los valores reales.
// correo.local.php esta en .gitignore y NUNCA debe subirse al repositorio.
// cifrado: 'tls' = STARTTLS (puerto 587), 'ssl' = SMTPS (puerto 465)
return array(
    'host' => 'smtp.ejemplo.com',
    'puerto' => 587,
    'cifrado' => 'tls',
    'usuario' => 'cuenta@ejemplo.com',
    'password' => 'contrasena-de-aplicacion',
    'remitente_correo' => 'cuenta@ejemplo.com',
    'remitente_nombre' => 'Soporte PetCare',
);
