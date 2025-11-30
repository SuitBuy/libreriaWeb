<?php
session_start();
include 'db.php';

// SEGURIDAD: Solo admin
if (!isset($_SESSION['uid']) || $_SESSION['rol'] != 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) { header("Location: admin_panel.php"); exit; }
$id = (int)$_GET['id'];

// LÓGICA DE ACTUALIZACIÓN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = mysqli_real_escape_string($conn, $_POST['titulo']);
    $autor = mysqli_real_escape_string($conn, $_POST['autor']);
    $categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
    $descripcion = mysqli_real_escape_string($conn, $_POST['descripcion']);
    $estado = mysqli_real_escape_string($conn, $_POST['estado']);

    $sql = "UPDATE recursos SET titulo='$titulo', autor_nombre='$autor', categoria='$categoria', descripcion='$descripcion', estado='$estado' WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: admin_panel.php?msg=updated");
        exit;
    } else {
        $error = "Error al actualizar.";
    }
}

// Obtener datos actuales
$row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM recursos WHERE id=$id"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Editar Recurso - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="estilos.css">
</head>
<body style="background:#f1f5f9;">
    <nav class="navbar">
        <div class="logo"><div class="logo-icon"></div>Editar Recurso</div>
        <a href="admin_panel.php">Cancelar y Volver</a>
    </nav>

    <div class="auth-wrapper" style="max-width:600px; margin-top:40px;">
        <h2 style="margin-bottom:20px;">Editando: <?php echo $row['titulo']; ?></h2>
        
        <form method="POST">
            <label style="font-weight:bold; font-size:0.9rem;">Título:</label>
            <input type="text" name="titulo" class="input-field" value="<?php echo $row['titulo']; ?>" required>

            <label style="font-weight:bold; font-size:0.9rem;">Autor:</label>
            <input type="text" name="autor" class="input-field" value="<?php echo $row['autor_nombre']; ?>" required>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div>
                    <label style="font-weight:bold; font-size:0.9rem;">Categoría:</label>
                    <select name="categoria" class="input-field">
                        <option value="<?php echo $row['categoria']; ?>" selected hidden><?php echo $row['categoria']; ?></option>
                        <option>Ciencias</option><option>Arte</option><option>Historia</option><option>Ingeniería</option><option>Otros</option>
                    </select>
                </div>
                <div>
                    <label style="font-weight:bold; font-size:0.9rem;">Estado:</label>
                    <select name="estado" class="input-field">
                        <option value="aprobado" <?php if($row['estado']=='aprobado') echo 'selected'; ?>>Aprobado</option>
                        <option value="pendiente" <?php if($row['estado']=='pendiente') echo 'selected'; ?>>Pendiente</option>
                        <option value="rechazado" <?php if($row['estado']=='rechazado') echo 'selected'; ?>>Rechazado</option>
                    </select>
                </div>
            </div>

            <label style="font-weight:bold; font-size:0.9rem;">Descripción:</label>
            <textarea name="descripcion" class="input-field" rows="5"><?php echo $row['descripcion']; ?></textarea>

            <button type="submit" class="btn-login" style="width:100%;">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>