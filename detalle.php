<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$uid = $_SESSION['uid'];

// Historial de visitas
$check = mysqli_query($conn, "SELECT id FROM historial_vistas WHERE usuario_id = $uid AND recurso_id = $id");
if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn, "UPDATE recursos SET vistas = vistas + 1 WHERE id = $id");
    mysqli_query($conn, "INSERT INTO historial_vistas (usuario_id, recurso_id) VALUES ($uid, $id)");
}

$query = "SELECT r.*, u.nombre AS subido_por FROM recursos r JOIN usuarios u ON r.usuario_id = u.id WHERE r.id=$id";
$row = mysqli_fetch_assoc(mysqli_query($conn, $query));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $com = mysqli_real_escape_string($conn, $_POST['comentario']);
    $val = (int)$_POST['valoracion'];
    mysqli_query($conn, "INSERT INTO comentarios (recurso_id, usuario_id, comentario, valoracion) VALUES ($id, $uid, '$com', $val)");
}
$comentarios = mysqli_query($conn, "SELECT c.*, u.nombre FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE recurso_id=$id ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $row['titulo']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        iframe {
            display: block;
            background: #fff;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div>Urban Canvas
        </div>
        <a href="index.php">← Volver</a>
    </nav>

    <div class="container" style="margin-top:20px; max-width: 95%;">
        <div style="background:white; border-radius:30px; padding:30px; box-shadow:var(--card-shadow); display:flex; gap:30px; flex-wrap:wrap;">

            <div style="flex:3; border-radius:20px; overflow:hidden; border:1px solid #e2e8f0; min-height:850px; background:white;">
                <?php
                $ruta = $row['archivo_pdf']; // Ruta directa del volumen
                $ext = strtolower($row['tipo_archivo']);

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    echo "<div style='display:flex; align-items:center; justify-content:center; height:100%; background:#f8fafc;'>
                                <img src='$ruta' style='max-width:100%; max-height:850px; object-fit:contain;'>
                              </div>";
                } elseif ($ext == 'pdf') {
                    echo "<iframe src='$ruta#toolbar=0&navpanes=0&scrollbar=0&view=FitH' width='100%' height='100%' style='border:none; min-height:850px;'></iframe>";
                } else {
                    echo "<div style='display:flex; align-items:center; justify-content:center; height:850px; background:#f8fafc; color:#2563eb; flex-direction:column;'>
                                <i class='fa-solid fa-file-lines' style='font-size:8rem;'></i>
                                <p>Vista previa no disponible. Descarga el archivo.</p>
                              </div>";
                }
                ?>
            </div>

            <div style="flex:1; min-width:300px;">
                <span class="tag" style="margin-bottom:15px; display:inline-block;"><?php echo $row['categoria']; ?></span>
                <h1 style="font-size:2.5rem; margin:10px 0;"><?php echo $row['titulo']; ?></h1>

                <div style="margin-bottom:25px;">
                    <p>Autor Obra: <strong><?php echo $row['autor_nombre']; ?></strong></p>
                    <p style="color:#0ea5e9;">Subido por: <strong><?php echo $row['subido_por']; ?></strong></p>
                </div>

                <div style="background:#f1f5f9; padding:20px; border-radius:20px; margin-bottom:20px;">
                    <?php echo nl2br($row['descripcion']); ?>
                </div>

                <a href="<?php echo $ruta; ?>" download target="_blank" class="btn-login" style="display:block; text-align:center; padding:15px;">
                    <i class="fa-solid fa-download"></i> Descargar
                </a>
            </div>
        </div>
    </div>
</body>

</html>