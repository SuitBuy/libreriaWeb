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
<head><title>Subir Obra</title><link rel="stylesheet" href="estilos.css"></head>
<body style="background:#f1f5f9;">
    <nav class="navbar"><div class="logo">BiblioShare</div><a href="index.php">Cancelar</a></nav>
    <div class="auth-wrapper" style="max-width:600px;">
        <h2>Publicar Recurso</h2>
        <form method="POST" enctype="multipart/form-data" style="margin-top:20px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <input type="text" name="titulo" class="input-field" placeholder="Título de la obra" required>
                <input type="text" name="autor" class="input-field" placeholder="Autor principal" required>
            </div>
            <select name="categoria" class="input-field" style="margin-top:15px;">
                <option>Ciencias</option><option>Arte</option><option>Historia</option><option>Ingeniería</option>
            </select>
            <textarea name="desc" class="input-field" placeholder="Descripción breve..." style="margin-top:15px;" rows="3"></textarea>
            
            <div style="margin:20px 0; padding:20px; border:2px dashed #cbd5e1; border-radius:15px; text-align:center;">
                <p>Sube tu PDF aquí</p>
                <input type="file" name="archivo" accept=".pdf" required>
            </div>
            
            <button type="submit" class="btn-login" style="width:100%; border:none; cursor:pointer;">Publicar Ahora</button>
        </form>
    </div>
</body>
</html>