<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) { header("Location: index.php"); exit; }
$uid = (int)$_GET['id'];

// Obtener datos del autor (Solo datos públicos)
$query = mysqli_query($conn, "SELECT nombre, descripcion, avatar, rol, fecha_registro, ubicacion, trabajo, web FROM usuarios WHERE id = $uid");
$autor = mysqli_fetch_assoc($query);

if (!$autor) { echo "Autor no encontrado"; exit; }

// Obtener sus aportes APROBADOS
$libros = mysqli_query($conn, "SELECT * FROM recursos WHERE usuario_id = $uid AND estado = 'aprobado' ORDER BY id DESC");

// Avatares (Misma lógica que perfil.php)
$avatars = [
    'avatar1' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Felix',
    'avatar2' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Aneka',
    'avatar3' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Bob',
    'avatar4' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Cali',
    'avatar5' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Dante',
    'avatar6' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Eliza',
    'avatar7' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Ginger',
    'avatar8' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Harley',
    'avatar9' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Isaac',
    'avatar10' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=Jack'
];
$avatar_url = isset($avatars[$autor['avatar']]) ? $avatars[$autor['avatar']] : $avatars['avatar1'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Perfil de <?php echo $autor['nombre']; ?> - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
    <style>
        .banner { height: 150px; background: var(--grad-primary); border-radius: 20px 20px 0 0; }
        .profile-card { background: white; border-radius: 20px; box-shadow: var(--shadow-md); overflow: hidden; margin-bottom: 40px; }
        .profile-content { padding: 30px; text-align: center; margin-top: -60px; }
        .avatar-img { width: 120px; height: 120px; border-radius: 50%; border: 5px solid white; background: white; object-fit: cover; }
        .meta-tags { display: flex; justify-content: center; gap: 15px; margin-top: 15px; flex-wrap: wrap; color: var(--text-muted); font-size: 0.9rem; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php">← Volver a la Biblioteca</a>
    </nav>

    <div class="container" style="max-width: 900px; margin-top: 40px;">
        
        <div class="profile-card">
            <div class="banner"></div>
            <div class="profile-content">
                <img src="<?php echo $avatar_url; ?>" class="avatar-img">
                <h1 style="margin: 10px 0 5px 0;"><?php echo $autor['nombre']; ?></h1>
                <span class="tag"><?php echo ucfirst($autor['rol']); ?></span>
                
                <?php if($autor['descripcion']): ?>
                    <p style="margin-top: 15px; color: var(--text-main); max-width: 600px; margin-left: auto; margin-right: auto;">
                        <?php echo nl2br($autor['descripcion']); ?>
                    </p>
                <?php endif; ?>

                <div class="meta-tags">
                    <?php if($autor['ubicacion']) echo "<span><i class='fa-solid fa-map-pin'></i> {$autor['ubicacion']}</span>"; ?>
                    <?php if($autor['trabajo']) echo "<span><i class='fa-solid fa-briefcase'></i> {$autor['trabajo']}</span>"; ?>
                    <span><i class='fa-solid fa-calendar'></i> Miembro desde <?php echo date("M Y", strtotime($autor['fecha_registro'])); ?></span>
                </div>
            </div>
        </div>

        <h3 class="section-title">Aportes Publicados</h3>
        
        <?php if(mysqli_num_rows($libros) == 0): ?>
            <div class="alert alert-error" style="background:white; border:1px solid #e2e8f0; color:#64748b;">
                Este usuario aún no ha publicado nada.
            </div>
        <?php else: ?>
            <div class="grid">
                <?php while($row = mysqli_fetch_assoc($libros)): ?>
                    <div class="book-card">
                        <div class="book-body">
                            <span class="tag"><?php echo $row['categoria']; ?></span>
                            <h4 style="margin: 10px 0;"><?php echo $row['titulo']; ?></h4>
                            <p style="font-size: 0.85rem; color: #64748b;">Autor Obra: <?php echo $row['autor_nombre']; ?></p>
                            <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-outline">Ver Aporte</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>