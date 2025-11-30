<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['uid'])) {
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $autor = mysqli_real_escape_string($conn, $_POST['autor']);
    $cat = mysqli_real_escape_string($conn, $_POST['categoria']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $uid = $_SESSION['uid'];

    // Asegurar nombre único
    $nombreArchivo = time() . "_" . basename($_FILES["archivo"]["name"]);
    $ruta = "uploads/" . $nombreArchivo;

    if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $ruta)) {
        $sql = "INSERT INTO recursos (titulo, autor_nombre, categoria, descripcion, archivo_pdf, usuario_id) 
                VALUES ('$titulo', '$autor', '$cat', '$desc', '$ruta', $uid)";
        mysqli_query($conn, $sql);
        header("Location: index.php");
    } else {
        echo "<script>alert('Error al subir el archivo.'); window.history.back();</script>";
    }
} else {
    header("Location: login.php");
}
?>