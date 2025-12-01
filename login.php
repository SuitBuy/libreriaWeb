<?php
session_start();
include 'db.php';
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $res = mysqli_query($conn, "SELECT * FROM usuarios WHERE email = '$email'");
    if ($row = mysqli_fetch_assoc($res)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['uid'] = $row['id'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['rol'] = $row['rol'];
            header("Location: index.php");
            exit;
        }
    }
    $error = "Credenciales incorrectas.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php">Volver</a>
    </nav>
    <div class="auth-wrapper">
        <h2 style="text-align:center;">Bienvenido</h2>
        <p style="text-align:center; color:#64748b; margin-bottom:30px;">Accede a la biblioteca colaborativa</p>
        
        <?php if($error) echo "<div class='alert alert-error'>$error</div>"; ?>
        <?php if(isset($_GET['success'])) echo "<div class='alert alert-success'>¡Cuenta creada! Inicia sesión.</div>"; ?>
        
        <form method="POST">
            <input type="email" name="email" class="input-field" placeholder="Correo electrónico" required>
            <input type="password" name="password" class="input-field" placeholder="Contraseña" required>
            <button type="submit" class="btn-login" style="width:100%;">Entrar</button>
        </form>
        <p style="text-align:center; margin-top:20px; font-size:0.9rem;">
            ¿Nuevo aquí? <a href="registro.php" style="color:#0ea5e9;">Regístrate gratis</a>
        </p>
    </div>
</body>
</html>