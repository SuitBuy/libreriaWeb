<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM recursos WHERE id = $id";
    $res = mysqli_query($conn, $sql);
    
    if ($row = mysqli_fetch_assoc($res)) {
        $ext = strtolower($row['tipo_archivo']);
        
        // Definir qué tipo de archivo es para el navegador
        $mime = 'application/octet-stream'; // Por defecto
        if ($ext == 'pdf') $mime = 'application/pdf';
        if ($ext == 'jpg' || $ext == 'jpeg') $mime = 'image/jpeg';
        if ($ext == 'png') $mime = 'image/png';
        if ($ext == 'gif') $mime = 'image/gif';
        if ($ext == 'doc') $mime = 'application/msword';
        if ($ext == 'docx') $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        header("Content-Type: $mime");
        echo $row['datos'];
    }
}
?>