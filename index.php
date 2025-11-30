<?php
session_start();
include 'db.php';

$where = "1";
if (isset($_GET['q']) && $_GET['q'] != '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where .= " AND (titulo LIKE '%$q%' OR autor_nombre LIKE '%$q%')";
}
if (isset($_GET['cat']) && $_GET['cat'] != 'Todas') {
    $c = mysqli_real_escape_string($conn, $_GET['cat']);
    $where .= " AND categoria = '$c'";
}

$libros = mysqli_query($conn, "SELECT * FROM recursos WHERE $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>BiblioShare - Urban Design</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div> <span style="margin-left: 10px;">BiblioShare</span>
        </div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="premium.php">Planes</a>
            <a href="contacto.php">Ayuda</a>
            <?php if(isset($_SESSION['uid'])): ?>
                <a href="perfil.php" style="color:#0f172a; font-weight:600;">Mi Perfil</a>
                <a href="logout.php" style="color:#ef4444;">Salir</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero-wrapper">
        <div class="hero">
            <div class="hero-overlay"></div> <div class="hero-content">
                <h1>Desbloquea tu potencial.<br>Comparte conocimiento.</h1>
                <p>Únete a la comunidad académica más colaborativa.</p>
                
                <form method="GET" class="search-container">
                    <input type="text" name="q" class="search-input" placeholder="Buscar libros, autores...">
                    <select name="cat" class="search-select">
                        <option>Todas</option>
                        <option>Ciencias</option>
                        <option>Arte</option>
                        <option>Historia</option>
                    </select>
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if(!isset($_GET['q'])): ?>
        <h2 class="section-title">Colecciones Destacadas</h2>
        <div class="collections-grid">
            <div class="collection-card col-green" onclick="window.location='index.php?cat=Ciencias'">
                <span class="collection-icon">🔬</span>
                <div class="collection-title">Ciencia & Tec</div>
            </div>
            <div class="collection-card col-blue" onclick="window.location='index.php?cat=Arte'">
                <span class="collection-icon">🎨</span>
                <div class="collection-title">Artes Creativas</div>
            </div>
            <div class="collection-card col-white" onclick="window.location='index.php?cat=Historia'">
                <span class="collection-icon">🏛️</span>
                <div class="collection-title">Historia & Cultura</div>
            </div>
        </div>
        <?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 class="section-title">Explorar Biblioteca</h2>
            <?php if(isset($_SESSION['uid']) && $_SESSION['rol'] == 'autor'): ?>
                <a href="upload.php" class="btn-login" style="background:#0ea5e9; text-decoration:none;">+ Subir Libro</a>
            <?php endif; ?>
        </div>

        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($libros)): ?>
            <div class="book-card">
                <div class="book-img">📚</div>
                <div class="book-body">
                    <span class="tag"><?php echo $row['categoria']; ?></span>
                    <h3 style="margin:10px 0; font-size:1.1rem;"><?php echo $row['titulo']; ?></h3>
                    <p style="color:#64748b; font-size:0.9rem;">Por: <?php echo $row['autor_nombre']; ?></p>
                    <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-outline">Ver Detalles</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <h3>BiblioShare</h3>
            <p>Conectando mentes, compartiendo futuro.</p>
            <p style="font-size:0.8rem; margin-top:20px; opacity:0.5;">&copy; 2024 Urban Design Update</p>
        </div>
    </footer>
</body>
</html>