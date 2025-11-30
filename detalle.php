<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
if (!isset($_SESSION['uid'])) { header("Location: login.php"); exit; }

$id = mysqli_real_escape_string($conn, $_GET['id']);
mysqli_query($conn, "UPDATE recursos SET vistas = vistas + 1 WHERE id = $id");
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM recursos WHERE id=$id"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $com = mysqli_real_escape_string($conn, $_POST['comentario']);
    $val = (int)$_POST['valoracion'];
    $uid = $_SESSION['uid'];
    mysqli_query($conn, "INSERT INTO comentarios (recurso_id, usuario_id, comentario, valoracion) VALUES ($id, $uid, '$com', $val)");
}
$comentarios = mysqli_query($conn, "SELECT c.*, u.nombre FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE recurso_id=$id ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $row['titulo']; ?> - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php">← Volver</a>
    </nav>

    <div class="container" style="margin-top:40px;">
        
        <div style="background:white; border-radius:30px; padding:40px; box-shadow:var(--card-shadow); display:flex; gap:40px; flex-wrap:wrap;">
            
            <div style="flex:2; background:#f8fafc; border-radius:20px; display:flex; align-items:center; justify-content:center; min-height:1200px; padding:20px; border:1px solid #e2e8f0;">
                <?php
                    $archivo = $row['archivo_pdf'];
                    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        // Imagen grande
                        echo "<img src='$archivo' style='width:100%; height:auto; max-height:750px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.1); object-fit:contain;'>";
                    } 
                    elseif ($ext == 'pdf') {
                        // Visor PDF grande
                        echo "<embed src='$archivo' type='application/pdf' width='100%' height='750px' style='border-radius:10px; border:none;'>";
                    } 
                    elseif (in_array($ext, ['doc', 'docx'])) {
                        echo "<div style='text-align:center; color:#2563eb;'>
                                <i class='fa-solid fa-file-word' style='font-size:8rem; margin-bottom:20px;'></i>
                                <h3 style='color:#1e293b;'>Archivo de Word</h3>
                                <p style='color:#64748b;'>Vista previa no disponible en navegador.</p>
                              </div>";
                    } 
                    else {
                        echo "<i class='fa-regular fa-file' style='font-size:8rem; color:#cbd5e1;'></i>";
                    }
                ?>
            </div>

            <div style="flex:1; min-width:300px;">
                <span class="tag" style="margin-bottom:15px; display:inline-block; font-size:1rem;"><?php echo $row['categoria']; ?></span>
                <h1 style="font-size:2.8rem; margin:10px 0; line-height:1.2;"><?php echo $row['titulo']; ?></h1>
                
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:25px; color:#64748b; font-size:1.1rem;">
                    <i class="fa-solid fa-user-circle"></i>
                    <span>Autor: <strong><?php echo $row['autor_nombre']; ?></strong></span>
                </div>

                <div style="background:#f1f5f9; padding:25px; border-radius:20px; margin-bottom:30px; line-height:1.8; color:#334155;">
                    <h4 style="margin-top:0; color:#1e293b;">Descripción:</h4>
                    <?php echo nl2br($row['descripcion']); ?>
                </div>
                
                <a href="<?php echo $row['archivo_pdf']; ?>" download target="_blank" class="btn-login" style="padding:20px 30px; font-size:1.2rem; display:block; text-align:center; margin-bottom:20px;">
                    <i class="fa-solid fa-download"></i> Descargar Archivo
                </a>
                
                <div style="text-align:center; color:#64748b;">
                    <i class="fa-solid fa-eye"></i> <?php echo $row['vistas']; ?> personas han visto esto
                </div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top:60px;">Comentarios de la Comunidad</h3>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:30px;">
            
            <div style="background:white; padding:30px; border-radius:25px; box-shadow:var(--card-shadow); height:fit-content;">
                <h4 style="margin-top:0;">Dejar una Reseña</h4>
                <form method="POST">
                    <select name="valoracion" class="input-field" style="margin-bottom:15px;">
                        <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                        <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                        <option value="3">⭐⭐⭐ Regular</option>
                        <option value="2">⭐⭐ Malo</option>
                        <option value="1">⭐ Pésimo</option>
                    </select>
                    <textarea name="comentario" class="input-field" placeholder="¿Qué te pareció este recurso?" rows="4" required></textarea>
                    <button type="submit" class="btn-login" style="width:100%; margin-top:10px;">Publicar Comentario</button>
                </form>
            </div>

            <div style="max-height:500px; overflow-y:auto; padding-right:10px;">
                <?php if(mysqli_num_rows($comentarios) == 0): ?>
                    <div style="text-align:center; color:#94a3b8; padding:20px;">
                        <i class="fa-regular fa-comment-dots" style="font-size:3rem; margin-bottom:10px;"></i>
                        <p>Aún no hay comentarios. ¡Sé el primero!</p>
                    </div>
                <?php else: ?>
                    <?php while($c = mysqli_fetch_assoc($comentarios)): ?>
                        <div style="background:white; padding:20px; border-radius:15px; margin-bottom:15px; border:1px solid #f1f5f9;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                <strong style="color:#0f172a;"><?php echo $c['nombre']; ?></strong>
                                <span style="color:#eab308; font-size:0.9rem;">
                                    <?php for($i=0; $i<$c['valoracion']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                </span>
                            </div>
                            <p style="margin:5px 0; color:#475569; font-size:0.95rem;"><?php echo $c['comentario']; ?></p>
                            <small style="color:#cbd5e1; font-size:0.75rem;"><?php echo $c['fecha']; ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>