<?php
include("conexion.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$titulo = $conexion->real_escape_string($_POST['titulo']);
$descripcion = $conexion->real_escape_string($_POST['descripcion']);
$mapa = $_POST['mapa'] ?? '';

if (strpos($mapa, 'iframe') !== false) {
    preg_match('/src="([^"]+)"/', $mapa, $m);
    if (isset($m[1])) $mapa = $m[1];
}

$mapa = $conexion->real_escape_string($mapa);

$ruta_pdf = "";

if(!empty($_FILES['pdf']['name'])){
    $pdf_nombre = time() . "_" . $_FILES['pdf']['name'];
    $ruta_pdf = "Documentos/Planos/" . $pdf_nombre;

    if(!move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_pdf)){
        die("Error al subir PDF");
    }
}

if(!$conexion->query("INSERT INTO proyectos (titulo, descripcion, pdf, mapa)
                      VALUES ('$titulo', '$descripcion', '$ruta_pdf', '$mapa')")){
    die("Error: " . $conexion->error);
}

$id_proyecto = $conexion->insert_id;

if(isset($_FILES['imagenes'])){
    foreach($_FILES['imagenes']['name'] as $i => $nombre){
        if(!empty($nombre)){
            $nombre_final = time() . "_" . $nombre;
            $ruta = "Documentos/Proyectos/" . $nombre_final;

            if(move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $ruta)){
                $conexion->query("INSERT INTO imagenes_proyecto (id_proyecto, ruta)
                                  VALUES ($id_proyecto, '$ruta')");
            }
        }
    }
}

echo "Guardado correctamente";
?>