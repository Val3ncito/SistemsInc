<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = $_POST["nombre"] ?? '';
    $email    = $_POST["email"] ?? '';
    $telefono = $_POST["telefono"] ?? '';
    $mensaje  = $_POST["mensaje"] ?? '';

    $destino = "constructora@sistemsinc.com";
    $asunto  = "Nuevo mensaje desde la web - Sistems Inc";

    $contenido  = "Has recibido un nuevo mensaje:\r\n\r\n";
    $contenido .= "Nombre: $nombre\r\n";
    $contenido .= "Email: $email\r\n";
    $contenido .= "Teléfono: $telefono\r\n\r\n";
    $contenido .= "Mensaje:\r\n$mensaje\r\n";

    $headers  = "From: constructora@sistemsinc.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($destino, $asunto, $contenido, $headers)) {
        echo "<script>
                alert('Mensaje enviado correctamente');
                window.location.href='Contacto.html';
              </script>";
    } else {
        echo "ERROR AL ENVIAR";
    }
}

?>