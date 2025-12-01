<?php
session_start();
include 'db.php';

$step = 1;
$error = "";
$pregunta_mostrar = "";
$email_valido = "";

// STEP 1: VERIFICAR EMAIL
if (isset($_POST['check_email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $res = mysqli_query($conn, "SELECT pregunta_seguridad FROM usuarios WHERE email = '$email'");
    if ($row = mysqli_fetch_assoc($res)) {
        if (!empty($row['pregunta_seguridad'])) {
            $step = 2;
            $email_valido = $email;
            
            // Convertir la clave de la pregunta en texto legible
            $preguntas_texto = [
                'animal' => '¿Cuál es el nombre de tu primera mascota?',
                'madre' => '¿Cuál es el segundo nombre de tu madre?',
                'ciudad' => '¿En qué ciudad naciste?',
                'comida' => '¿Cuál es tu comida favorita?',
                'escuela' => '¿Cómo se llamaba tu primera escuela?'
            ];
            // Si es una pregunta personalizada antigua o una clave, intentamos mostrarla bien
            $pregunta_mostrar = isset($preguntas_texto[$row['pregunta_seguridad']]) ? $preguntas_texto[$row['pregunta_seguridad']] : $row['pregunta_seguridad'];
        } else {
            $error = "Esta cuenta no tiene configurada una pregunta de seguridad.";
        }
    } else {
        $error = "Correo no encontrado.";
    }
}

// STEP 2: VERIFICAR RESPUESTA
if (isset($_POST['check_answer'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email_hidden']);
    $respuesta_input = strtolower(trim($_POST['respuesta']));
    
    $res = mysqli_query($conn, "SELECT respuesta_seguridad FROM usuarios WHERE email = '$email'");
    $row = mysqli_fetch_assoc($res);
    
    if (password_verify($respuesta_input, $row['respuesta_seguridad'])) {
        $step = 3;
        $email_valido = $email;
        $_SESSION['reset_authorized'] = $email; // Autorizar cambio
    } else {
        $error = "Respuesta incorrecta.";
        $step = 1; // Volver al inicio por seguridad
    }
}

// STEP 3: CAMBIAR PASSWORD
if (isset($_POST['change_pass'])) {
    if (isset($_SESSION['reset_authorized'])) {
        $email = $_SESSION['reset_authorized'];
        $pass = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];
        
        if ($pass === $confirm) {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE usuarios SET password = '$hash' WHERE email = '$email'");
            unset($_SESSION['reset_authorized']);
            header("Location: login.php?msg=restored");
            exit;
        } else {
            $error = "Las contraseñas no coinciden.";
            $step = 3;
        }
    } else {
        header("Location: recuperar.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recuperar - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="login.php">Cancelar</a>
    </nav>
    <div class="auth-wrapper">
        <h2 style="text-align:center;">Recuperar Acceso</h2>
        
        <?php if($error) echo "<div class='alert alert-error'>$error</div>"; ?>

        <?php if ($step == 1): ?>
            <p style="text-align:center; color:#64748b;">Ingresa tu correo para ver tu pregunta de seguridad.</p>
            <form method="POST">
                <input type="email" name="email" class="input-field" placeholder="Tu correo electrónico" required>
                <button type="submit" name="check_email" class="btn-login" style="width:100%;">Continuar</button>
            </form>
        
        <?php elseif ($step == 2): ?>
            <p style="text-align:center; color:#64748b;">Responde a tu pregunta de seguridad:</p>
            <div style="background:#eff6ff; padding:15px; border-radius:10px; border:1px solid #bfdbfe; color:#1e40af; text-align:center; margin-bottom:20px; font-weight:bold;">
                <i class="fa-solid fa-circle-question"></i> <?php echo $pregunta_mostrar; ?>
            </div>
            <form method="POST">
                <input type="hidden" name="email_hidden" value="<?php echo $email_valido; ?>">
                <input type="text" name="respuesta" class="input-field" placeholder="Tu respuesta..." required autocomplete="off">
                <button type="submit" name="check_answer" class="btn-login" style="width:100%;">Verificar Respuesta</button>
            </form>

        <?php elseif ($step == 3): ?>
            <div class="alert alert-success">¡Identidad verificada!</div>
            <form method="POST">
                <input type="password" name="new_password" class="input-field" placeholder="Nueva contraseña" required>
                <input type="password" name="confirm_password" class="input-field" placeholder="Confirma nueva contraseña" required>
                <button type="submit" name="change_pass" class="btn-login" style="width:100%;">Guardar Cambios</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>