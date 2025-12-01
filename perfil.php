<?php
session_start();
include 'db.php';

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];

// --- LÓGICA 1: ELIMINAR PUBLICACIÓN ---
if (isset($_GET['borrar'])) {
    $id_recurso = (int)$_GET['borrar'];

    // Verificar propiedad y obtener archivo
    $check = mysqli_query($conn, "SELECT id, archivo_pdf FROM recursos WHERE id = $id_recurso AND usuario_id = $uid");

    if ($row = mysqli_fetch_assoc($check)) {
        // BORRAR DEL DISCO
        if (file_exists($row['archivo_pdf'])) {
            unlink($row['archivo_pdf']);
        }
        // BORRAR DE BD
        mysqli_query($conn, "DELETE FROM recursos WHERE id = $id_recurso");
        header("Location: perfil.php?msg=deleted");
    }
}
// --- LÓGICA 2: ACTUALIZAR PERFIL ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $ubicacion = mysqli_real_escape_string($conn, $_POST['ubicacion']);
    $trabajo = mysqli_real_escape_string($conn, $_POST['trabajo']);
    $web = mysqli_real_escape_string($conn, $_POST['web']);
    $nacimiento = mysqli_real_escape_string($conn, $_POST['nacimiento']);
    $avatar = mysqli_real_escape_string($conn, $_POST['avatar']);

    $sql = "UPDATE usuarios SET descripcion='$bio', ubicacion='$ubicacion', trabajo='$trabajo', web='$web', fecha_nacimiento='$nacimiento', avatar='$avatar' WHERE id=$uid";
    mysqli_query($conn, $sql);
    header("Location: perfil.php?msg=updated");
}

// OBTENER DATOS DE USUARIO
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM usuarios WHERE id = $uid"));

// OBTENER PUBLICACIONES DEL USUARIO
$mis_libros = mysqli_query($conn, "SELECT id, titulo, vistas, estado FROM recursos WHERE usuario_id = $uid ORDER BY id DESC");
$total_vistas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(vistas) as total FROM recursos WHERE usuario_id = $uid"))['total'];

// Lista de Avatares Predefinidos (API DiceBear)
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
$mi_avatar = isset($avatars[$user['avatar']]) ? $avatars[$user['avatar']] : $avatars['avatar1'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Perfil - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
    <style>
        /* Estilos específicos del Perfil */
        .profile-header {
            position: relative;
            margin-bottom: 60px;
        }

        .banner {
            height: 180px;
            background: var(--primary-grad);
            border-radius: 20px;
        }

        .profile-pic-container {
            position: absolute;
            bottom: -50px;
            left: 40px;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: white;
            padding: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-pic {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            background: #eee;
        }

        .edit-btn {
            position: absolute;
            bottom: -50px;
            right: 20px;
            border: 1px solid #cbd5e1;
            padding: 8px 20px;
            border-radius: 20px;
            text-decoration: none;
            color: #1e293b;
            font-weight: 600;
            background: white;
            transition: 0.3s;
        }

        .edit-btn:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .profile-info {
            margin-top: 60px;
            padding: 0 10px;
        }

        .meta-list {
            display: flex;
            gap: 20px;
            color: #64748b;
            font-size: 0.9rem;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .meta-list i {
            margin-right: 5px;
        }

        /* Modal de Edición (Simple con details/summary) */
        details>summary {
            list-style: none;
            cursor: pointer;
            outline: none;
        }

        details[open] .edit-modal {
            display: block;
        }

        .edit-modal {
            background: #f8fafc;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            margin-top: 20px;
        }

        .avatar-selector {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .avatar-option {
            cursor: pointer;
            border: 3px solid transparent;
            border-radius: 50%;
            width: 50px;
            height: 50px;
        }

        .avatar-option:hover {
            transform: scale(1.1);
        }

        input[type="radio"]:checked+img {
            border-color: #0ea5e9;
        }

        input[type="radio"] {
            display: none;
        }

        /* Stats y Grid */
        .stats-row {
            display: flex;
            gap: 30px;
            margin: 20px 0;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
            padding: 15px 0;
        }

        .stat-item b {
            display: block;
            font-size: 1.2rem;
            color: #0f172a;
        }

        .stat-item span {
            font-size: 0.85rem;
            color: #64748b;
        }

        .delete-btn {
            color: #ef4444;
            background: #fee2e2;
            padding: 5px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            text-decoration: none;
            font-weight: 600;
        }

        .delete-btn:hover {
            background: #fecaca;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div>Urban Canvas
        </div>
        <div class="nav-links"><a href="index.php">Inicio</a><a href="logout.php" style="color:#ef4444;">Salir</a></div>
    </nav>

    <div class="container" style="max-width: 900px; margin-top: 30px;">

        <div class="background:white; border-radius: 20px; box-shadow: var(--card-shadow); overflow: hidden; background: white; padding-bottom: 20px;">
            <div class="profile-header">
                <div class="banner"></div>
                <div class="profile-pic-container">
                    <img src="<?php echo $mi_avatar; ?>" class="profile-pic">
                </div>

                <details style="position: absolute; width: 100%;">
                    <summary class="edit-btn">Editar Perfil</summary>

                    <div style="position: absolute; top: 180px; left: 0; right: 0; z-index: 10; padding: 0 20px;">
                        <div class="edit-modal">
                            <h3 style="margin-top:0;">Personaliza tu Espacio</h3>
                            <form method="POST">
                                <label style="font-size:0.9rem; font-weight:bold;">Elige tu Avatar:</label>
                                <div class="avatar-selector">
                                    <?php foreach ($avatars as $key => $url): ?>
                                        <label>
                                            <input type="radio" name="avatar" value="<?php echo $key; ?>" <?php if ($user['avatar'] == $key) echo 'checked'; ?>>
                                            <img src="<?php echo $url; ?>" class="avatar-option">
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                                    <input type="text" name="trabajo" class="input-field" placeholder="Cargo / Profesión" value="<?php echo $user['trabajo']; ?>">
                                    <input type="text" name="ubicacion" class="input-field" placeholder="Ciudad, País" value="<?php echo $user['ubicacion']; ?>">
                                    <input type="date" name="nacimiento" class="input-field" value="<?php echo $user['fecha_nacimiento']; ?>">
                                    <input type="text" name="web" class="input-field" placeholder="Enlace (LinkedIn, GitHub...)" value="<?php echo $user['web']; ?>">
                                </div>
                                <textarea name="bio" class="input-field" placeholder="Cuéntanos sobre ti..." rows="3"><?php echo $user['descripcion']; ?></textarea>

                                <button type="submit" name="update_profile" class="btn-login" style="width:100%;">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </details>
            </div>

            <div class="profile-info">
                <h1 style="margin: 0; font-size: 1.8rem;">
                    <?php echo $user['nombre']; ?>
                    <?php if ($_SESSION['rol'] == 'admin') echo '<i class="fa-solid fa-certificate" style="color:#0ea5e9; font-size:1.2rem;" title="Verificado"></i>'; ?>
                </h1>
                <p style="color: #64748b; margin: 5px 0 15px 0;"><?php echo $user['rol']; ?></p>

                <?php if ($user['descripcion']): ?>
                    <p style="color: #1e293b; line-height: 1.5;"><?php echo nl2br($user['descripcion']); ?></p>
                <?php else: ?>
                    <p style="color: #cbd5e1; font-style: italic;">Sin descripción aún.</p>
                <?php endif; ?>

                <div class="meta-list">
                    <?php if ($user['ubicacion']): ?><span><i class="fa-solid fa-location-dot"></i> <?php echo $user['ubicacion']; ?></span><?php endif; ?>
                    <?php if ($user['trabajo']): ?><span><i class="fa-solid fa-briefcase"></i> <?php echo $user['trabajo']; ?></span><?php endif; ?>
                    <?php if ($user['web']): ?><span><i class="fa-solid fa-link"></i> <a href="<?php echo $user['web']; ?>" target="_blank" style="color:#0ea5e9; text-decoration:none;">Website</a></span><?php endif; ?>
                    <?php if ($user['fecha_nacimiento']): ?><span><i class="fa-solid fa-cake-candles"></i> <?php echo date("d/m", strtotime($user['fecha_nacimiento'])); ?></span><?php endif; ?>
                    <span><i class="fa-regular fa-calendar"></i> Se unió en <?php echo date("M Y", strtotime($user['fecha_registro'])); ?></span>
                </div>

                <div class="stats-row">
                    <div class="stat-item"><b><?php echo mysqli_num_rows($mis_libros); ?></b><span>Aportes</span></div>
                    <div class="stat-item"><b><?php echo $total_vistas ? $total_vistas : 0; ?></b><span>Visualizaciones Totales</span></div>
                </div>
            </div>
        </div>

        <h3 class="section-title" style="margin-top: 40px;">Mis Aportes</h3>

        <?php if (mysqli_num_rows($mis_libros) == 0): ?>
            <div style="text-align:center; padding: 40px; color: #94a3b8; border: 2px dashed #e2e8f0; border-radius: 20px;">
                <i class="fa-solid fa-folder-open" style="font-size: 3rem; margin-bottom: 15px;"></i>
                <p>Aún no has compartido nada con la comunidad.</p>
                <a href="upload.php" class="btn-login" style="margin-top: 10px;">+ Subir mi primer aporte</a>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php while ($row = mysqli_fetch_assoc($mis_libros)): ?>
                    <div class="book-card">
                        <div style="padding: 15px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <?php
                            $stClass = 'st-pendiente';
                            if ($row['estado'] == 'aprobado') $stClass = 'st-aprobado';
                            if ($row['estado'] == 'rechazado') $stClass = 'st-rechazado'; // Definir este estilo en CSS si quieres rojo
                            ?>
                            <span class="status-badge <?php echo $stClass; ?>" style="background:<?php echo ($row['estado'] == 'aprobado' ? '#10b981' : ($row['estado'] == 'rechazado' ? '#ef4444' : '#f59e0b')); ?>">
                                <?php echo ucfirst($row['estado']); ?>
                            </span>

                            <a href="perfil.php?borrar=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('¿Estás seguro? Esto no se puede deshacer.');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>

                        <div class="book-body">
                            <h4 style="margin: 0 0 5px 0;"><?php echo $row['titulo']; ?></h4>
                            <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">
                                <i class="fa-solid fa-eye"></i> <?php echo $row['vistas']; ?> vistas
                            </p>
                            <a href="detalle.php?id=<?php echo $row['id']; ?>" class="btn-outline" style="font-size: 0.85rem; padding: 5px 0;">Ver Página</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>
</body>

</html>