<?php
session_start();
include 'db.php';

$id = $_GET['id'];
// CONTADOR DE VISTAS (Nuevo)
mysqli_query($conn, "UPDATE recursos SET vistas = vistas + 1 WHERE id = $id");
// ... resto del código ...
$id = $_GET['id'];
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM recursos WHERE id=$id"));

// Procesar comentario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $com = mysqli_real_escape_string($conn, $_POST['comentario']);
    $val = $_POST['valoracion'];
    $uid = $_SESSION['uid'];
    mysqli_query($conn, "INSERT INTO comentarios (recurso_id, usuario_id, comentario, valoracion) VALUES ($id, $uid, '$com', $val)");
}

$comentarios = mysqli_query($conn, "SELECT c.*, u.nombre FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE recurso_id=$id ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <a href="index.php" style="color:white;">Volver</a>
    </nav>

    <div class="container">
        <div class="detalle-header">
            <h1><?php echo $row['titulo']; ?></h1>
            <p><strong>Autor:</strong> <?php echo $row['autor_nombre']; ?> | <strong>Categoría:</strong> <?php echo $row['categoria']; ?></p>
            <p><?php echo $row['descripcion']; ?></p>
            <a href="<?php echo $row['archivo_pdf']; ?>" target="_blank" class="btn-action" style="text-decoration:none; background:#27ae60;">Descargar / Leer PDF</a>
        </div>

        <h3>Comentarios y Valoraciones</h3>
        
        <?php if(isset($_SESSION['uid'])): ?>
        <form method="POST" style="background:white; padding:20px; border-radius:8px;">
            <select name="valoracion">
                <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                <option value="4">⭐⭐⭐⭐ Bueno</option>
                <option value="3">⭐⭐⭐ Regular</option>
            </select>
            <textarea name="comentario" placeholder="Escribe tu opinión..." required></textarea>
            <button type="submit">Enviar Reseña</button>
        </form>
        <?php else: ?>
            <p class="alert error">Inicia sesión para comentar.</p>
        <?php endif; ?>

        <div style="margin-top:20px; background:white; padding:20px; border-radius:8px;">
            <?php while($c = mysqli_fetch_assoc($comentarios)): ?>
                <div class="comentario-item">
                    <strong><?php echo $c['nombre']; ?></strong> 
                    <span class="stars"><?php echo str_repeat("★", $c['valoracion']); ?></span>
                    <p><?php echo $c['comentario']; ?></p>
                    <small style="color:#999;"><?php echo $c['fecha']; ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>