<?php

$server = "localhost";
$user = "root";
$passwword = "";
$db = "petcare";

$conexion = new mysqli($server, $user, $passwword, $db);

mysqli_set_charset($conexion, "utf8");