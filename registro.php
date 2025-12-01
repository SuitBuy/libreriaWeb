<?php
include 'db.php'; //
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = $_POST['rol'];
    
    // N uevos campos de seguridad
    $pregunta = mysqli_real_escape_string($conn, $_POST['pregunta']);
    // Encriptamos la respuesta para que nadie la pueda leer en la BD
    $respuesta = password_hash(strtolower(trim($_POST['respuesta'])), PASSWORD_DEFAULT);

    if ($rol != 'estudiante' && $rol != 'autor') {
        $rol = 'estudiante'; 
    }

    $sql = "INSERT INTO usuarios (nombre, email, password, rol, pregunta_seguridad, respuesta_seguridad) 
            VALUES ('$nombre', '$email', '$pass', '$rol', '$pregunta', '$respuesta')";
            
    if (@mysqli_query($conn, $sql)) header("Location: login.php?success=1");
    else $msg = "El correo ya está registrado.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registro - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php">Volver</a>
    </nav>
    <div class="auth-wrapper">
        <h2 style="text-align:center; margin-bottom:20px;">Crear Cuenta</h2>
        <?php if($msg) echo "<div class='alert alert-error'>$msg</div>"; ?>
        
        <form method="POST">
            <input type="text" name="nombre" class="input-field" placeholder="Nombre completo" required>
            <input type="email" name="email" class="input-field" placeholder="Correo electrónico" required>
            <input type="password" name="password" class="input-field" placeholder="Contraseña segura" required>
            
            <p style="font-size:0.9rem; margin-bottom:5px; margin-top:15px; color:#64748b; font-weight:bold;">Seguridad de la cuenta:</p>
            <select name="pregunta" class="input-field" required>
                <option value="" disabled selected>Selecciona una pregunta de seguridad...</option>
                <option value="animal">¿Cuál es el nombre de tu primera mascota?</option>
                <option value="madre">¿Cuál es el segundo nombre de tu madre?</option>
                <option value="ciudad">¿En qué ciudad naciste?</option>
                <option value="comida">¿Cuál es tu comida favorita?</option>
                <option value="escuela">¿Cómo se llamaba tu primera escuela?</option>
            </select>
            <input type="text" name="respuesta" class="input-field" placeholder="Tu respuesta secreta" required>

            <p style="font-size:0.9rem; margin-bottom:5px; margin-top:15px; color:#64748b;">Selecciona tu perfil:</p>
            <select name="rol" class="input-field">
                <option value="estudiante">Estudiante</option>
                <option value="autor">Autor / Investigador</option>
            </select>
            <button type="submit" class="btn-login" style="width:100%;">Registrarse</button>
        </form>
        <p style="text-align:center; margin-top:20px; font-size:0.9rem;">
            ¿Ya tienes cuenta? <a href="login.php" style="color:#0ea5e9;">Inicia sesión</a>
        </p>
    </div>
</body>
</html>