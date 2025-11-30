<?php
session_start();
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['uid'])) {
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $autor = mysqli_real_escape_string($conn, $_POST['autor']);
    $cat = mysqli_real_escape_string($conn, $_POST['categoria']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $uid = $_SESSION['uid'];
    $nombreArchivo = time() . "_" . basename($_FILES["archivo"]["name"]);
    $ruta = "uploads/" . $nombreArchivo;
    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta)) {
        $sql = "INSERT INTO recursos (titulo, autor_nombre, categoria, descripcion, archivo_pdf, usuario_id) 
                VALUES ('$titulo', '$autor', '$cat', '$desc', '$ruta', $uid)";
        mysqli_query($conn, $sql);
        header("Location: index.php");
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Subir</title><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar"><div class="logo">BiblioShare</div><a href="index.php">Cancelar</a></nav>
    <div class="auth-container">
        <h2>Publicar Recurso</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="titulo" class="form-input" placeholder="Título de la obra" required>
            <input type="text" name="autor" class="form-input" placeholder="Autor principal" required>
            <select name="categoria" class="form-input">
                <option>Ciencias</option><option>Arte</option><option>Historia</option><option>Ingeniería</option>
            </select>
            <textarea name="desc" class="form-input" placeholder="Descripción breve"></textarea>
            <label style="font-size:0.9rem; font-weight:bold;">Archivo PDF:</label>
            <input type="file" name="archivo" class="form-input" accept=".pdf" required>
            <button type="submit" class="btn-primary">Publicar Ahora</button>
        </form>
    </div>
</body>
</html>