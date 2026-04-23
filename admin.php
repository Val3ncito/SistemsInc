<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: Sesion.html");
    exit();
}

include("conexion.php");

$mensaje = "";

/* GUARDAR */
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $titulo = $conexion->real_escape_string($_POST['titulo']);
    $descripcion = $conexion->real_escape_string($_POST['descripcion']);
    $mapa = $_POST['mapa'];

    if(strpos($mapa, 'iframe') !== false){
        preg_match('/src="([^"]+)"/', $mapa, $m);
        $mapa = $m[1] ?? $mapa;
    }

    $mapa = $conexion->real_escape_string($mapa);

    $ruta_pdf = "";
    if(!empty($_FILES['pdf']['name'])){
        $ruta_pdf = "Documentos/Planos/".time()."_".$_FILES['pdf']['name'];
        move_uploaded_file($_FILES['pdf']['tmp_name'], $ruta_pdf);
    }

    $conexion->query("INSERT INTO proyectos (titulo, descripcion, pdf, mapa)
                      VALUES ('$titulo','$descripcion','$ruta_pdf','$mapa')");

    $id = $conexion->insert_id;

    if(isset($_FILES['imagenes'])){
        foreach($_FILES['imagenes']['name'] as $k => $n){
            if(!$n) continue;

            $ruta = "Documentos/Proyectos/".time()."_".$n;

            if(move_uploaded_file($_FILES['imagenes']['tmp_name'][$k], $ruta)){
                $conexion->query("INSERT INTO imagenes_proyecto (id_proyecto, ruta)
                                  VALUES ($id,'$ruta')");
            }
        }
    }

    $mensaje = "Guardado correctamente";
}

/* ELIMINAR */
if(isset($_GET['eliminar'])){
    $id = (int)$_GET['eliminar'];

    $res = $conexion->query("SELECT pdf FROM proyectos WHERE id=$id")->fetch_assoc();
    if(!empty($res['pdf']) && file_exists($res['pdf'])) unlink($res['pdf']);

    $imgs = $conexion->query("SELECT ruta FROM imagenes_proyecto WHERE id_proyecto=$id");
    while($i = $imgs->fetch_assoc()){
        if(file_exists($i['ruta'])) unlink($i['ruta']);
    }

    $conexion->query("DELETE FROM imagenes_proyecto WHERE id_proyecto=$id");
    $conexion->query("DELETE FROM proyectos WHERE id=$id");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Admin</title>

<style>
body{background:#c9ccff;font-family:Arial;padding:20px;}

form,.proyecto{background:white;padding:15px;border-radius:10px;margin-bottom:20px;}

input,textarea{display:block;margin-bottom:10px;padding:8px;}
input{width:300px;}
textarea{width:100%;resize:vertical;}

button{padding:8px;border:none;border-radius:5px;cursor:pointer;}

.eliminar{background:red;color:white;}

img{height:80px;margin:5px;}

.file-btn{
    display:inline-block;
    background:#A8ADFF;
    color:white;
    padding:10px;
    border-radius:5px;
    cursor:pointer;
    margin-bottom:10px;
}
.file-btn:hover{background:#8f95ff;}
</style>
</head>

<body>

<h1>Bienvenido <?php echo $_SESSION['usuario']; ?></h1>

<form method="POST" enctype="multipart/form-data">
    <input type="text" name="titulo" placeholder="Título" required>
    <textarea name="descripcion" placeholder="Descripción" rows="8"></textarea>

    <label class="file-btn">
        Elegir imágenes
        <input type="file" name="imagenes[]" multiple hidden>
    </label>

    <label class="file-btn">
        Elegir PDF
        <input type="file" name="pdf" hidden>
    </label>

    <textarea name="mapa" placeholder="Link o iframe de Google Maps" rows="4"></textarea>

    <button>Guardar</button>
</form>

<h2>Proyectos cargados</h2>

<?php
$res = $conexion->query("SELECT * FROM proyectos");

while($row = $res->fetch_assoc()){
?>

<div class="proyecto">
    <h3><?php echo $row['titulo']; ?></h3>
    <p><?php echo $row['descripcion']; ?></p>

    <?php
    $imgs = $conexion->query("SELECT ruta FROM imagenes_proyecto WHERE id_proyecto=".$row['id']);
    while($img = $imgs->fetch_assoc()){
        echo '<img src="'.$img['ruta'].'">';
    }
    ?>

    <?php if(!empty($row['pdf'])){ ?>
        <br><a href="<?php echo $row['pdf']; ?>" target="_blank">Ver PDF</a>
    <?php } ?>

    <br><br>

    <a href="?eliminar=<?php echo $row['id']; ?>"
       onclick="return confirm('¿Eliminar proyecto?');">
        <button class="eliminar">Eliminar</button>
    </a>
</div>

<?php } ?>

<a href="index.html">Cerrar sesión</a>

<?php if($mensaje){ ?>
<script>alert("<?php echo $mensaje; ?>");</script>
<?php } ?>

</body>
</html>