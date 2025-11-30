<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. Contador de Vistas (Requisito: Estadísticas)
mysqli_query($conn, "UPDATE recursos SET vistas = vistas + 1 WHERE id = $id");

// 2. Obtener datos
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM recursos WHERE id=$id"));

// 3. Procesar comentario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['uid'])) {
    $com = mysqli_real_escape_string($conn, $_POST['comentario']);
    $val = (int)$_POST['valoracion'];
    $uid = $_SESSION['uid'];
    mysqli_query($conn, "INSERT INTO comentarios (recurso_id, usuario_id, comentario, valoracion) VALUES ($id, $uid, '$com', $val)");
}

$comentarios = mysqli_query($conn, "SELECT c.*, u.nombre FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE recurso_id=$id ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html>
<head><title><?php echo $row['titulo']; ?></title><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <a href="index.php" style="color:white;">Volver</a>
    </nav>

    <div class="container">
        <div class="detalle-header">
            <span class="tag"><?php echo $row['categoria']; ?></span>
            <h1><?php echo $row['titulo']; ?></h1>
            <p><strong>Autor:</strong> <?php echo $row['autor_nombre']; ?> | 👁️ <?php echo $row['vistas']; ?> lecturas</p>
            <p><?php echo $row['descripcion']; ?></p>
            <a href="<?php echo $row['archivo_pdf']; ?>" target="_blank" class="btn-action" style="background:var(--success); text-decoration:none; display:inline-block; margin-top:10px;">⬇ Descargar / Leer PDF</a>
        </div>

        <h3>Reseñas de la Comunidad</h3>
        <?php if(isset($_SESSION['uid'])): ?>
        <form method="POST" style="background:white; padding:20px; border-radius:8px; margin-bottom:20px;">
            <select name="valoracion" style="width:150px;">
                <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                <option value="4">⭐⭐⭐⭐ Bueno</option>
                <option value="3">⭐⭐⭐ Regular</option>
            </select>
            <textarea name="comentario" placeholder="Escribe tu opinión..." required rows="2"></textarea>
            <button type="submit">Publicar Reseña</button>
        </form>
        <?php else: ?>
            <div class="alert error">Debes <a href="login.php">iniciar sesión</a> para comentar.</div>
        <?php endif; ?>

        <div style="background:white; padding:20px; border-radius:8px;">
            <?php while($c = mysqli_fetch_assoc($comentarios)): ?>
                <div class="comentario-item">
                    <strong><?php echo $c['nombre']; ?></strong> 
                    <span class="stars"><?php echo str_repeat("★", $c['valoracion']); ?></span>
                    <p><?php echo $c['comentario']; ?></p>
                    <small style="color:#999;"><?php echo date('d/m/Y', strtotime($c['fecha'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>