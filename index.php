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

// --- OPTIMIZACIÓN CLAVE: NO USAR SELECT * ---
// Seleccionamos columna por columna. NO incluimos 'datos'.
$sql = "SELECT r.id, r.titulo, r.autor_nombre, r.categoria, r.tipo_archivo, r.vistas, u.nombre AS subido_por 
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
    <title>Urban Canvas - Biblioteca</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        .preview-container {
            width: 100%;
            height: 250px;
            background-color: #f1f5f9;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
        }

        .preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .preview-pdf {
            width: 100%;
            height: 100%;
            border: none;
            pointer-events: none;
            overflow: hidden;
        }

        .click-shield {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            background: transparent;
            cursor: pointer;
        }

        .uploader-tag {
            font-size: 0.8rem;
            color: #64748b;
            background: #f8fafc;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 8px;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div> <span style="margin-left: 10px;">Urban Canvas</span>
        </div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <?php if ($isLoggedIn): ?>
                <a href="upload.php" style="color:#0ea5e9;">+ Subir Aporte</a>
                <?php if ($isAdmin): ?>
                    <a href="admin_panel.php" style="color:#eab308; font-weight:bold;"><i class="fa-solid fa-shield-halved"></i> Admin Panel</a>
                <?php endif; ?>
                <a href="perfil.php">Mi Perfil</a>
                <a href="logout.php" style="color:#ef4444;">Salir</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero-wrapper">
        <div class="hero">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1>Comparte Conocimiento.<br>Sin Límites.</h1>
                <p>Únete a la comunidad académica más colaborativa.</p>
                <form method="GET" class="search-container">
                    <input type="text" name="q" class="search-input" placeholder="Buscar libros, autores...">
                    <select name="cat" class="search-select">
                        <option>Todas</option>
                        <option>Ciencias</option>
                        <option>Arte</option>
                        <option>Historia</option>
                    </select>
                    <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'uploaded'): ?>
                    <div class="alert alert-success" style="margin-top:20px;">¡Subido! Tu archivo está en revisión.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 class="section-title">Biblioteca Pública</h2>
        <div class="grid">
            <?php while ($row = mysqli_fetch_assoc($libros)): ?>
                <div class="book-card" style="position:relative;">
                    <div class="<?php echo !$isLoggedIn ? 'blur-content' : ''; ?>">
                        <div class="preview-container">
                            <?php
                            // Usamos ver.php para cargar la imagen real solo cuando sea necesario
                            $ruta_visual = $row['archivo_pdf'];
                            $ext = strtolower($row['tipo_archivo']);

                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                echo "<img src='$ruta_visual' class='preview-img' loading='lazy'>";
                            } elseif ($ext == 'pdf') {
                                // Iframe ligero
                                echo "<iframe src='$ruta_visual#toolbar=0&navpanes=0&scrollbar=0&view=Fit' class='preview-pdf' loading='lazy'></iframe>";
                                echo "<div class='click-shield' onclick=\"window.location='detalle.php?id={$row['id']}'\"></div>";
                            } else {
                                $icon = ($ext == 'doc' || $ext == 'docx') ? "fa-file-word" : "fa-book";
                                $color = ($ext == 'doc' || $ext == 'docx') ? "#2563eb" : "#94a3b8";
                                echo "<i class='fa-solid $icon' style='font-size:5rem; color:$color;'></i>";
                            }
                            ?>
                        </div>
                        <div class="book-body">
                            <span class="tag"><?php echo $row['categoria']; ?></span>
                            <h3 style="margin:10px 0 5px 0; font-size:1.1rem;"><?php echo $row['titulo']; ?></h3>
                            <p style="color:#64748b; font-size:0.9rem; margin:0;">Autor: <?php echo $row['autor_nombre']; ?></p>
                            <div class="uploader-tag"><i class="fa-solid fa-user-pen"></i> Subido por: <strong><?php echo $row['subido_por']; ?></strong></div>
                            <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-outline" style="margin-top:15px;">Ver Detalles</a>
                        </div>
                    </div>
                    <?php if (!$isLoggedIn): ?>
                        <div class="locked-overlay">
                            <i class="fa-solid fa-lock" style="font-size:2rem; color:#64748b; margin-bottom:10px;"></i>
                            <h4 style="margin:0;">Contenido Protegido</h4>
                            <p style="font-size:0.8rem; margin-bottom:15px;">Inicia sesión para acceder.</p>
                            <a href="login.php" class="btn-login" style="padding:8px 20px; font-size:0.9rem;">Ingresar</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>