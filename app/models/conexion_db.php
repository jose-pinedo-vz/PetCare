<?php

$server = "localhost";
$user = "root";
$passwword =  "";
$db = "";

$conexion = new mysqli($server, $user, $passwword, $db);

if ($conexion -> connect_errno){
    die("Conexion fallida" . $conexion->connect_errno);
}else{
    echo  "Conectado";
}