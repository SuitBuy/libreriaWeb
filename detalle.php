<?php
session_start();
include 'db.php';
if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = mysqli_real_escape_string($conn, $_GET['id']);
mysqli_query($conn, "UPDATE recursos SET vistas = vistas + 1 WHERE id = $id");
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM recursos WHERE id=$id"));

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
        <div class="logo"><div class="logo-icon"></div>BiblioShare</div>
        <a href="index.php" style="text-decoration:none; color:var(--dark);">← Volver</a>
    </nav>
    <div class="container" style="margin-top:40px;">
        <div class="card" style="flex-direction:row; flex-wrap:wrap; padding:0;">
            <div style="flex:1; background:var(--light); padding:40px; min-width:300px; display:flex; align-items:center; justify-content:center; font-size:5rem;">
                📄
            </div>
            <div style="flex:2; padding:40px;">
                <span class="tag" style="background:var(--accent-grad); color:white;"><?php echo $row['categoria']; ?></span>
                <h1 style="margin-top:10px; font-size:2.5rem;"><?php echo $row['titulo']; ?></h1>
                <p style="color:var(--gray);">Autor: <strong><?php echo $row['autor_nombre']; ?></strong></p>
                <div style="margin:20px 0; line-height:1.8;">
                    <?php echo nl2br($row['descripcion']); ?>
                </div>
                <a href="<?php echo $row['archivo_pdf']; ?>" target="_blank" class="btn-primary" style="text-decoration:none; display:inline-block; width:auto;">Descargar PDF Completo</a>
            </div>
        </div>

        <h3 class="section-title">Reseñas de la Comunidad</h3>
        <div class="grid">
            <div class="card" style="box-shadow:none; border:none; background:transparent;">
                <?php if(isset($_SESSION['uid'])): ?>
                    <form method="POST" class="auth-container" style="margin:0; width:100%; max-width:100%;">
                        <h4>Deja tu opinión</h4>
                        <select name="valoracion" class="form-input">
                            <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                            <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                            <option value="3">⭐⭐⭐ Regular</option>
                        </select>
                        <textarea name="comentario" class="form-input" placeholder="¿Qué te pareció?" rows="3"></textarea>
                        <button type="submit" class="btn-primary">Publicar</button>
                    </form>
                <?php endif; ?>
            </div>
            <div style="grid-column: span 2;">
                <?php while($c = mysqli_fetch_assoc($comentarios)): ?>
                    <div style="background:white; padding:20px; border-radius:15px; margin-bottom:15px; border:1px solid #f1f5f9;">
                        <div style="font-weight:bold; color:var(--dark);"><?php echo $c['nombre']; ?></div>
                        <div style="color:#f59e0b;"><?php echo str_repeat("★", $c['valoracion']); ?></div>
                        <p style="color:var(--gray); margin-top:5px;"><?php echo $c['comentario']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>