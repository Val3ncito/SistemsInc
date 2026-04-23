<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = $_POST["nombre"] ?? '';
    $email    = $_POST["email"] ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $mensaje  = $_POST["mensaje"] ?? '';

    $destino = "constructora@sistemsinc.com";
    $asunto  = "Nuevo mensaje - Sistems Inc";

    $contenido =
        "Nuevo mensaje:\n\n" .
        "Nombre: $nombre\n" .
        "Email: $email\n" .
        "Teléfono: $telefono\n\n" .
        "Mensaje:\n$mensaje";

    $headers =
        "From: constructora@sistemsinc.com\r\n" .
        "Reply-To: $email\r\n" .
        "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($destino, $asunto, $contenido, $headers)) {
        echo "<script>alert('Mensaje enviado correctamente');location.href='Contacto.html';</script>";
    } else {
        echo "<script>alert('Error al enviar mensaje');history.back();</script>";
    }
}
?>