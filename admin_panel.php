<?php
session_start();
include 'db.php';

if (!isset($_SESSION['uid']) || $_SESSION['rol'] != 'admin') { header("Location: index.php"); exit; }

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        mysqli_query($conn, "UPDATE recursos SET estado = 'aprobado' WHERE id = $id");
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE recursos SET estado = 'rechazado' WHERE id = $id");
    } elseif ($action == 'delete') {
        // OBTENER LA RUTA PARA BORRAR EL ARCHIVO FÍSICO
        $q = mysqli_query($conn, "SELECT archivo_pdf FROM recursos WHERE id = $id");
        $file = mysqli_fetch_assoc($q);
        
        // BORRAR DEL DISCO
        if ($file && file_exists($file['archivo_pdf'])) {
            unlink($file['archivo_pdf']);
        }

        // BORRAR DE LA BD
        mysqli_query($conn, "DELETE FROM recursos WHERE id = $id");
        mysqli_query($conn, "DELETE FROM comentarios WHERE recurso_id = $id");
    }
    header("Location: admin_panel.php"); exit;
}

// Consultas igual que antes, sin cambiar nada visual
$pendientes = mysqli_query($conn, "SELECT r.*, u.nombre AS nombre_uploader FROM recursos r JOIN usuarios u ON r.usuario_id = u.id WHERE r.estado = 'pendiente' ORDER BY r.id DESC");
$aprobados = mysqli_query($conn, "SELECT r.*, u.nombre AS nombre_uploader FROM recursos r JOIN usuarios u ON r.usuario_id = u.id WHERE r.estado = 'aprobado' ORDER BY r.id DESC");
?>