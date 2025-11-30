<?php
session_start();
include 'db.php';
if (!isset($_SESSION['uid'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['uid'];
$mis_libros = mysqli_query($conn, "SELECT * FROM recursos WHERE usuario_id = $uid ORDER BY fecha_subida DESC");
$total_vistas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(vistas) as total FROM recursos WHERE usuario_id = $uid"))['total'];
if(!$total_vistas) $total_vistas = 0;
?>
<!DOCTYPE html>
<html>
<head><title>Mi Perfil</title><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <div class="nav-links"><a href="index.php">Inicio</a><a href="logout.php">Salir</a></div>
    </nav>

    <div class="container">
        <div class="detalle-header" style="text-align:center;">
            <h1>Hola, <?php echo $_SESSION['nombre']; ?></h1>
            <p>Rol: <strong><?php echo ucfirst($_SESSION['rol']); ?></strong></p>
            
            <?php if($_SESSION['rol'] == 'autor'): ?>
                <div style="display:flex; justify-content:center; gap:20px; margin-top:20px;">
                    <div class="stat-box">
                        <h2 style="color:var(--primary); margin:0;"><?php echo mysqli_num_rows($mis_libros); ?></h2>
                        <small>Publicaciones</small>
                    </div>
                    <div class="stat-box">
                        <h2 style="color:var(--success); margin:0;"><?php echo $total_vistas; ?></h2>
                        <small>Lecturas Totales</small>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if($_SESSION['rol'] == 'autor'): ?>
        <h3>Mis Obras</h3>
        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($mis_libros)): ?>
            <div class="card">
                <div class="card-body">
                    <h3><?php echo $row['titulo']; ?></h3>
                    <p>👁️ <?php echo $row['vistas']; ?> vistas</p>
                    <a href="detalle.php?id=<?php echo $row['id']; ?>" style="color:var(--primary);">Ver publicación</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>