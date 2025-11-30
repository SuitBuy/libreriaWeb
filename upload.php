<?php
session_start();
include 'db.php';

if (!isset($_SESSION['uid'])) { header("Location: login.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $autor = mysqli_real_escape_string($conn, $_POST['autor']);
    $cat = mysqli_real_escape_string($conn, $_POST['categoria']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $uid = $_SESSION['uid'];
    
    // Archivo
    $nombreOriginal = $_FILES["archivo"]["name"];
    $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
    $nombreArchivo = time() . "_" . basename($nombreOriginal);
    $ruta = "uploads/" . $nombreArchivo;
    
    $permitidos = array("pdf", "doc", "docx", "jpg", "png");

    if (in_array($ext, $permitidos)) {
        if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta)) {
            // INSERTAR COMO PENDIENTE
            $sql = "INSERT INTO recursos (titulo, autor_nombre, categoria, descripcion, archivo_pdf, usuario_id, estado, tipo_archivo) 
                    VALUES ('$titulo', '$autor', '$cat', '$desc', '$ruta', $uid, 'pendiente', '$ext')";
            mysqli_query($conn, $sql);
            header("Location: index.php?msg=uploaded"); 
            exit;
        }
    } else {
        $error = "Formato no permitido (Solo PDF, Word, JPG, PNG)";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subir - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php">Cancelar</a>
    </nav>
    <div class="auth-wrapper" style="max-width:600px;">
        <h2>Publicar Aporte</h2>
        <p style="color:#64748b; margin-bottom:20px;">Tu archivo pasará a revisión por un administrador.</p>
        
        <?php if(isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <input type="text" name="titulo" class="input-field" placeholder="Título" required>
                <input type="text" name="autor" class="input-field" placeholder="Autor" required>
            </div>
            <select name="categoria" class="input-field">
                <option>Ciencias</option><option>Arte</option><option>Historia</option><option>Ingeniería</option><option>Otros</option>
            </select>
            <textarea name="desc" class="input-field" placeholder="Descripción breve..." rows="3"></textarea>
            
            <div style="margin:20px 0; padding:20px; border:2px dashed #cbd5e1; border-radius:15px; text-align:center;">
                <p>Formatos: PDF, DOC, JPG, PNG</p>
                <input type="file" name="archivo" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
            </div>
            
            <button type="submit" class="btn-login" style="width:100%;">Enviar a Revisión</button>
        </form>
    </div>
</body>
</html>