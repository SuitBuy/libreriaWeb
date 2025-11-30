<?php
session_start();
include 'db.php';

// Filtros
$where = "1";
if (isset($_GET['q']) && $_GET['q'] != '') {
    $q = mysqli_real_escape_string($conn, $_GET['q']);
    $where .= " AND (titulo LIKE '%$q%' OR autor_nombre LIKE '%$q%')";
}
if (isset($_GET['cat']) && $_GET['cat'] != 'Todas') {
    $c = mysqli_real_escape_string($conn, $_GET['cat']);
    $where .= " AND categoria = '$c'";
}

// Consulta Principal
$libros = mysqli_query($conn, "SELECT * FROM recursos WHERE $where ORDER BY id DESC");

// Consulta para Recomendados (Simulada: los más vistos)
$recomendados = mysqli_query($conn, "SELECT * FROM recursos ORDER BY vistas DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>BiblioShare - Repositorio Académico</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <div class="nav-links">
            <a href="index.php">Inicio</a>
            <a href="premium.php" style="color:gold;">Premium</a>
            <a href="contacto.php">Ayuda</a>
            
            <?php if(isset($_SESSION['uid'])): ?>
                <a href="perfil.php">Mi Perfil</a>
                <span style="margin-left:10px; color:#ccc;">|</span>
                <span><?php echo $_SESSION['nombre']; ?></span>
                <a href="logout.php" style="color:#ff6b6b; margin-left:10px;">Salir</a>
            <?php else: ?>
                <a href="login.php">Ingresar</a>
                <a href="registro.php" class="btn-action">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <div style="background:linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1507842217343-583bb726cc2e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1502&q=80'); background-size:cover; color:white; padding:80px 20px; text-align:center;">
        <h1 style="font-size:2.5rem; margin-bottom:10px;">Acceso Democrático al Conocimiento</h1>
        <p style="margin-bottom:30px; font-size:1.1rem;">Descubre libros, tesis y artículos de forma rápida y legal.</p>
        
        <form method="GET" style="max-width:700px; margin:auto; display:flex; gap:10px; background:white; padding:10px; border-radius:8px;">
            <input type="text" name="q" placeholder="¿Qué quieres investigar hoy?" style="margin:0; border:none; padding:10px;">
            <select name="cat" style="width:180px; margin:0; border:none; border-left:1px solid #ddd;">
                <option>Todas</option><option>Ingeniería</option><option>Medicina</option><option>Derecho</option><option>Literatura</option>
            </select>
            <button type="submit" style="width:120px;">Buscar</button>
        </form>
        
        <?php if(isset($_SESSION['uid']) && $_SESSION['rol'] == 'autor'): ?>
            <div style="margin-top:20px;">
                <button onclick="document.getElementById('modal').style.display='block'" style="width:auto; padding:10px 30px; background:var(--success);">+ Publicar Obra</button>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <?php if(mysqli_num_rows($recomendados) > 0 && !isset($_GET['q'])): ?>
        <div style="margin-bottom:40px;">
            <h2 style="border-bottom:2px solid var(--primary); padding-bottom:10px; display:inline-block;">🔥 Recomendados para ti</h2>
            <div class="grid">
                <?php while($rec = mysqli_fetch_assoc($recomendados)): ?>
                <div class="card" style="border:1px solid gold;">
                    <div class="card-body">
                        <div class="tag">Tendencia</div>
                        <h3><?php echo $rec['titulo']; ?></h3>
                        <p style="color:#666;">Por: <?php echo $rec['autor_nombre']; ?></p>
                        <small>👁️ <?php echo $rec['vistas']; ?> lecturas</small>
                        <a href="detalle.php?id=<?php echo $rec['id']; ?>" class="btn-action" style="display:block; text-align:center; margin-top:10px; text-decoration:none;">Leer</a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php endif; ?>

        <h2>📚 Biblioteca General</h2>
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

    <footer style="background:#222; color:white; padding:40px 0; margin-top:50px; text-align:center;">
        <div class="container">
            <p style="color:#999; margin-bottom:20px;">Nuestras Alianzas Clave y Fuentes de Acceso Abierto</p>
            <div style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap; opacity:0.7;">
                <span>🏛️ Scielo</span>
                <span>🎓 Google Scholar</span>
                <span>📖 RedALyC</span>
                <span>📚 Dialnet</span>
                <span>⚖️ Creative Commons</span>
            </div>
            <p style="margin-top:30px; font-size:0.8rem; color:#666;">&copy; 2024 BiblioShare - Grupo 6 UTP. Todos los derechos reservados.</p>
        </div>
    </footer>

    <div id="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000;">
        <div class="auth-box" style="margin-top:80px;">
            <h3>Publicar Nueva Obra</h3>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <label>Título de la Obra</label><input type="text" name="titulo" required>
                <label>Autor Principal</label><input type="text" name="autor" required>
                <label>Categoría</label>
                <select name="categoria"><option>Ingeniería</option><option>Medicina</option><option>Derecho</option><option>Literatura</option><option>Ciencias</option></select>
                <label>Descripción / Resumen</label><textarea name="desc"></textarea>
                <label>Archivo PDF</label><input type="file" name="archivo" accept=".pdf" required>
                <button type="submit">Publicar</button>
                <button type="button" onclick="document.getElementById('modal').style.display='none'" style="background:#666; margin-top:5px;">Cancelar</button>
            </form>
        </div>
    </div>
</body>
</html>