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
        <a href="index.php" style="text-decoration:none; color:#64748b;">← Volver</a>
    </nav>
    <div class="container" style="margin-top:40px;">
        <div style="background:white; border-radius:30px; padding:40px; box-shadow:var(--card-shadow); display:flex; gap:40px; flex-wrap:wrap;">
            <div style="flex:1; background:#f8fafc; border-radius:20px; display:flex; align-items:center; justify-content:center; font-size:4rem; min-height:300px;">
                📄
            </div>
            <div style="flex:2;">
                <span class="tag" style="font-size:1rem;"><?php echo $row['categoria']; ?></span>
                <h1 style="font-size:2.5rem; margin:10px 0;"><?php echo $row['titulo']; ?></h1>
                <p style="color:#64748b; font-size:1.1rem;">Autor: <strong><?php echo $row['autor_nombre']; ?></strong></p>
                
                <div style="margin:25px 0; line-height:1.8; color:#334155;">
                    <?php echo nl2br($row['descripcion']); ?>
                </div>
                
                <div style="display:flex; gap:15px;">
                    <a href="<?php echo $row['archivo_pdf']; ?>" target="_blank" class="btn-login" style="text-decoration:none;">Leer PDF</a>
                    <span style="display:flex; align-items:center; color:#64748b;">👁️ <?php echo $row['vistas']; ?> lecturas</span>
                </div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top:50px;">Comentarios</h3>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:30px;">
            <div>
                <?php if(isset($_SESSION['uid'])): ?>
                    <form method="POST" style="background:white; padding:25px; border-radius:20px; box-shadow:var(--card-shadow);">
                        <h4 style="margin-top:0;">Tu opinión cuenta</h4>
                        <select name="valoracion" class="input-field" style="margin-bottom:10px;">
                            <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                            <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                            <option value="3">⭐⭐⭐ Regular</option>
                        </select>
                        <textarea name="comentario" class="input-field" placeholder="Escribe aquí..." rows="3"></textarea>
                        <button type="submit" class="btn-login" style="width:100%; border:none; margin-top:10px; cursor:pointer;">Publicar</button>
                    </form>
                <?php endif; ?>
            </div>
            <div>
                <?php while($c = mysqli_fetch_assoc($comentarios)): ?>
                    <div style="background:white; padding:20px; border-radius:15px; margin-bottom:15px; border:1px solid #f1f5f9;">
                        <strong><?php echo $c['nombre']; ?></strong>
                        <span style="color:#eab308; margin-left:10px;"><?php echo str_repeat("★", $c['valoracion']); ?></span>
                        <p style="margin:5px 0; color:#475569;"><?php echo $c['comentario']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>
</html>