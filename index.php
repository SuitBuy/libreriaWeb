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

// Consulta optimizada
$sql = "SELECT r.*, u.nombre AS subido_por 
        FROM recursos r 
        JOIN usuarios u ON r.usuario_id = u.id 
        WHERE $where 
        ORDER BY r.id DESC";

$libros = mysqli_query($conn, $sql);
$isLoggedIn = isset($_SESSION['uid']);
$isAdmin = isset($_SESSION['rol']) && $_SESSION['rol'] == 'admin';

// Verificar plan
$user_plan = 'gratis';
if ($isLoggedIn) {
    $uid = $_SESSION['uid'];
    $u_query = mysqli_query($conn, "SELECT plan FROM usuarios WHERE id=$uid");
    if ($u_data = mysqli_fetch_assoc($u_query)) {
        $user_plan = $u_data['plan'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Urban Canvas - Biblioteca</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
    <style>
        /* Estilos visuales extra para las tarjetas */
        .preview-container {
            width: 100%;
            height: 220px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            border-bottom: 1px solid #f1f5f9;
        }

        .preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .book-card:hover .preview-img {
            transform: scale(1.05);
        }

        .click-shield {
            position: absolute;
            inset: 0;
            z-index: 10;
            cursor: pointer;
        }

        .uploader-tag {
            font-size: 0.75rem;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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
            <a href="index.php" class="active">Inicio</a>
            <a href="premiun.php" style="color:#fbbf24; font-weight:bold;"></i> Planes</a>

            <?php if ($isLoggedIn): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin_panel.php" style="color:#38bdf8;">Admin Panel</a>
                <?php endif; ?>
                <a href="perfil.php">Mi Perfil</a>
                <a href="logout.php" style="color:#ef4444;">Salir</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero-wrapper">
        <div class="hero">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <h1>El Conocimiento es Libre.<br><span style="color:#38bdf8;">Compártelo.</span></h1>
                <p>Accede a miles de recursos académicos subidos por la comunidad.</p>

                <form method="GET" class="search-container">
                    <input type="text" name="q" class="search-input" placeholder="¿Qué quieres aprender hoy?">
                    <select name="cat" class="search-select">
                        <option>Todas</option>
                        <option>Ciencias</option>
                        <option>Arte</option>
                        <option>Historia</option>
                    </select>
                    <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <?php if ($isLoggedIn): ?>
                    <div style="margin-top: 30px;">
                        <a href="upload.php" class="hero-action-btn">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Subir Aporte
                        </a>
                    </div>
                <?php else: ?>
                    <div style="margin-top: 30px;">
                        <a href="registro.php" class="hero-action-btn" style="background: white; color: #0f172a;">
                            <i class="fa-solid fa-user-plus"></i> Únete Gratis
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['msg']) && $_GET['msg'] == 'uploaded'): ?>
                    <div class="alert alert-success" style="margin-top:20px; display:inline-block;">
                        <i class="fa-solid fa-check-circle"></i> ¡Subido! Tu archivo está en revisión.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 class="section-title">Explorar Biblioteca</h2>
        <div class="grid">
            <?php while ($row = mysqli_fetch_assoc($libros)): ?>
                <?php
                $es_contenido_premium = ($row['es_premium'] == 1);
                $tiene_acceso = false;

                if ($isLoggedIn) {
                    if (!$es_contenido_premium) $tiene_acceso = true;
                    elseif ($es_contenido_premium && $user_plan == 'premium') $tiene_acceso = true;
                    elseif ($es_contenido_premium && $_SESSION['rol'] == 'admin') $tiene_acceso = true;
                }
                ?>
                <div class="book-card" style="position:relative;">

                    <?php if ($es_contenido_premium): ?>
                        <div class="premium-badge"><i class="fa-solid fa-crown"></i> PRO</div>
                    <?php endif; ?>

                    <div class="<?php echo !$tiene_acceso ? 'blur-content' : ''; ?>">
                        <div class="preview-container">
                            <?php
                            $ruta = htmlspecialchars($row['archivo_pdf']);
                            $ext = strtolower($row['tipo_archivo']);

                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                echo "<img src='$ruta' class='preview-img' loading='lazy'>";
                            } elseif ($ext == 'pdf') {
                                echo "<iframe src='$ruta#toolbar=0&navpanes=0&scrollbar=0&view=Fit' style='width:100%; height:100%; border:none;' loading='lazy'></iframe>";
                                echo "<div class='click-shield' onclick=\"window.location='detalle.php?id={$row['id']}'\"></div>";
                            } else {
                                $icon = ($ext == 'doc' || $ext == 'docx') ? "fa-file-word" : "fa-book";
                                $color = ($ext == 'doc' || $ext == 'docx') ? "#2563eb" : "#cbd5e1";
                                echo "<i class='fa-solid $icon' style='font-size:4rem; color:$color;'></i>";
                            }
                            ?>
                        </div>
                        <div class="book-body">
                            <span class="tag"><?php echo $row['categoria']; ?></span>
                            <h3 style="margin:12px 0 5px 0; font-size:1.1rem; font-weight:700; color:#1e293b;"><?php echo $row['titulo']; ?></h3>
                            <p style="font-size:0.9rem; color:#64748b; margin:0;">Autor: <?php echo $row['autor_nombre']; ?></p>

                            <div class="uploader-tag">
                                <i class="fa-solid fa-user-circle"></i> <span><?php echo $row['subido_por']; ?></span>
                            </div>

                            <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-outline" style="margin-top:15px;">Ver Material</a>
                        </div>
                    </div>

                    <?php if (!$isLoggedIn): ?>
                        <div class="locked-overlay">
                            <div style="background:white; padding:15px; border-radius:50%; margin-bottom:10px; width:60px; height:60px; display:flex; align-items:center; justify-content:center; margin:0 auto 15px auto;">
                                <i class="fa-solid fa-lock" style="font-size:1.5rem; color:#1e293b;"></i>
                            </div>
                            <h4 style="margin:0 0 5px 0; color:#1e293b;">Contenido Privado</h4>
                            <p style="font-size:0.8rem; color:#64748b; margin-bottom:15px;">Inicia sesión para acceder.</p>
                            <a href="login.php" class="btn-login">Entrar</a>
                        </div>
                    <?php elseif (!$tiene_acceso && $es_contenido_premium): ?>
                        <div class="locked-overlay">
                            <div style="background:#fef3c7; padding:15px; border-radius:50%; margin-bottom:10px; width:60px; height:60px; display:flex; align-items:center; justify-content:center; margin:0 auto 15px auto;">
                                <i class="fa-solid fa-crown" style="font-size:1.5rem; color:#d97706;"></i>
                            </div>
                            <h4 style="margin:0 0 5px 0; color:#b45309;">Solo Premium</h4>
                            <p style="font-size:0.8rem; color:#92400e; margin-bottom:15px;">Mejora tu cuenta para ver.</p>
                            <a href="premiun.php" class="btn-login" style="background:#eab308; box-shadow: none;">Ver Planes</a>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>

</html>