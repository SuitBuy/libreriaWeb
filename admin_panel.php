<?php
session_start();
include 'db.php';

// Seguridad: Solo admin puede entrar
if (!isset($_SESSION['uid']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Lógica de Aprobar/Rechazar
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    if ($action == 'approve') {
        mysqli_query($conn, "UPDATE recursos SET estado = 'aprobado' WHERE id = $id");
    } elseif ($action == 'reject') {
        mysqli_query($conn, "UPDATE recursos SET estado = 'rechazado' WHERE id = $id");
    }
    header("Location: admin_panel.php");
    exit;
}

// Obtener pendientes
$pendientes = mysqli_query($conn, "SELECT * FROM recursos WHERE estado = 'pendiente' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de Control - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Control Panel</div>
        <div class="nav-links">
            <a href="index.php">Ver Web</a>
            <a href="logout.php" style="color:#ef4444;">Salir</a>
        </div>
    </nav>

    <div class="container" style="margin-top:40px;">
        <h2 class="section-title">Publicaciones Pendientes de Revisión</h2>
        
        <?php if(mysqli_num_rows($pendientes) == 0): ?>
            <div style="text-align:center; padding:50px; color:#64748b;">
                <i class="fa-solid fa-check-circle" style="font-size:3rem; margin-bottom:20px;"></i>
                <p>¡Todo al día! No hay publicaciones pendientes.</p>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php while($row = mysqli_fetch_assoc($pendientes)): ?>
                <div class="book-card">
                    <div class="book-body">
                        <span class="tag"><?php echo $row['categoria']; ?></span>
                        <h3 style="margin-top:10px;"><?php echo $row['titulo']; ?></h3>
                        <p style="font-size:0.9rem;">Por: <?php echo $row['autor_nombre']; ?></p>
                        <p style="font-size:0.8rem; color:#64748b;">Tipo: <?php echo strtoupper($row['tipo_archivo']); ?></p>
                        
                        <div style="margin-top:15px; display:flex; gap:10px;">
                            <a href="<?php echo $row['archivo_pdf']; ?>" target="_blank" class="btn-outline" style="flex:1;">Ver Archivo</a>
                        </div>
                        <div style="margin-top:15px; display:flex; gap:10px;">
                            <a href="admin_panel.php?action=approve&id=<?php echo $row['id']; ?>" class="btn-login" style="background:#10b981; flex:1; text-align:center;">Aprobar</a>
                            <a href="admin_panel.php?action=reject&id=<?php echo $row['id']; ?>" class="btn-login" style="background:#ef4444; flex:1; text-align:center;">Rechazar</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>