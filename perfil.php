<?php
session_start();
include 'db.php';

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

$uid = $_SESSION['uid'];
$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];

// Obtener estadísticas de mis libros
$mis_libros = mysqli_query($conn, "SELECT * FROM recursos WHERE usuario_id = $uid ORDER BY fecha_subida DESC");
$total_vistas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(vistas) as total FROM recursos WHERE usuario_id = $uid"))['total'];
if(!$total_vistas) $total_vistas = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - BiblioShare</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="logout.php" style="color:#ff6b6b;">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <div class="detalle-header" style="text-align:center;">
            <h1>Hola, <?php echo $nombre; ?></h1>
            <p>Rol: <strong><?php echo ucfirst($rol); ?></strong></p>
            
            <?php if($rol == 'autor'): ?>
                <div style="display:flex; justify-content:center; gap:30px; margin-top:20px;">
                    <div class="stat-box">
                        <span style="font-size:2rem; font-weight:bold; color:var(--primary);"><?php echo mysqli_num_rows($mis_libros); ?></span>
                        <p>Obras Publicadas</p>
                    </div>
                    <div class="stat-box">
                        <span style="font-size:2rem; font-weight:bold; color:var(--success);"><?php echo $total_vistas; ?></span>
                        <p>Lecturas Totales</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if($rol == 'autor'): ?>
        <h3>Mis Obras Publicadas</h3>
        <p style="color:#666; margin-bottom:20px;">Gestiona tu contenido y revisa el impacto de tus publicaciones.</p>
        
        <table style="width:100%; border-collapse:collapse; background:white; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
            <tr style="background:#eee; text-align:left;">
                <th style="padding:15px;">Título</th>
                <th style="padding:15px;">Categoría</th>
                <th style="padding:15px;">Vistas</th>
                <th style="padding:15px;">Fecha</th>
                <th style="padding:15px;">Acción</th>
            </tr>
            <?php while($row = mysqli_fetch_assoc($mis_libros)): ?>
            <tr style="border-bottom:1px solid #ddd;">
                <td style="padding:15px;"><?php echo $row['titulo']; ?></td>
                <td style="padding:15px;"><span class="tag"><?php echo $row['categoria']; ?></span></td>
                <td style="padding:15px;">👁️ <?php echo $row['vistas']; ?></td>
                <td style="padding:15px;"><?php echo date('d/m/Y', strtotime($row['fecha_subida'])); ?></td>
                <td style="padding:15px;">
                    <a href="detalle.php?id=<?php echo $row['id']; ?>" style="color:var(--primary);">Ver</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php else: ?>
            <div class="alert success">
                <p>Como estudiante, puedes navegar, descargar y comentar libremente. ¿Quieres publicar? <a href="#">Solicita ser Autor</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>