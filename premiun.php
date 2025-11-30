<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Premium - BiblioShare</title>
    <link rel="stylesheet" href="estilos.css">
    <style>
        .pricing-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .plan-card { background: white; padding: 30px; border-radius: 8px; text-align: center; border: 1px solid #eee; }
        .plan-card.featured { border: 2px solid var(--primary); transform: scale(1.05); }
        .price { font-size: 2rem; font-weight: bold; margin: 20px 0; }
        .features { list-style: none; padding: 0; text-align: left; margin-bottom: 30px; }
        .features li { padding: 10px 0; border-bottom: 1px solid #f4f4f4; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">Biblio<span>Share</span></div>
        <div class="nav-links"><a href="index.php">Volver</a></div>
    </nav>

    <div class="container" style="text-align:center;">
        <h1>Apoya el Conocimiento Libre</h1>
        <p>Elige un plan para obtener más beneficios o realiza una donación voluntaria.</p>

        <div class="pricing-grid">
            <div class="plan-card">
                <h3>Estudiante</h3>
                <div class="price">S/ 0</div>
                <ul class="features">
                    <li>✅ Acceso a lecturas</li>
                    <li>✅ Descargas limitadas (5/día)</li>
                    <li>✅ Comentar y calificar</li>
                    <li>❌ Estadísticas avanzadas</li>
                </ul>
                <button style="background:#666;">Tu Plan Actual</button>
            </div>

            <div class="plan-card featured">
                <h3 style="color:var(--primary);">Investigador PRO</h3>
                <div class="price">S/ 15 <span style="font-size:0.9rem">/mes</span></div>
                <ul class="features">
                    <li>✅ <strong>Descargas ilimitadas</strong></li>
                    <li>✅ Publicación destacada</li>
                    <li>✅ <strong>Insignia de verificado</strong></li>
                    <li>✅ Soporte prioritario</li>
                </ul>
                <button>Suscribirse</button>
            </div>

            <div class="plan-card">
                <h3>Mecenas</h3>
                <div class="price">Voluntario</div>
                <p>Ayúdanos a mantener los servidores y el acceso libre para todos.</p>
                <form>
                    <input type="number" placeholder="Monto (S/)" style="margin-bottom:10px;">
                    <button style="background:var(--success);">Donar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>