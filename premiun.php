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
    <title>Planes Premium - Urban Canvas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css?v=<?php echo time(); ?>">
    <style>
        /* --- ESTILOS ESPECÍFICOS DE PRECIOS --- */
        .pricing-header {
            margin-bottom: 50px;
        }
        .pricing-header h1 {
            font-size: 3rem;
            margin-bottom: 10px;
            background: -webkit-linear-gradient(#1e293b, #334155);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 900px;
            margin: 0 auto;
            align-items: center; /* Centra verticalmente para que el premium resalte */
        }

        /* Tarjetas Base */
        .plan-card {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #f1f5f9;
            position: relative;
            overflow: hidden;
            text-align: left; /* Alineación interna a la izquierda se ve más limpia */
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }

        /* Títulos y Precios */
        .plan-card h3 {
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .price {
            font-size: 3.5rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 20px;
            line-height: 1;
        }
        .price small {
            font-size: 1rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Listas de beneficios */
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        .plan-features li {
            margin-bottom: 15px;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }
        .plan-features li i {
            font-size: 1.1rem;
        }
        .check { color: #10b981; } /* Verde */
        .cross { color: #cbd5e1; } /* Gris claro */

        /* Botones */
        .plan-btn {
            display: block;
            width: 100%;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
            border: none;
            cursor: pointer;
        }

        /* Estilo Plan Gratis */
        .btn-free {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Estilo Plan Premium (Destacado) */
        .plan-card.featured {
            border: 2px solid #0ea5e9;
            transform: scale(1.05);
            z-index: 2;
        }
        .plan-card.featured:hover {
            transform: scale(1.08);
        }
        
        .btn-premium {
            background: var(--primary-grad);
            color: white;
            box-shadow: 0 10px 25px -5px rgba(14, 165, 233, 0.4);
        }
        .btn-premium:hover {
            box-shadow: 0 15px 30px -5px rgba(14, 165, 233, 0.6);
            transform: translateY(-2px);
        }

        /* Etiqueta de Recomendado */
        .badge-recommended {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #eab308;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(234, 179, 8, 0.3);
        }

    </style>
</head>
<body style="background:#f8fafc;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Urban Canvas</div>
        <a href="index.php" style="text-decoration:none; color:#64748b;">← Volver al Inicio</a>
    </nav>
    
    <div class="container" style="margin-top:60px; margin-bottom: 60px;">
        
        <div class="pricing-header" style="text-align: center;">
            <h1>Invierte en tu Conocimiento</h1>
            <p style="color:#64748b; font-size: 1.1rem;">Elige el plan perfecto y desbloquea todo el potencial de la plataforma.</p>
        </div>
        
        <div class="pricing-grid">
            
            <div class="plan-card">
                <h3>Estudiante</h3>
                <div class="price">Gratis</div>
                <p style="color:#94a3b8; font-size:0.9rem;">Para empezar a explorar.</p>
                
                <ul class="plan-features">
                    <li><i class="fa-solid fa-check check"></i> Acceso a contenido libre</li>
                    <li><i class="fa-solid fa-check check"></i> Comentarios básicos</li>
                    <li><i class="fa-solid fa-triangle-exclamation" style="color:#eab308;"></i> Máximo 2 subidas al día</li>
                    <li style="opacity: 0.5;"><i class="fa-solid fa-xmark cross"></i> Contenido Premium bloqueado</li>
                    <li style="opacity: 0.5;"><i class="fa-solid fa-xmark cross"></i> Avatares exclusivos</li>
                </ul>
                
                <button class="plan-btn btn-free">Tu Plan Actual</button>
            </div>
            
            <div class="plan-card featured">
                <div class="badge-recommended">Recomendado</div>
                <h3 style="color:#0ea5e9;">Investigador PRO</h3>
                <div class="price">$5 <small>/único</small></div>
                <p style="color:#94a3b8; font-size:0.9rem;">Para creadores sin límites.</p>
                
                <ul class="plan-features">
                    <li><i class="fa-solid fa-check check"></i> <b>Subidas ILIMITADAS</b></li>
                    <li><i class="fa-solid fa-check check"></i> Acceso a <b>TODO</b> el contenido</li>
                    <li><i class="fa-solid fa-check check"></i> <b>10 Avatares</b> exclusivos</li>
                    <li><i class="fa-solid fa-check check"></i> Insignia Verificada en perfil</li>
                    <li><i class="fa-solid fa-check check"></i> Soporte prioritario</li>
                </ul>
                
                <?php if(isset($_SESSION['uid'])): ?>
                    <a href="premiun.php?comprar=1" class="plan-btn btn-premium">¡Obtener Premium Ahora!</a>
                <?php else: ?>
                    <a href="login.php" class="plan-btn btn-premium">Inicia Sesión para Comprar</a>
                <?php endif; ?>
            </div>
            
        </div>
        
        <div style="text-align:center; margin-top: 60px; color: #94a3b8; font-size: 0.9rem;">
            <p><i class="fa-solid fa-lock"></i> Pago seguro y único. Sin suscripciones mensuales ocultas.</p>
        </div>
    </div>
</body>
</html>