<?php
// db.php

// 1. Intentamos cargar variables de entorno (Para cuando subas la web a Railway)
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$dbname = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// 2. Si NO hay variables de entorno, asumimos que estás en LOCAL (XAMPP)
// Aquí he puesto TUS datos exactos sacados de la imagen (MYSQL_PUBLIC_URL)
if (!$host) {
    $host = 'yamanote.proxy.rlwy.net';      // El host público de tu imagen
    $port = 18736;                          // El puerto público (¡No es 3306!)
    $user = 'root';                         // Tu usuario
    $password = 'kBolhkcFtJVtXcffzhwfhrXFtrKgDLgK'; // Tu contraseña
    $dbname = 'biblioshare_db';                    // El nombre real de la BD en Railway
}

// 3. Crear la conexión
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// 4. Verificar errores
if (!$conn) {
    die("Error de conexión fatal: " . mysqli_connect_error());
}

// 5. Configurar caracteres
mysqli_set_charset($conn, "utf8");
?>