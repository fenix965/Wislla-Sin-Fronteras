<?php
session_start();
include '../conexion.php';

$error_message = ''; 
$success_message = ''; 

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $conn = conexion();

    $query = "SELECT id, nombre_usuario, token_expiracion FROM usuarios WHERE token_recuperacion = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $db_nombre, $token_expiracion);
    $stmt->fetch();

    if ($stmt->num_rows > 0) {
        if (time() > $token_expiracion) {
            $error_message = "El enlace de recuperación ha expirado.";
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['nueva_contrasena'])) {
                    $nueva_contrasena = $_POST['nueva_contrasena'];

                    $stmt_update = $conn->prepare("UPDATE usuarios SET contrasena = ?, token_recuperacion = NULL, token_expiracion = NULL WHERE id = ?");
                    $stmt_update->bind_param("si", $nueva_contrasena, $user_id);
                    $stmt_update->execute();
                    $stmt_update->close();

                    $success_message = "Contraseña restablecida con éxito. Ahora puedes iniciar sesión.";
                } else {
                    $error_message = "Por favor, ingresa una nueva contraseña.";
                }
            }
        }
    } else {
        $error_message = "El enlace de recuperación es inválido.";
    }

    $stmt->close();
    $conn->close();
} else {
    header('Location: login.php'); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleLogin.css">
    <title>Restablecer Contraseña</title>
</head>
<body>
<div class="login-container">
    <h2 class="login-title">Restablecer Contraseña</h2>

    <?php if(isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if(isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="nueva_contrasena">Nueva contraseña:</label>
            <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
        </div>

        <button type="submit" class="submit-btn">Restablecer contraseña</button>
    </form>

    <div class="options-links">
        <a href="login.php" class="link">Volver al inicio de sesión</a>
    </div>
</div>
</body>
</html>
