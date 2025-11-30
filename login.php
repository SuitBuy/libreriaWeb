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
    <title>BiblioShare - Urban Style</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div> BiblioShare
        </div>
        <div class="nav-links">
            <a href="index.php">Explorar</a>
            <a href="premium.php">Comunidad</a>
            <a href="contacto.php">Soporte</a>
            <?php if(isset($_SESSION['uid'])): ?>
                <a href="perfil.php">Mi Biblioteca</a>
                <a href="logout.php" style="color:#ef4444;">Salir</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero-wrapper">
        <div class="hero">
            <div class="hero-content">
                <h1>Desbloquea tu Potencial.<br>Sumérgete en el Conocimiento.</h1>
                <p>Encuentra tu próxima gran lectura, comparte ideas y aprende de forma colaborativa.</p>
                
                <form method="GET" class="search-bar">
                    <input type="text" name="q" placeholder="Buscar libros, tesis, autores...">
                    <select name="cat">
                        <option>Todas</option>
                        <option>Ciencias</option>
                        <option>Arte</option>
                        <option>Historia</option>
                        <option>Ingeniería</option>
                    </select>
                    <button type="submit" class="search-btn">Buscar</button>
                </form>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if(!isset($_GET['q'])): ?>
        <h2 class="section-title">Colecciones Destacadas</h2>
        <div class="grid">
            <div class="card" onclick="window.location='index.php?cat=Ciencias'" style="cursor:pointer;">
                <div class="card-img tech">🔬</div>
                <div class="card-body" style="text-align:center;">
                    <h3>Ciencia & Tecnología</h3>
                    <p>Explora el futuro hoy.</p>
                </div>
            </div>
            <div class="card" onclick="window.location='index.php?cat=Arte'" style="cursor:pointer;">
                <div class="card-img art">🎨</div>
                <div class="card-body" style="text-align:center;">
                    <h3>Artes Creativas</h3>
                    <p>Inspiración sin límites.</p>
                </div>
            </div>
            <div class="card" onclick="window.location='index.php?cat=Historia'" style="cursor:pointer;">
                <div class="card-img">🏛️</div>
                <div class="card-body" style="text-align:center;">
                    <h3>Historia & Cultura</h3>
                    <p>Aprende del pasado.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <h2 class="section-title">Resultados de Búsqueda</h2>
            <?php if(isset($_SESSION['uid']) && $_SESSION['rol'] == 'autor'): ?>
                <a href="upload.php" class="btn-login" style="background:var(--primary-grad); color:white;">+ Subir Nuevo</a>
            <?php endif; ?>
        </div>

        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($libros)): ?>
            <div class="card">
                <div class="card-body">
                    <span class="tag"><?php echo $row['categoria']; ?></span>
                    <h3><?php echo $row['titulo']; ?></h3>
                    <p>Por: <?php echo $row['autor_nombre']; ?></p>
                    <p style="font-size:0.8rem; color:#94a3b8; margin-top:10px;">
                        <?php echo substr($row['descripcion'], 0, 80); ?>...
                    </p>
                    <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-card">Leer Ahora →</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <br><br>
</body>
</html>