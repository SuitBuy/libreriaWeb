<?php
session_start();
include 'db.php';

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION['uid'];

// --- VERIFICAR PLAN (GRATIS/PREMIUM) ---
$user_query = mysqli_query($conn, "SELECT plan, rol FROM usuarios WHERE id = $uid");
$user = mysqli_fetch_assoc($user_query);
$es_premium = ($user['plan'] == 'premium');
$es_admin = ($user['rol'] == 'admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Límite diario para usuarios GRATIS (2 subidas)
    if (!$es_premium && !$es_admin) {
        $hoy = date('Y-m-d');
        $check_limit = mysqli_query($conn, "SELECT COUNT(*) as total FROM recursos WHERE usuario_id = $uid AND DATE(fecha_subida) = '$hoy'");
        $count = mysqli_fetch_assoc($check_limit);
        if ($count['total'] >= 2) {
            $error = "⛔ Límite diario alcanzado. Hazte Premium para subir más.";
        }
    }

    if (!isset($error)) {
        $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
        $autor = mysqli_real_escape_string($conn, $_POST['autor']);
        $cat = mysqli_real_escape_string($conn, $_POST['categoria']);
        $desc = mysqli_real_escape_string($conn, $_POST['desc']);

        // Checkbox de Premium (Solo Admin)
        $es_contenido_premium = 0;
        if ($es_admin && isset($_POST['es_premium'])) {
            $es_contenido_premium = 1;
        }

        // --- GESTIÓN DE ARCHIVO EN VOLUMEN ---
        $nombreOriginal = $_FILES["archivo"]["name"];
        $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $nombreFinal = time() . "_" . preg_replace("/[^a-zA-Z0-9\.]/", "", $nombreOriginal);
        $rutaDestino = "uploads/" . $nombreFinal;

        if (!file_exists("uploads")) {
            mkdir("uploads", 0777, true);
        }
        $permitidos = array("pdf", "doc", "docx", "jpg", "jpeg", "png");

        if (in_array($ext, $permitidos)) {
            if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $rutaDestino)) {
                $sql = "INSERT INTO recursos (titulo, autor_nombre, categoria, descripcion, archivo_pdf, usuario_id, estado, tipo_archivo, es_premium, fecha_subida) 
                        VALUES ('$titulo', '$autor', '$cat', '$desc', '$rutaDestino', $uid, 'pendiente', '$ext', $es_contenido_premium, NOW())";

                if (mysqli_query($conn, $sql)) {
                    header("Location: index.php?msg=uploaded");
                    exit;
                } else {
                    $error = "Error BD: " . mysqli_error($conn);
                }
            } else {
                $error = "Error al guardar en el volumen.";
            }
        } else {
            $error = "Formato no permitido.";
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Subir - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>

<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo">
            <div class="logo-icon"></div>Urban Canvas
        </div>
        <a href="index.php">Cancelar</a>
    </nav>

    <div class="auth-wrapper">
        <h2>Subir Aporte</h2>

        <?php if ($es_premium): ?>
            <p style="color:#10b981; text-align:center; margin-bottom:20px; font-weight:600;">
                <i class="fa-solid fa-star"></i> Eres Premium: Subidas Ilimitadas
            </p>
        <?php endif; ?>

        <?php if (isset($error)) echo "<div class='alert alert-error'>$error</div>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <div style="margin-bottom: 15px;">
                <input type="text" name="titulo" class="input-field" placeholder="Título de la obra" required>
            </div>

            <div style="margin-bottom: 15px;">
                <input type="text" name="autor" class="input-field" placeholder="Autor original" required>
            </div>

            <div style="display:grid; grid-template-columns: 1fr <?php echo $es_admin ? '1fr' : ''; ?>; gap:15px; margin-bottom: 15px;">
                <select name="categoria" class="input-field">
                    <option>Ciencias</option>
                    <option>Arte</option>
                    <option>Historia</option>
                    <option>Ingeniería</option>
                    <option>Otros</option>
                </select>

                <?php if ($es_admin): ?>
                    <!-- Checkbox Premium Estilizado -->
                    <div class="premium-option">
                        <input type="checkbox" name="es_premium" id="prem">
                        <label for="prem">Premium ⭐</label>
                    </div>
                <?php endif; ?>
            </div>

            <textarea name="desc" class="input-field" placeholder="Añade una descripción..." rows="4"></textarea>

            <!-- Área de Archivo Estilizada -->
            <div class="file-upload-area">
                <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 10px;"></i>
                <p style="margin: 0 0 10px 0; color: #64748b; font-size: 0.9rem;">Arrastra o selecciona tu archivo</p>
                <input type="file" name="archivo" required style="display: inline-block; margin: 0 auto;">
            </div>

            <button type="submit" class="btn-submit">Publicar Aporte</button>
        </form>
    </div>
</body>

</html>