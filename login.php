<?php
session_start();
include("conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(empty($_POST['usuario']) || empty($_POST['clave'])){
    die("Acceso no válido");
}

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];

$stmt = $conexion->prepare("SELECT clave FROM Usuarios WHERE usuario = ?");
if(!$stmt) die("Error: " . $conexion->error);

$stmt->bind_param("s", $usuario);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows){
    $fila = $res->fetch_assoc();

    if(password_verify($clave, $fila['clave'])){
        $_SESSION['usuario'] = $usuario;
        header("Location: admin.php");
        exit();
    } else {
        echo "Contraseña incorrecta";
    }

} else {
    echo "Usuario no encontrado";
}
?>