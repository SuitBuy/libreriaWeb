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
    $error = "Datos incorrectos";
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="estilos.css"></head>
<body>
    <div class="auth-box">
        <h2>Iniciar Sesión</h2>
        <?php if(isset($_GET['success'])) echo "<div class='alert success'>Registro exitoso.</div>"; ?>
        <?php if($error) echo "<div class='alert error'>$error</div>"; ?>
        <form method="POST">
            <label>Email</label><input type="email" name="email" required>
            <label>Contraseña</label><input type="password" name="password" required>
            <button type="submit">Ingresar</button>
        </form>
        <p style="text-align:center;"><a href="registro.php">Crear cuenta</a> | <a href="index.php">Ir al Inicio</a></p>
    </div>
</body>
</html>