<?php
include 'db.php';
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES ('$nombre', '$email', '$pass', '$rol')";
    if (mysqli_query($conn, $sql)) header("Location: login.php?success=1");
    else $msg = "El correo ya existe.";
}
?>
<!DOCTYPE html>
<html>
<head><title>Registro</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><link rel="stylesheet" href="estilos.css"></head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>BiblioShare</div>
        <a href="index.php" style="text-decoration:none; color:#334155;">Volver</a>
    </nav>
    <div class="auth-wrapper">
        <h2 style="text-align:center; margin-bottom:30px;">Crear Cuenta</h2>
        <?php if($msg) echo "<div class='alert alert-error'>$msg</div>"; ?>
        <form method="POST">
            <input type="text" name="nombre" class="input-field" placeholder="Nombre completo" required style="margin-bottom:15px;">
            <input type="email" name="email" class="input-field" placeholder="Correo electrónico" required style="margin-bottom:15px;">
            <input type="password" name="password" class="input-field" placeholder="Contraseña segura" required style="margin-bottom:15px;">
            <p style="font-size:0.9rem; margin-bottom:5px; font-weight:600; color:#64748b;">Selecciona tu perfil:</p>
            <select name="rol" class="input-field" style="margin-bottom:20px;">
                <option value="estudiante">Estudiante</option>
                <option value="autor">Autor / Investigador</option>
            </select>
            <button type="submit" class="btn-login" style="width:100%; border:none; cursor:pointer; background:#0ea5e9;">Registrarse</button>
        </form>
    </div>
</body>
</html>