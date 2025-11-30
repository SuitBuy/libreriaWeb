<?php session_start(); ?>
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
        <a href="index.php" style="text-decoration:none; color:#64748b;">← Volver</a>
    </nav>
    <div class="container" style="text-align:center; margin-top:40px;">
        <h1 style="font-size:2.5rem; margin-bottom:10px;">Apoya el Conocimiento Libre</h1>
        <p style="color:#64748b;">Elige el plan perfecto para ti</p>
        
        <div class="pricing-grid">
            <div class="plan-card">
                <h3>Estudiante</h3>
                <div class="price">Gratis</div>
                <p>Acceso a lectura y descargas limitadas.</p>
                <button style="background:#cbd5e1; color:#475569;">Tu Plan Actual</button>
            </div>
            
            <div class="plan-card featured">
                <h3 style="color:#0ea5e9;">Investigador PRO</h3>
                <div class="price">S/ 15 <small>/mes</small></div>
                <p>Descargas ilimitadas, perfil destacado y soporte.</p>
                <button style="background:#0ea5e9;">Suscribirse Ahora</button>
            </div>
            
            <div class="plan-card">
                <h3>Donación</h3>
                <div class="price">Voluntario</div>
                <p>Ayúdanos a mantener los servidores online.</p>
                <button style="background:#84cc16;">Donar</button>
            </div>
        </div>
    </div>
</body>
</html>