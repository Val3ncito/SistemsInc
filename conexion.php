<?php
$host = "localhost";
$usuario = "u758850771_Valentino";
$clave = "Panasonic307$";
$bd = "u758850771_SistemsInc";

$conexion = new mysqli($host, $usuario, $clave, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>