<?php
session_start();
include 'db.php';

// Filtros de búsqueda (Requisito: Búsqueda eficiente)
$where = "1";
if (isset($_GET['q']) && $_GET['q'] != '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where .= " AND (titulo LIKE '%$q%' OR autor_nombre LIKE '%$q%')";
}
if (isset($_GET['cat']) && $_GET['cat'] != 'Todas') {
    $c = mysqli_real_escape_string($conn, $_GET['cat']);
    $where .= " AND categoria = '$c'";
}

// Libros generales
$libros = mysqli_query($conn, "SELECT * FROM recursos WHERE $where ORDER BY id DESC");

// Recomendados (Requisito: Recomendaciones según intereses/vistas)
$recomendados = mysqli_query($conn, "SELECT * FROM recursos ORDER BY vistas DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>BiblioShare - Inicio</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="premium.php" style="color:#f1c40f;">Premium/Donar</a>
            <a href="contacto.php">Ayuda</a>
            <?php if(isset($_SESSION['uid'])): ?>
                <a href="perfil.php"><strong>Mi Perfil</strong></a>
                <a href="logout.php" style="color:#ff6b6b;">Salir</a>
            <?php else: ?>
                <a href="login.php">Ingresar</a>
                <a href="registro.php" class="btn-action">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <div style="background:linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80'); background-size:cover; color:white; padding:80px 20px; text-align:center;">
        <h1 style="font-size:2.5rem;">Acceso Democrático al Conocimiento</h1>
        <p>Busca, comparte y publica investigaciones académicas.</p>
        
        <form method="GET" style="max-width:700px; margin:20px auto; display:flex; gap:10px; background:white; padding:10px; border-radius:8px;">
            <input type="text" name="q" placeholder="Buscar libro, autor o tema..." style="margin:0; border:none;">
            <select name="cat" style="width:150px; margin:0; border:none; border-left:1px solid #ddd;">
                <option>Todas</option><option>Ingeniería</option><option>Medicina</option><option>Derecho</option><option>Literatura</option>
            </select>
            <button type="submit" style="width:120px;">Buscar</button>
        </form>
        
        <?php if(isset($_SESSION['uid']) && $_SESSION['rol'] == 'autor'): ?>
            <button onclick="document.getElementById('modal').style.display='block'" style="width:auto; padding:10px 30px; margin-top:10px; background:var(--success);">+ Publicar Obra</button>
        <?php endif; ?>
    </div>

    <div class="container">
        <?php if(!isset($_GET['q']) && mysqli_num_rows($recomendados) > 0): ?>
            <h2 style="border-bottom:2px solid var(--primary); display:inline-block; padding-bottom:5px;">🔥 Lo más leído</h2>
            <div class="grid" style="margin-bottom:40px;">
                <?php while($rec = mysqli_fetch_assoc($recomendados)): ?>
                <div class="card" style="border:1px solid #f1c40f;">
                    <div class="card-body">
                        <span class="tag" style="background:#f1c40f; color:black;">Tendencia</span>
                        <h3><?php echo $rec['titulo']; ?></h3>
                        <p style="color:#666;">Por: <?php echo $rec['autor_nombre']; ?></p>
                        <small>👁️ <?php echo $rec['vistas']; ?> lecturas</small>
                        <a href="detalle.php?id=<?php echo $rec['id']; ?>" class="btn-action" style="display:block; text-align:center; margin-top:10px; text-decoration:none;">Leer</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <h2>📚 Biblioteca General</h2>
        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($libros)): ?>
            <div class="card">
                <div class="card-body">
                    <span class="tag"><?php echo $row['categoria']; ?></span>
                    <h3><?php echo $row['titulo']; ?></h3>
                    <p style="color:#666;">Por: <?php echo $row['autor_nombre']; ?></p>
                    <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-action" style="display:block; text-align:center; text-decoration:none;">Ver Detalles</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="auth-box" style="margin-top:80px;">
            <h3>Publicar Recurso</h3>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <label>Título</label><input type="text" name="titulo" required>
                <label>Autor</label><input type="text" name="autor" required>
                <label>Categoría</label>
                <select name="categoria"><option>Ingeniería</option><option>Medicina</option><option>Derecho</option><option>Ciencias</option></select>
                <label>Descripción</label><textarea name="desc"></textarea>
                <label>Archivo PDF</label><input type="file" name="archivo" accept=".pdf" required>
                <button type="submit">Publicar</button>
                <button type="button" onclick="document.getElementById('modal').style.display='none'" style="background:#666; margin-top:5px;">Cancelar</button>
            </form>
        </div>
    </div>
    
    <footer style="background:#222; color:#999; padding:20px; text-align:center; margin-top:50px;">
        <p>Aliados: Scielo | RedALyC | Google Scholar | Creative Commons</p>
        <p>&copy; 2024 BiblioShare - Grupo 6 UTP</p>
    </footer>
</body>
</html>