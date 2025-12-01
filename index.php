<?php
session_start();
include 'db.php';

$where = "r.estado = 'aprobado'";
if (isset($_GET['q']) && $_GET['q'] != '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where .= " AND (r.titulo LIKE '%$q%' OR r.autor_nombre LIKE '%$q%' OR u.nombre LIKE '%$q%')";
}
if (isset($_GET['cat']) && $_GET['cat'] != 'Todas') {
    $c = mysqli_real_escape_string($conn, $_GET['cat']);
    $where .= " AND r.categoria = '$c'";
}

// CONSULTA SIMPLE (Recuperamos la ruta del archivo)
$sql = "SELECT r.*, u.nombre AS subido_por 
        FROM recursos r 
        JOIN usuarios u ON r.usuario_id = u.id 
        WHERE $where 
        ORDER BY r.id DESC";

$libros = mysqli_query($conn, $sql);
$isLoggedIn = isset($_SESSION['uid']);
$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        .preview-container {
            width: 100%;
            height: 250px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .click-shield {
            position: absolute;
            inset: 0;
            z-index: 10;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div>Urban Canvas
        </div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <?php if ($isLoggedIn): ?>
                <a href="upload.php">+ Subir</a>
                <?php if ($isAdmin): ?><a href="admin_panel.php" style="color:#eab308;">Admin</a><?php endif; ?>
                <a href="perfil.php">Perfil</a>
                <a href="logout.php" style="color:red;">Salir</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container" style="margin-top:40px;">
        <h2 class="section-title">Biblioteca Pública</h2>
        <div class="grid">
            <?php while ($row = mysqli_fetch_assoc($libros)): ?>
                <div class="book-card" style="position:relative;">
                    <div class="<?php echo !$isLoggedIn ? 'blur-content' : ''; ?>">
                        <div class="preview-container">
                            <?php
                            $ruta = htmlspecialchars($row['archivo_pdf']); // Ruta directa: uploads/archivo.jpg
                            $ext = strtolower($row['tipo_archivo']);

                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                echo "<img src='$ruta' class='preview-img' loading='lazy'>";
                            } elseif ($ext == 'pdf') {
                                // PDF directo
                                echo "<iframe src='$ruta#toolbar=0&navpanes=0&scrollbar=0&view=Fit' style='width:100%; height:100%; border:none;' loading='lazy'></iframe>";
                                echo "<div class='click-shield' onclick=\"window.location='detalle.php?id={$row['id']}'\"></div>";
                            } else {
                                echo "<i class='fa-solid fa-file-lines' style='font-size:5rem; color:#cbd5e1;'></i>";
                            }
                            ?>
                        </div>
                        <div class="book-body">
                            <span class="tag"><?php echo $row['categoria']; ?></span>
                            <h3 style="margin:10px 0;"><?php echo $row['titulo']; ?></h3>
                            <p style="font-size:0.9rem; color:#64748b;">Por: <?php echo $row['autor_nombre']; ?></p>
                            <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-outline">Ver Detalles</a>
                        </div>
                    </div>
                    <?php if (!$isLoggedIn): ?>
                        <div class="locked-overlay">
                            <i class="fa-solid fa-lock" style="font-size:2rem; margin-bottom:10px;"></i>
                            <p>Inicia sesión para ver.</p>
                            <a href="login.php" class="btn-login">Ingresar</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>