<?php
include 'db.php';
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];

    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES ('$nombre', '$email', '$pass', '$rol')";
    if (mysqli_query($conn, $sql)) { header("Location: login.php?success=1"); } 
    else { $msg = "El correo ya existe."; }
}
?>
<!DOCTYPE html>
<html>
<head><title>Registro</title><link rel="stylesheet" href="estilos.css"></head>
<body>
    <div class="auth-box">
        <h2>Crear Cuenta</h2>
        <?php if($msg) echo "<div class='alert error'>$msg</div>"; ?>
        <form method="POST">
            <label>Nombre Completo</label><input type="text" name="nombre" required>
            <label>Email</label><input type="email" name="email" required>
            <label>Contraseña</label><input type="password" name="password" required>
            <label>Soy:</label>
            <select name="rol">
                <option value="estudiante">Estudiante / Lector</option>
                <option value="autor">Autor / Investigador</option>
            </select>
            <button type="submit">Registrarse</button>
        </form>
        <p style="text-align:center;"><a href="login.php">¿Ya tienes cuenta?</a></p>
    </div>
</body>
</html> 