<?php
session_start();
include 'db.php';

// FILTRO: Solo mostrar libros APROBADOS
$where = "estado = 'aprobado'"; 

if (isset($_GET['q']) && $_GET['q'] != '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where .= " AND (titulo LIKE '%$q%' OR autor_nombre LIKE '%$q%')";
}
if (isset($_GET['cat']) && $_GET['cat'] != 'Todas') {
    $c = mysqli_real_escape_string($conn, $_GET['cat']);
    $where .= " AND categoria = '$c'";
}

$libros = mysqli_query($conn, "SELECT * FROM recursos WHERE $where ORDER BY id DESC");
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
        /* Ajuste para que el embed no capture el scroll de la página */
        embed { pointer-events: none; } 
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div> <span style="margin-left: 10px;">Urban Canvas</span>
        </div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            
            <?php if($isLoggedIn): ?>
                <a href="upload.php" style="color:#0ea5e9;">+ Subir Aporte</a>
                
                <?php if($isAdmin): ?>
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
                        <option>Todas</option><option>Ciencias</option><option>Arte</option><option>Historia</option>
                    </select>
                    <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <?php if(isset($_GET['msg']) && $_GET['msg']=='uploaded'): ?>
                    <div class="alert alert-success" style="margin-top:20px;">¡Subido! Tu archivo está en revisión.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="container">
        <h2 class="section-title">Biblioteca Pública</h2>

        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($libros)): ?>
            <div class="book-card" style="position:relative;">
                
                <div class="<?php echo !$isLoggedIn ? 'blur-content' : ''; ?>">
                    <div class="book-img" style="overflow:hidden; padding:0; height:220px; background:#f1f5f9;">
                        <?php 
                        $ext = strtolower(pathinfo($row['archivo_pdf'], PATHINFO_EXTENSION));
                        
                        // OPCIÓN 1: Es una IMAGEN (JPG, PNG, etc)
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            echo "<img src='" . $row['archivo_pdf'] . "' style='width:100%; height:100%; object-fit:cover;'>";
                        } 
                        // OPCIÓN 2: Es un PDF (¡Aquí está el cambio!)
                        elseif ($ext == 'pdf') {
                            // Usamos embed ocultando toolbar, scrollbar y paneles para que parezca una imagen
                            echo "<embed src='" . $row['archivo_pdf'] . "#toolbar=0&navpanes=0&scrollbar=0&view=Fit' type='application/pdf' style='width:100%; height:100%; border:none; pointer-events:none; overflow:hidden;'>";
                        }
                        // OPCIÓN 3: Es WORD u otro (Icono)
                        else {
                            $icon = "fa-book";
                            $color = "#cbd5e1";
                            if($ext == 'doc' || $ext == 'docx') { $icon = "fa-file-word"; $color = "#2563eb"; }
                            
                            echo "<div style='display:flex; align-items:center; justify-content:center; height:100%; width:100%;'>";
                            echo "<i class='fa-solid $icon' style='font-size:4rem; color:$color;'></i>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                    
                    <div class="book-body">
                        <span class="tag"><?php echo $row['categoria']; ?></span>
                        <h3 style="margin:10px 0; font-size:1.1rem;"><?php echo $row['titulo']; ?></h3>
                        <p style="color:#64748b; font-size:0.9rem;">Por: <?php echo $row['autor_nombre']; ?></p>
                        <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-outline">Ver Detalles</a>
                    </div>
                </div>

                <?php if(!$isLoggedIn): ?>
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