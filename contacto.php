<?php
include 'db.php';
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mensaje = mysqli_real_escape_string($conn, $_POST['mensaje']);
    $sql = "INSERT INTO mensajes_contacto (nombre, email, mensaje) VALUES ('$nombre', '$email', '$mensaje')";
    @mysqli_query($conn, $sql); 
    $msg = "Mensaje enviado con éxito.";
}   
?>
<!DOCTYPE html>
<html>
<head><title>Contacto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <a href="index.php" style="color:white;">Volver</a>
    </nav>
    <div class="auth-box">
        <h2>Contáctanos</h2>
        <?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>
        <form method="POST">
            <label>Nombre</label><input type="text" name="nombre" required>
            <label>Correo</label><input type="email" name="email" required>
            <label>Mensaje</label><textarea name="mensaje" required rows="4"></textarea>
            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>