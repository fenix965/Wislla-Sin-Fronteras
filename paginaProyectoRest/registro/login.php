<?php
session_start();
include '../conexion.php';

$error_message = ''; 

$max_attempts = 2;
$lockout_duration = 5 * 60;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre']) && isset($_POST['password'])) {
        $nombre = $_POST['nombre'];
        $password = $_POST['password'];

        $conn = conexion();
        $query = "SELECT id, nombre_usuario, contrasena, rol, intentos_fallidos, ultimo_intento FROM usuarios WHERE nombre_usuario = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $db_nombre, $db_password, $rol, $intentos_fallidos, $ultimo_intento);
        $stmt->fetch();

        if ($stmt->num_rows > 0) {
            $current_time = time();

            if ($intentos_fallidos >= $max_attempts && ($current_time - $ultimo_intento) < $lockout_duration) {
                $error_message = "Cuenta suspendida durante 5 minutos. Inténtelo más tarde.";
            } else {
                if ($password == $db_password) {
                    $stmt_reset = $conn->prepare("UPDATE usuarios SET intentos_fallidos = 0 WHERE id = ?");
                    $stmt_reset->bind_param("i", $user_id);
                    $stmt_reset->execute();
                    $stmt_reset->close();

                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['nombre'] = $db_nombre;
                    $_SESSION['rol'] = $rol;
                    
                    if ($rol == 'cliente') {
                        $_SESSION['cliente_id'] = $user_id; // Ahora el carrito reconocerá a los clientes
                        header('Location: ../cliente/PaginaInfo.php');
                        exit();
                    }
                    
                    } elseif ($rol == 'administrador') {
                        header('Location: ../administrador/admin.php');
                        exit();
                    } else {
                        header('Location: ../empleado/indexE.php');  
                        exit();
                    }
                } else {
                    $intentos_fallidos++;
                    $remaining_attempts = $max_attempts - $intentos_fallidos;
                    $stmt_update = $conn->prepare("UPDATE usuarios SET intentos_fallidos = ?, ultimo_intento = ? WHERE id = ?");
                    $stmt_update->bind_param("iii", $intentos_fallidos, $current_time, $user_id);
                    $stmt_update->execute();
                    $stmt_update->close();

                    $error_message = "Contraseña incorrecta. Te quedan $remaining_attempts intentos.";
                }
            }
        } else {
            $error_message = "El nombre de usuario no está registrado.";
        }

        $stmt->close();
        $conn->close();
    } else {
        $error_message = "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleLogin.css">
    <title>Login</title>
</head>
<body>
<div class="login-container">
    <div style="display: flex; justify-content: center;">
        <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla" style="max-width: 180px; border-radius: 40%; object-fit: cover;">
    </div>

    <h2 class="login-title">Iniciar sesión</h2>

    <?php if (isset($_SESSION['registro_exitoso'])): ?>
        <div class="success-message">
            <?php echo $_SESSION['registro_exitoso']; ?>
        </div>
        <?php unset($_SESSION['registro_exitoso']); ?>  
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="nombre">Nombre de usuario:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="submit-btn">Iniciar sesión</button>
    </form>

    <div class="options-links">
        <a href="registroClientes.php" class="link">¿No tienes cuenta? Regístrate aquí</a>
        <br>
        <a href="../cliente/paginaInfo.php" class="link">Entrar como invitado</a>
        <br>
        <a href="cambiar_contraseña.php" class="link">¿Olvidaste tu contraseña? Cambia aquí</a>
    </div>
</div>
</body>
</html>
