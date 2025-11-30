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
    <style>
        iframe { display: block; background: #fff; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php">← Volver</a>
    </nav>

    <div class="container" style="margin-top:20px; max-width: 95%;">
        
        <div style="background:white; border-radius:30px; padding:30px; box-shadow:var(--card-shadow); display:flex; gap:30px; flex-wrap:wrap;">
            
            <div style="flex:3; border-radius:20px; overflow:hidden; border:1px solid #e2e8f0; min-height:850px; background:white;">
                <?php
                    $archivo = $row['archivo_pdf'];
                    $ext = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        echo "<div style='display:flex; align-items:center; justify-content:center; height:100%; background:#f8fafc;'>
                                <img src='$archivo' style='max-width:100%; max-height:850px; object-fit:contain;'>
                              </div>";
                    } 
                    elseif ($ext == 'pdf') {
                        // PDF en modo limpio
                        $pdf_limpio = $archivo . "#toolbar=0&navpanes=0&scrollbar=0&view=FitH";
                        echo "<iframe src='$pdf_limpio' width='100%' height='100%' style='border:none; min-height:850px;'></iframe>";
                    } 
                    elseif (in_array($ext, ['doc', 'docx'])) {
                        echo "<div style='display:flex; flex-direction:column; align-items:center; justify-content:center; height:850px; background:#f8fafc; color:#2563eb;'>
                                <i class='fa-solid fa-file-word' style='font-size:8rem; margin-bottom:20px;'></i>
                                <h3 style='color:#1e293b;'>Vista previa Word no disponible</h3>
                                <p style='color:#64748b;'>Usa el botón de descarga.</p>
                              </div>";
                    } 
                    else {
                        echo "<div style='display:flex; align-items:center; justify-content:center; height:850px; background:#f8fafc;'>
                                <i class='fa-regular fa-file' style='font-size:8rem; color:#cbd5e1;'></i>
                              </div>";
                    }
                ?>
            </div>

            <div style="flex:1; min-width:300px; display:flex; flex-direction:column;">
                
                <div style="margin-bottom:auto;">
                    <span class="tag" style="margin-bottom:15px; display:inline-block; font-size:1rem;"><?php echo $row['categoria']; ?></span>
                    <h1 style="font-size:2.5rem; margin:10px 0; line-height:1.2;"><?php echo $row['titulo']; ?></h1>
                    
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
                        <i class="fa-solid fa-eye"></i> <?php echo $row['vistas']; ?> visualizaciones
                    </div>
                </div>

                <h3 class="section-title" style="margin-top:40px; border-top:1px solid #e2e8f0; padding-top:20px;">Comentarios</h3>
                
                <div style="background:white; padding:20px; border-radius:20px; border:1px solid #e2e8f0; margin-bottom:20px;">
                    <form method="POST">
                        <select name="valoracion" class="input-field" style="margin-bottom:10px; padding:10px;">
                            <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                            <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                            <option value="3">⭐⭐⭐ Regular</option>
                            <option value="2">⭐⭐ Malo</option>
                            <option value="1">⭐ Pésimo</option>
                        </select>
                        <textarea name="comentario" class="input-field" placeholder="Escribe tu opinión..." rows="3" required></textarea>
                        <button type="submit" class="btn-login" style="width:100%; margin-top:10px; font-size:0.9rem;">Publicar</button>
                    </form>
                </div>

                <div style="flex:1; overflow-y:auto; max-height:400px; padding-right:5px;">
                    <?php if(mysqli_num_rows($comentarios) == 0): ?>
                        <div style="text-align:center; color:#94a3b8; padding:20px;">
                            <i class="fa-regular fa-comment-dots" style="font-size:2rem; margin-bottom:10px;"></i>
                            <p>Sin comentarios aún.</p>
                        </div>
                    <?php else: ?>
                        <?php while($c = mysqli_fetch_assoc($comentarios)): ?>
                            <div style="background:#f8fafc; padding:15px; border-radius:15px; margin-bottom:10px; border:1px solid #f1f5f9;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                    <strong style="color:#0f172a; font-size:0.9rem;"><?php echo $c['nombre']; ?></strong>
                                    <span style="color:#eab308; font-size:0.8rem;">
                                        <?php for($i=0; $i<$c['valoracion']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                    </span>
                                </div>
                                <p style="margin:5px 0; color:#475569; font-size:0.9rem;"><?php echo $c['comentario']; ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</body>
</html>