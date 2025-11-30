<?php
session_start();
include 'db.php';

// Filtros de búsqueda
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
<html>
<head>
    <title>BiblioShare</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <div class="nav-links">
            <?php if(isset($_SESSION['uid'])): ?>
                <span>Hola, <?php echo $_SESSION['nombre']; ?></span>
                <a href="logout.php" style="color:#ff6b6b;">Salir</a>
            <?php else: ?>
                <a href="login.php">Ingresar</a>
                <a href="registro.php" class="btn-action">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <div style="background:#333; color:white; padding:40px 20px; text-align:center;">
        <h1>Encuentra y Comparte Conocimiento</h1>
        <form method="GET" style="max-width:600px; margin:auto; display:flex; gap:10px;">
            <input type="text" name="q" placeholder="Buscar libro o autor..." style="margin:0;">
            <select name="cat" style="width:150px; margin:0;">
                <option>Todas</option><option>Ingeniería</option><option>Medicina</option><option>Derecho</option>
            </select>
            <button type="submit" style="width:100px;">Buscar</button>
        </form>
        
        <?php if(isset($_SESSION['uid'])): ?>
            <br>
            <button onclick="document.getElementById('modal').style.display='block'" style="width:200px; background:var(--success);">+ Subir Nuevo Libro</button>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>Recursos Disponibles</h2>
        <div class="grid">
            <?php while($row = mysqli_fetch_assoc($libros)): ?>
            <div class="card">
                <div class="card-body">
                    <div class="tag"><?php echo $row['categoria']; ?></div>
                    <h3><?php echo $row['titulo']; ?></h3>
                    <p style="color:#666; font-size:0.9rem;">Por: <?php echo $row['autor_nombre']; ?></p>
                    <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-action" style="display:block; text-align:center; text-decoration:none;">Ver Detalles</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
        <div class="auth-box" style="margin-top:100px;">
            <h3>Publicar Recurso</h3>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <label>Título</label><input type="text" name="titulo" required>
                <label>Autor</label><input type="text" name="autor" required>
                <label>Categoría</label>
                <select name="categoria"><option>Ingeniería</option><option>Medicina</option><option>Derecho</option></select>
                <label>Descripción</label><textarea name="desc"></textarea>
                <label>PDF</label><input type="file" name="archivo" accept=".pdf" required>
                <button type="submit">Publicar</button>
                <button type="button" onclick="document.getElementById('modal').style.display='none'" style="background:#666; margin-top:5px;">Cancelar</button>
            </form>
        </div>
    </div>
</body>
</html>