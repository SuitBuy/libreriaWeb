<?php 
session_start(); 
include 'db.php';

// LÓGICA DE COMPRA (SIMULADA)
if (isset($_GET['comprar']) && isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
    // Actualizamos al usuario a Premium
    mysqli_query($conn, "UPDATE usuarios SET plan = 'premium' WHERE id = $uid");
    header("Location: perfil.php?msg=premium_activated");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Planes Premium</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php">← Volver</a>
    </nav>
    <div class="container" style="text-align:center; margin-top:40px;">
        <h1 style="font-size:2.5rem;">Elige tu Nivel</h1>
        <p style="color:#64748b;">Desbloquea todo el potencial de la biblioteca.</p>
        
        <div class="pricing-grid">
            <div class="plan-card">
                <h3>Estudiante</h3>
                <div class="price">Gratis</div>
                <ul style="list-style:none; padding:0; color:#64748b; margin:20px 0;">
                    <li>Acceso a contenido libre</li>
                    <li>Máximo 2 subidas al día</li>
                    <li>Contenido Premium bloqueado</li>
                    <li>Avatares básicos</li>
                </ul>
                <button style="background:#cbd5e1; color:#475569; cursor:default;">Tu Plan Actual</button>
            </div>
            
            <div class="plan-card featured">
                <h3 style="color:#0ea5e9;">Investigador PRO</h3>
                <div class="price">$5 <small>/único</small></div>
                <ul style="list-style:none; padding:0; color:#64748b; margin:20px 0;">
                    <li>🔥 <b>Subidas ILIMITADAS</b></li>
                    <li>🔓 Acceso a <b>TODO</b> el contenido</li>
                    <li>🎨 <b>10 Avatares</b> exclusivos</li>
                    <li>⭐ Insignia en tu perfil</li>
                </ul>
                <?php if(isset($_SESSION['uid'])): ?>
                    <a href="premiun.php?comprar=1" class="btn-login" style="display:block; background:#0ea5e9;">¡Mejorar Ahora!</a>
                <?php else: ?>
                    <a href="login.php" class="btn-login">Inicia Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>