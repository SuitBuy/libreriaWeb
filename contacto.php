<?php
include 'db.php';
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mensaje = mysqli_real_escape_string($conn, $_POST['mensaje']);
    
    // Guardar mensaje en BD (si creaste la tabla) o simplemente simular envío
    $sql = "INSERT INTO mensajes_contacto (nombre, email, mensaje) VALUES ('$nombre', '$email', '$mensaje')";
    // Si la tabla no existe, no romperá la página, solo no guardará
    @mysqli_query($conn, $sql); 
    
    $msg = "¡Gracias! Hemos recibido tu mensaje.";
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <div class="nav-links"><a href="index.php">Volver</a></div>
    </nav>
    <div class="auth-box">
        <h2>Contáctanos</h2>
        <p style="text-align:center; color:#666; font-size:0.9rem;">Resuelve tus dudas o reporta problemas.</p>
        <?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>
        <form method="POST">
            <label>Nombre</label><input type="text" name="nombre" required>
            <label>Correo</label><input type="email" name="email" required>
            <label>Mensaje</label><textarea name="mensaje" rows="4" required></textarea>
            <button type="submit">Enviar Mensaje</button>
        </form>
    </div>
</body>
</html>