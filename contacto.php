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
<head>
    <title>Contacto</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php" style="text-decoration:none; color:#64748b;">← Volver</a>
    </nav>
    
    <div class="auth-wrapper" style="max-width:500px;">
        <h2 style="text-align:center; margin-bottom:10px;">Contáctanos</h2>
        <p style="text-align:center; color:#64748b; margin-bottom:30px;">¿Tienes alguna duda o sugerencia?</p>
        
        <?php if($msg) echo "<div class='alert alert-success'>$msg</div>"; ?>
        
        <form method="POST">
            <div class="input-group">
                <input type="text" name="nombre" class="input-field" placeholder="Tu Nombre" required>
            </div>
            <div class="input-group">
                <input type="email" name="email" class="input-field" placeholder="Tu Correo Electrónico" required>
            </div>
            <div class="input-group">
                <textarea name="mensaje" class="input-field" placeholder="Escribe tu mensaje aquí..." required rows="5"></textarea>
            </div>
            <button type="submit" class="btn-login" style="width:100%;">Enviar Mensaje</button>
        </form>
    </div>
</body>
</html>