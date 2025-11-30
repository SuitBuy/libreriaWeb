<?php
session_start();
include 'db.php';
if (!isset($_SESSION['uid'])) { header("Location: login.php"); exit; }
$uid = $_SESSION['uid'];
$mis_libros = mysqli_query($conn, "SELECT * FROM recursos WHERE usuario_id = $uid ORDER BY fecha_subida DESC");
$total_vistas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(vistas) as total FROM recursos WHERE usuario_id = $uid"))['total'];
?>
<!DOCTYPE html>
<html>
<head><title>Mi Perfil</title><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>BiblioShare</div>
        <div class="nav-links"><a href="index.php">Inicio</a><a href="logout.php">Salir</a></div>
    </nav>
    <div class="container" style="margin-top:40px;">
        <div style="background:var(--primary-grad); color:white; padding:40px; border-radius:30px; text-align:center;">
            <h1>Hola, <?php echo $_SESSION['nombre']; ?></h1>
            <p style="opacity:0.9;">Rol: <?php echo strtoupper($_SESSION['rol']); ?></p>
            
            <?php if($_SESSION['rol'] == 'autor'): ?>
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo mysqli_num_rows($mis_libros); ?></div>
                        <div style="color:var(--gray);">Publicaciones</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_vistas ? $total_vistas : 0; ?></div>
                        <div style="color:var(--gray);">Lecturas Totales</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if($_SESSION['rol'] == 'autor'): ?>
            <h2 class="section-title">Mis Obras Gestionadas</h2>
            <div class="grid">
                <?php while($row = mysqli_fetch_assoc($mis_libros)): ?>
                    <div class="card">
                        <div class="card-body">
                            <span class="tag"><?php echo $row['categoria']; ?></span>
                            <h3><?php echo $row['titulo']; ?></h3>
                            <p><?php echo $row['vistas']; ?> lecturas</p>
                            <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-card">Ver Página</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>