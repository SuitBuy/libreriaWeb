<?php
include 'db.php';

// Aumentar límite de memoria para archivos grandes
ini_set('memory_limit', '256M');

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. CACHÉ DEL NAVEGADOR (El secreto de la velocidad)
    // Si el navegador ya tiene la imagen, le decimos "No ha cambiado" (304) y no descargamos nada.
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        header('HTTP/1.1 304 Not Modified');
        exit;
    }

    // 2. CONSULTA EFICIENTE
    // Solo traemos el BLOB de un solo archivo específico
    $sql = "SELECT tipo_archivo, datos FROM recursos WHERE id = $id";
    $res = mysqli_query($conn, $sql);
    
    if ($row = mysqli_fetch_assoc($res)) {
        $ext = strtolower($row['tipo_archivo']);
        
        // Definir tipo MIME
        $mime = 'application/octet-stream';
        if ($ext == 'pdf') $mime = 'application/pdf';
        if ($ext == 'jpg' || $ext == 'jpeg') $mime = 'image/jpeg';
        if ($ext == 'png') $mime = 'image/png';
        if ($ext == 'gif') $mime = 'image/gif';
        if ($ext == 'doc') $mime = 'application/msword';
        if ($ext == 'docx') $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        // 3. ENVIAR CABECERAS DE CACHÉ
        header("Content-Type: $mime");
        header("Cache-Control: public, max-age=31536000"); // 1 año de caché
        header("Pragma: cache");
        header("Expires: " . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        header("Last-Modified: " . gmdate('D, d M Y H:i:s', time()) . ' GMT');

        // 4. IMPRIMIR EL ARCHIVO BINARIO
        echo $row['datos'];
    }
}
?>