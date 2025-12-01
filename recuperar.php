<?php
session_start();
include 'db.php';
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // 1. Verificar si el correo existe
    $query = mysqli_query($conn, "SELECT id, nombre FROM usuarios WHERE email = '$email'");
    
    if ($row = mysqli_fetch_assoc($query)) {
        // 2. Generar código de 6 dígitos
        $codigo = rand(100000, 999999);
        $expiracion = date("Y-m-d H:i:s", strtotime('+1 hour')); // Expira en 1 hora
        
        // 3. Guardar en BD
        $sql_update = "UPDATE usuarios SET token_recuperacion = '$codigo', token_expiracion = '$expiracion' WHERE email = '$email'";
        mysqli_query($conn, $sql_update);
        
        // 4. Enviar Correo
        $asunto = "Recuperar Password - Urban Canvas";
        $mensaje = "Hola " . $row['nombre'] . ",\n\nTu código de recuperación es: " . $codigo . "\n\nEste código expira en 1 hora.";
        $cabeceras = "From: no-reply@urbancanvas.com";

        // NOTA: Si estás en Localhost (XAMPP), mail() no funcionará sin configurar SMTP.
        // Para pruebas locales, descomenta la línea de abajo para ver el código en pantalla:
        // echo "<script>alert('MODO PRUEBA: Tu código es $codigo');</script>";

        if (mail($email, $asunto, $mensaje, $cabeceras)) {
            header("Location: restablecer.php?email=" . urlencode($email));
            exit;
        } else {
            // Si falla el envío (común en localhost), redirigimos igual para simular el proceso
            // En producción real, esto debería ser manejado con PHPMailer.
            header("Location: restablecer.php?email=" . urlencode($email));
            exit; 
        }
    } else {
        $error = "Ese correo no está registrado en nuestro sistema.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Recuperar Contraseña - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="login.php">Cancelar</a>
    </nav>
    <div class="auth-wrapper">
        <h2 style="text-align:center;">Recuperar Cuenta</h2>
        <p style="text-align:center; color:#64748b; margin-bottom:30px;">
            Ingresa tu correo y te enviaremos un código.
        </p>
        
        <?php if($error) echo "<div class='alert alert-error'>$error</div>"; ?>
        
        <form method="POST">
            <input type="email" name="email" class="input-field" placeholder="Tu Correo Electrónico" required>
            <button type="submit" class="btn-login" style="width:100%; margin-top:15px;">Enviar Código</button>
        </form>
    </div>
</body>
</html>