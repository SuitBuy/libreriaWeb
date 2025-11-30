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
    else $msg = "El correo ya está registrado.";
}
?>
<!DOCTYPE html>
<html>
<head><title>Registro</title><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>BiblioShare</div>
        <a href="index.php" style="text-decoration:none; color:var(--dark);">Volver</a>
    </nav>
    <div class="auth-container">
        <h2 style="text-align:center; margin-bottom:30px;">Crear Cuenta</h2>
        <?php if($msg) echo "<div class='alert error'>$msg</div>"; ?>
        <form method="POST">
            <input type="text" name="nombre" class="form-input" placeholder="Nombre completo" required>
            <input type="email" name="email" class="form-input" placeholder="Correo electrónico" required>
            <input type="password" name="password" class="form-input" placeholder="Crea una contraseña" required>
            <p style="font-size:0.9rem; margin-bottom:5px; font-weight:600;">¿Qué perfil deseas?</p>
            <select name="rol" class="form-input">
                <option value="estudiante">Estudiante (Leer y descargar)</option>
                <option value="autor">Autor (Publicar contenido)</option>
            </select>
            <button type="submit" class="btn-primary">Registrarse Gratis</button>
        </form>
    </div>
</body>
</html>