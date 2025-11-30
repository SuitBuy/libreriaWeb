<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head><title>Premium</title><link rel="stylesheet" href="estilos.css"></head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <a href="index.php" style="color:white;">Volver</a>
    </nav>
    <div class="container" style="text-align:center;">
        <h1>Apoya el Conocimiento Libre</h1>
        <div class="pricing-grid">
            <div class="plan-card">
                <h3>Estudiante</h3>
                <div class="price">Gratis</div>
                <p>Acceso a lectura y descargas limitadas.</p>
                <button style="background:#666;">Tu Plan</button>
            </div>
            <div class="plan-card featured">
                <h3 style="color:var(--primary);">Investigador PRO</h3>
                <div class="price">S/ 15 <small>/mes</small></div>
                <p>Descargas ilimitadas, perfil destacado y soporte.</p>
                <button>Suscribirse</button>
            </div>
            <div class="plan-card">
                <h3>Donación</h3>
                <div class="price">Voluntario</div>
                <p>Ayúdanos a mantener los servidores.</p>
                <button style="background:var(--success);">Donar</button>
            </div>
        </div>
    </div>
</body>
</html>