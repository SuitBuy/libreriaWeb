<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$uid = $_SESSION['uid'];

// Obtener info del recurso y los datos del usuario que lo subió
// Nota: Traemos u.id como usuario_id para el enlace
$query = "SELECT r.*, u.nombre AS subido_por, u.id as usuario_id 
          FROM recursos r 
          JOIN usuarios u ON r.usuario_id = u.id 
          WHERE r.id=$id";
$row = mysqli_fetch_assoc(mysqli_query($conn, $query));

// Si no existe el recurso
if (!$row) {
    header("Location: index.php");
    exit;
}

// --- VALIDACIÓN DE PLAN PREMIUM ---
$user_query = mysqli_query($conn, "SELECT plan, rol FROM usuarios WHERE id = $uid");
$user = mysqli_fetch_assoc($user_query);

if ($row['es_premium'] == 1 && $user['plan'] == 'gratis' && $user['rol'] != 'admin') {
    echo "<script>alert('Contenido exclusivo para Premium.'); window.location='premiun.php';</script>";
    exit;
}

// Historial de visitas (Contador Único)
$check_vista = mysqli_query($conn, "SELECT id FROM historial_vistas WHERE usuario_id = $uid AND recurso_id = $id");
if (mysqli_num_rows($check_vista) == 0) {
    mysqli_query($conn, "UPDATE recursos SET vistas = vistas + 1 WHERE id = $id");
    mysqli_query($conn, "INSERT INTO historial_vistas (usuario_id, recurso_id) VALUES ($uid, $id)");
}

// Comentarios
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $com = mysqli_real_escape_string($conn, $_POST['comentario']);
    $val = (int)$_POST['valoracion'];
    // Validación básica anti XSS al guardar o mostrar
    mysqli_query($conn, "INSERT INTO comentarios (recurso_id, usuario_id, comentario, valoracion, fecha) VALUES ($id, $uid, '$com', $val, NOW())");
}
$comentarios = mysqli_query($conn, "SELECT c.*, u.nombre FROM comentarios c JOIN usuarios u ON c.usuario_id = u.id WHERE recurso_id=$id ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo $row['titulo']; ?> - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
    <style>
        iframe {
            display: block;
            background: #fff;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div>Urban Canvas
        </div>
        <a href="index.php">← Volver</a>
    </nav>

    <div class="container" style="margin-top:20px; max-width: 95%;">
        <div style="background:white; border-radius:30px; padding:30px; box-shadow:var(--card-shadow); display:flex; gap:30px; flex-wrap:wrap;">

            <div style="flex:3; border-radius:20px; overflow:hidden; border:1px solid #e2e8f0; min-height:850px; background:white;">
                <?php
                $ruta = $row['archivo_pdf'];
                $ext = strtolower($row['tipo_archivo']);

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    echo "<div style='display:flex; align-items:center; justify-content:center; height:100%; background:#f8fafc;'>
                            <img src='$ruta' style='max-width:100%; max-height:850px; object-fit:contain;'>
                          </div>";
                } elseif ($ext == 'pdf') {
                    // FitH asegura que se vea ancho completo
                    echo "<iframe src='$ruta#toolbar=0&navpanes=0&scrollbar=0&view=FitH' width='100%' height='100%' style='border:none; min-height:850px;'></iframe>";
                } else {
                    echo "<div style='display:flex; align-items:center; justify-content:center; height:850px; background:#f8fafc; color:#2563eb; flex-direction:column;'>
                            <i class='fa-solid fa-file-lines' style='font-size:8rem;'></i>
                            <p style='margin-top:20px;'>Vista previa no disponible para este formato.</p>
                            <a href='$ruta' class='btn-login' style='margin-top:10px;'>Descargar para ver</a>
                          </div>";
                }
                ?>
            </div>

            <div style="flex:1; min-width:300px;">
                <span class="tag" style="margin-bottom:15px; display:inline-block;"><?php echo $row['categoria']; ?></span>
                <?php if ($row['es_premium']): ?><span style="background:#eab308; color:white; padding:2px 8px; border-radius:5px; font-size:0.8rem; margin-left:5px;">PREMIUM</span><?php endif; ?>

                <h1 style="font-size:2.5rem; margin:10px 0; line-height:1.2;"><?php echo $row['titulo']; ?></h1>

                <div style="margin-bottom:25px;">
                    <p>Autor Obra: <strong><?php echo $row['autor_nombre']; ?></strong></p>
                    <p style="color:#0ea5e9;">
                        Subido por:
                        <a href="autor.php?id=<?php echo $row['usuario_id']; ?>" style="color:#2563eb; text-decoration:none; font-weight:bold;">
                            <?php echo $row['subido_por']; ?>
                        </a>
                    </p>
                </div>

                <div style="background:#f1f5f9; padding:20px; border-radius:20px; margin-bottom:20px;">
                    <h4 style="margin-top:0;">Descripción:</h4>
                    <p style="color:#475569; font-size:0.95rem;">
                        <?php echo nl2br($row['descripcion']); ?>
                    </p>
                </div>

                <a href="<?php echo $ruta; ?>" download target="_blank" class="btn-login" style="display:block; text-align:center; padding:15px;">
                    <i class="fa-solid fa-download"></i> Descargar Archivo
                </a>

                <div style="margin-top:20px; text-align:center; color:#64748b;">
                    <i class="fa-solid fa-eye"></i> <?php echo $row['vistas']; ?> visualizaciones
                </div>

                <h3 style="margin-top:40px; border-top:1px solid #eee; padding-top:20px;">Comentarios</h3>

                <form method="POST" style="margin-bottom:20px;">
                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                        <select name="valoracion" class="input-field" style="width:auto; flex:1;">
                            <option value="5">⭐⭐⭐⭐⭐</option>
                            <option value="4">⭐⭐⭐⭐</option>
                            <option value="3">⭐⭐⭐</option>
                            <option value="2">⭐⭐</option>
                            <option value="1">⭐</option>
                        </select>
                    </div>
                    <textarea name="comentario" class="input-field" placeholder="Escribe tu opinión sobre este recurso..." rows="2" required></textarea>
                    <button type="submit" class="btn-login" style="width:100%; margin-top:5px;">Publicar Comentario</button>
                </form>

                <div style="max-height:400px; overflow-y:auto; padding-right:5px;">
                    <?php if (mysqli_num_rows($comentarios) == 0): ?>
                        <p style="color:#94a3b8; font-style:italic;">Sé el primero en comentar.</p>
                    <?php else: ?>
                        <?php while ($c = mysqli_fetch_assoc($comentarios)): ?>
                            <div style="background:#f8fafc; padding:15px; border-radius:15px; margin-bottom:10px; border:1px solid #f1f5f9;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                                    <strong><?php echo htmlspecialchars($c['nombre']); ?></strong>
                                    <span style="color:#eab308; font-size:0.8rem;">
                                        <?php for ($i = 0; $i < $c['valoracion']; $i++) echo '★'; ?>
                                    </span>
                                </div>
                                <p style="margin:0; font-size:0.9rem; color:#475569;"><?php echo htmlspecialchars($c['comentario']); ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container" style="margin-top: 60px; margin-bottom: 60px;">
        <h3 class="section-title">También te podría interesar</h3>
        <div class="grid">
            <?php
            $cat_actual = mysqli_real_escape_string($conn, $row['categoria']);
            $id_actual = (int)$id;

            // Buscar 3 libros de la misma categoría que NO sean el actual
            $sql_rel = "SELECT * FROM recursos WHERE categoria = '$cat_actual' AND id != $id_actual AND estado = 'aprobado' LIMIT 3";
            $relacionados = mysqli_query($conn, $sql_rel);

            if (mysqli_num_rows($relacionados) > 0):
                while ($rel = mysqli_fetch_assoc($relacionados)):
            ?>
                    <div class="book-card">
                        <div class="book-body">
                            <span class="tag"><?php echo $rel['categoria']; ?></span>
                            <h4 style="margin: 10px 0; font-size: 1rem;"><?php echo $rel['titulo']; ?></h4>
                            <p style="font-size: 0.85rem; color: #64748b;">Por: <?php echo $rel['autor_nombre']; ?></p>
                            <a href="detalle.php?id=<?php echo $rel['id']; ?>" class="btn-outline" style="margin-top:10px;">Ver Material</a>
                        </div>
                    </div>
            <?php
                endwhile;
            else:
                echo "<p style='color:#94a3b8; grid-column: 1/-1; text-align:center;'>No hay más recomendaciones en esta categoría por ahora.</p>";
            endif;
            ?>
        </div>
    </div>
</body>

</html>