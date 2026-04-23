<?php include("conexion.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nuestros Proyectos</title>
<link rel="icon" href="Logo.png" type="image/png">
<link rel="stylesheet" href="styles.css">

<style>
.project-text{
    margin-top:25px;
    max-width:1200px;
    line-height:1.6;
    font-size:15px;
    color:#333;
}

.gallery-section{
    padding:0 20px;
    margin-bottom:80px;
}

@media(min-width:900px){
    .gallery-section{
        width:90%;
        max-width:1400px;
        margin:auto;
    }
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

/* HEADER */
header{
    background:#A8ADFF;
    padding:20px 60px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo img{
    height:40px;
}

nav a{
    color:white;
    text-decoration:none;
    margin-left:25px;
}

/* GALERÍA */
.horizontal-gallery{
    display:flex;
    gap:20px;
    overflow-x:auto;
    padding-bottom:15px;
}

.horizontal-gallery img{
    height:200px;
    border-radius:15px;
    cursor:pointer;
    transition: transform 0.3s ease, opacity 0.8s ease;

    opacity: 0;
    transform: translateY(20px);
}

.horizontal-gallery img.show{
    opacity: 1;
    transform: translateY(0);
}

.horizontal-gallery img:hover{
    transform: scale(1.05);
}

/* MAPA */
.map-container{
    display:flex;
    justify-content:center;
    margin-top:40px;
}

.map-container iframe{
    width:100%;
    max-width:1000px;
    height:500px;
    border-radius:15px;
}

/* MENSAJES */
.empty-msg{
    color:#777;
    margin-top:10px;
}

/* MODAL */
.image-modal{
    display:none;
    position:fixed;
    z-index:1000;
    left:0;
    top:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.85);
    justify-content:center;
    align-items:center;
}

.image-modal img{
    max-width:80%;
    max-height:80%;
    border-radius:15px;
}

.close-btn{
    position:absolute;
    top:30px;
    right:40px;
    color:white;
    font-size:40px;
    cursor:pointer;
}

/* FOOTER */
footer{
    text-align:center;
    padding:40px;
}
</style>
</head>

<body>

<header>
    <div class="logo">
        <a href="index.html">
            <img src="Logo.png">
        </a>
    </div>

    <nav>
        <a href="index.html">Inicio</a>
        <a href="Nuestros_Proyectos.php" style="pointer-events:none;">Nuestros Proyectos</a>
        <a href="Sobre_Nosotros.html">Sobre Nosotros</a>
        <a href="Contacto.html">Contacto</a>
        <a href="Sesion.html">Iniciar Sesión</a>
    </nav>
</header>

<?php
$sql = "SELECT * FROM proyectos";
$resultado = $conexion->query($sql);

while($row = $resultado->fetch_assoc()){
?>

<div class="gallery-section">

    <h2><?php echo $row['titulo']; ?></h2>

    <p class="project-text"><?php echo $row['descripcion']; ?></p>

    <!-- GALERÍA -->
    <?php
    $id = $row['id'];
    $imagenes = $conexion->query("SELECT * FROM imagenes_proyecto WHERE id_proyecto = $id");

    if($imagenes->num_rows > 0){
        echo '<div class="horizontal-gallery">';
        while($img = $imagenes->fetch_assoc()){
            echo '<img src="'.$img['ruta'].'">';
        }
        echo '</div>';
    } else {
        echo '<p class="empty-msg">No hay imágenes disponibles</p>';
    }
    ?>

    <br>

    <!-- PDF -->
    <?php if(!empty($row['pdf'])){ ?>
        <a href="<?php echo $row['pdf']; ?>" target="_blank">Ver plano</a>
    <?php } else { ?>
        <p class="empty-msg">No hay plano disponible</p>
    <?php } ?>

    <!-- MAPA -->
    <?php if(!empty($row['mapa'])){ ?>
        <div class="map-container">
            <iframe src="<?php echo $row['mapa']; ?>"></iframe>
        </div>
    <?php } else { ?>
        <p class="empty-msg">Ubicación no disponible</p>
    <?php } ?>

</div>

<?php } ?>

<!-- MODAL -->
<div class="image-modal" id="imageModal">
    <span class="close-btn" id="closeModal">&times;</span>
    <img id="modalImg">
</div>

<footer>
    <div>
        <p><strong>Sistems Inc</strong></p>
        <p>Innovación en proyectos de construcción moderna.</p>
    </div>

    <div>
        <p><strong>Contacto</strong></p>
        <p>constructora@sistemsinc.com</p>
        <p>11 3674-5066</p>
    </div>
</footer>

<script>
const modal = document.getElementById("imageModal");
const modalImg = document.getElementById("modalImg");

let images = [];
let currentIndex = 0;

// abrir modal
document.addEventListener("click", function(e){
    if(e.target.tagName === "IMG" && e.target.parentElement.classList.contains("horizontal-gallery")){
        
        images = Array.from(e.target.parentElement.querySelectorAll("img"));
        currentIndex = images.indexOf(e.target);

        modal.style.display = "flex";
        modalImg.src = images[currentIndex].src;
    }
});

// cerrar
document.getElementById("closeModal").onclick = () => modal.style.display = "none";

// cambiar imagen
function showImage(index){
    if(index < 0) currentIndex = images.length - 1;
    else if(index >= images.length) currentIndex = 0;
    else currentIndex = index;

    modalImg.src = images[currentIndex].src;
}

// teclado
document.addEventListener("keydown", function(e){
    if(modal.style.display === "flex"){
        if(e.key === "ArrowLeft") showImage(currentIndex - 1);
        if(e.key === "ArrowRight") showImage(currentIndex + 1);
        if(e.key === "Escape") modal.style.display = "none";
    }
});


const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting){
            entry.target.classList.add("show");
        }
    });
}, { threshold: 0.2 });

document.querySelectorAll(".horizontal-gallery img").forEach(img => {
    observer.observe(img);
});
</script>

</body>
</html>