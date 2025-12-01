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
<!DOCTYPE html>
<html>
<head>
    <title>Panel Admin - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        .admin-header { background: #1e293b; padding: 20px; color: white; border-radius: 15px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .divider { border-top: 2px dashed #cbd5e1; margin: 50px 0; }
        .action-btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.9rem; display: inline-block; }
        .btn-edit { background: #3b82f6; color: white; }
        .btn-delete { background: #ef4444; color: white; }
        .uploader-info { font-size: 0.85rem; color: #64748b; background: #f1f5f9; padding: 5px 10px; border-radius: 6px; display: inline-block; margin-top: 5px; }
    </style>
</head>
<body>
    <nav class="navbar" style="border-bottom:3px solid #eab308;">
        <div class="logo"><div class="logo-icon"></div>Panel de Control</div>
        <div class="nav-links"><a href="index.php">Ver Web</a><a href="logout.php" style="color:#ef4444;">Cerrar Sesión</a></div>
    </nav>

    <div class="container" style="margin-top:40px;">
        <div class="admin-header" style="background:var(--primary-grad);">
            <h2 style="margin:0;"><i class="fa-solid fa-clock"></i> Pendientes de Revisión (<?php echo mysqli_num_rows($pendientes); ?>)</h2>
        </div>

        <?php if(mysqli_num_rows($pendientes) == 0): ?>
            <div style="text-align:center; padding:30px; color:#64748b; background:white; border-radius:20px; border:1px solid #e2e8f0;">
                <i class="fa-solid fa-check-circle" style="font-size:2rem; margin-bottom:10px; color:#10b981;"></i>
                <p>¡Todo limpio! No hay nada pendiente.</p>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php while($row = mysqli_fetch_assoc($pendientes)): ?>
                <div class="book-card" style="border:2px solid #f59e0b;">
                    <div class="book-body">
                        <span class="status-badge st-pendiente">Pendiente</span>
                        <h3 style="margin-top:10px; margin-bottom: 5px;"><?php echo $row['titulo']; ?></h3>
                        <p style="font-size:0.9rem; margin:0;">Autor Obra: <strong><?php echo $row['autor_nombre']; ?></strong></p>
                        <div class="uploader-info"><i class="fa-solid fa-user-upload"></i> Subido por: <strong><?php echo $row['nombre_uploader']; ?></strong></div>
                        <div style="margin-top:15px; display:flex; gap:5px;">
                            <a href="ver.php?id=<?php echo $row['id']; ?>" target="_blank" class="action-btn" style="background:#64748b; color:white; flex:1; text-align:center;">Ver</a>
                            <a href="admin_panel.php?action=approve&id=<?php echo $row['id']; ?>" class="action-btn" style="background:#10b981; color:white; flex:1; text-align:center;"><i class="fa-solid fa-check"></i></a>
                            <a href="admin_panel.php?action=reject&id=<?php echo $row['id']; ?>" class="action-btn" style="background:#ef4444; color:white; flex:1; text-align:center;"><i class="fa-solid fa-xmark"></i></a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <div class="divider"></div>

        <div class="admin-header">
            <h2 style="margin:0;"><i class="fa-solid fa-book"></i> Biblioteca Activa</h2>
        </div>

        <form method="GET" style="margin-bottom:30px; display:flex; gap:10px;">
            <input type="text" name="search" class="input-field" placeholder="Buscar..." value="<?php echo $search_term; ?>" style="margin:0;">
            <button type="submit" class="btn-login" style="border-radius:12px; padding:0 30px;"><i class="fa-solid fa-search"></i></button>
            <?php if($search_term): ?>
                <a href="admin_panel.php" class="btn-outline" style="width:auto; padding:0 20px; margin:0; display:flex; align-items:center;">Limpiar</a>
            <?php endif; ?>
        </form>

        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($aprobados)): ?>
            <div class="book-card">
                <div style="padding:15px; border-bottom:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center;">
                    <span class="tag"><?php echo $row['categoria']; ?></span>
                    <small style="color:#94a3b8;">ID: <?php echo $row['id']; ?></small>
                </div>
                <div class="book-body">
                    <h3 style="font-size:1.1rem; margin-bottom:5px;"><?php echo $row['titulo']; ?></h3>
                    <p style="color:#64748b; font-size:0.9rem; margin-bottom: 5px;">Autor: <?php echo $row['autor_nombre']; ?></p>
                    <div class="uploader-info"><i class="fa-solid fa-user"></i> Subido por: <?php echo $row['nombre_uploader']; ?></div>
                    <div style="margin-top:20px; display:flex; gap:10px;">
                        <a href="editar_recurso.php?id=<?php echo $row['id']; ?>" class="action-btn btn-edit" style="flex:1; text-align:center;"><i class="fa-solid fa-pen"></i> Editar</a>
                        <a href="admin_panel.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('¿Eliminar permanentemente?');" class="action-btn btn-delete" style="flex:1; text-align:center;"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>