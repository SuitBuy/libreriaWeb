<?php
session_start();
include 'db.php';
$error = "";
$email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_post = mysqli_real_escape_string($conn, $_POST['email']);
    $codigo = mysqli_real_escape_string($conn, $_POST['codigo']);
    $new_pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // 1. Verificar Código y Expiración
        $ahora = date("Y-m-d H:i:s");
        $sql = "SELECT id FROM usuarios WHERE email = '$email_post' AND token_recuperacion = '$codigo' AND token_expiracion > '$ahora'";
        $query = mysqli_query($conn, $sql);

        if (mysqli_num_rows($query) > 0) {
            // 2. Actualizar contraseña y borrar token
            $hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $sql_update = "UPDATE usuarios SET password = '$hash', token_recuperacion = NULL, token_expiracion = NULL WHERE email = '$email_post'";
            
            if (mysqli_query($conn, $sql_update)) {
                header("Location: login.php?msg=restored");
                exit;
            } else {
                $error = "Error al actualizar la base de datos.";
            }
        } else {
            $error = "Código incorrecto o expirado.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nueva Contraseña - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
    </nav>
    <div class="auth-wrapper">
        <h2 style="text-align:center;">Cambiar Contraseña</h2>
        <p style="text-align:center; color:#64748b; margin-bottom:20px;">
            Revisa tu correo para ver el código.
        </p>
        
        <?php if($error) echo "<div class='alert alert-error'>$error</div>"; ?>
        
        <form method="POST">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            
            <label style="font-weight:bold; font-size:0.9rem;">Código de Verificación:</label>
            <input type="text" name="codigo" class="input-field" placeholder="Ej: 123456" required style="letter-spacing: 5px; text-align:center; font-size:1.2rem;">

            <label style="font-weight:bold; font-size:0.9rem; margin-top:15px; display:block;">Nueva Contraseña:</label>
            <input type="password" name="password" class="input-field" placeholder="Mínimo 6 caracteres" required>

            <label style="font-weight:bold; font-size:0.9rem; margin-top:15px; display:block;">Confirmar Contraseña:</label>
            <input type="password" name="confirm_password" class="input-field" placeholder="Repite la contraseña" required>

            <button type="submit" class="btn-login" style="width:100%; margin-top:20px;">Actualizar Password</button>
        </form>
    </div>
</body>
</html>