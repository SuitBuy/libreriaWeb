<?php
session_start();
include 'db.php';

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $autor = mysqli_real_escape_string($conn, $_POST['autor']);
    $cat = mysqli_real_escape_string($conn, $_POST['categoria']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $uid = $_SESSION['uid'];

    // --- GESTIÓN DE ARCHIVO EN DISCO ---
    $nombreOriginal = $_FILES["archivo"]["name"];
    $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

    // Generamos nombre único para evitar que se sobrescriban
    $nombreFinal = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "", $nombreOriginal);
    $rutaDestino = "uploads/" . $nombreFinal;

    // Crear carpeta si no existe (aunque el volumen debería encargarse)
    if (!file_exists("uploads")) {
        mkdir("uploads", 0777, true);
    }

    $permitidos = array("pdf", "doc", "docx", "jpg", "jpeg", "png");

    if (in_array($ext, $permitidos)) {
        if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaDestino)) {
            // Guardamos solo la RUTA (texto) en la BD, no el archivo pesado
            // Nota: No usamos la columna 'datos' aquí
            $sql = "INSERT INTO recursos (titulo, autor_nombre, categoria, descripcion, archivo_pdf, usuario_id, estado, tipo_archivo) 
                    VALUES ('$titulo', '$autor', '$cat', '$desc', '$rutaDestino', $uid, 'pendiente', '$ext')";

            if (mysqli_query($conn, $sql)) {
                header("Location: index.php?msg=uploaded");
                exit;
            } else {
                $error = "Error BD: " . mysqli_error($conn);
            }
        } else {
            $error = "Error al guardar el archivo en el volumen.";
        }
    } else {
        $error = "Formato no permitido.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Subir</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo">Urban Canvas</div><a href="index.php">Cancelar</a>
    </nav>
    <div class="auth-wrapper">
        <h2>Subir Archivo (Modo Volumen)</h2>
        <?php if (isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="titulo" class="input-field" placeholder="Título" required>
            <input type="text" name="autor" class="input-field" placeholder="Autor" required>
            <select name="categoria" class="input-field">
                <option>Ciencias</option>
                <option>Arte</option>
                <option>Historia</option>
                <option>Ingeniería</option>
                <option>Otros</option>
            </select>
            <textarea name="desc" class="input-field" placeholder="Descripción..."></textarea>
            <div style="margin:20px 0; border:2px dashed #ccc; padding:20px; text-align:center;">
                <input type="file" name="archivo" required>
            </div>
            <button type="submit" class="btn-login" style="width:100%;">Subir</button>
        </form>
    </div>
</body>

</html>