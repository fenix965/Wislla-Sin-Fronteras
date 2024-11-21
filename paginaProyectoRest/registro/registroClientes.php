<?php
include '../conexion.php';
session_start();

$usuario_existe = false;
$email_existe = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'];
    $contrasena = $_POST['contrasena'];
    $nombre_cliente = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $conn = conexion();

    $sql_usuario = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
    $stmt_usuario = mysqli_prepare($conn, $sql_usuario);
    mysqli_stmt_bind_param($stmt_usuario, "s", $nombre_usuario);
    mysqli_stmt_execute($stmt_usuario);
    $resultado_usuario = mysqli_stmt_get_result($stmt_usuario);
    if (mysqli_num_rows($resultado_usuario) > 0) {
        $usuario_existe = true;
    }

    $sql_email = "SELECT * FROM clientes WHERE email = ?";
    $stmt_email = mysqli_prepare($conn, $sql_email);
    mysqli_stmt_bind_param($stmt_email, "s", $email);
    mysqli_stmt_execute($stmt_email);
    $resultado_email = mysqli_stmt_get_result($stmt_email);
    if (mysqli_num_rows($resultado_email) > 0) {
        $email_existe = true;
    }

    if (!$usuario_existe && !$email_existe) {
        $sql_usuario_insert = "INSERT INTO usuarios (nombre_usuario, contrasena, rol, intentos_fallidos, ultimo_intento) 
                       VALUES (?, ?, ?, ?, ?)";
        $rol = 'cliente';
        $intentos_fallidos = '0';  // Valor predeterminado
        $ultimo_intento = '0000-00-00 00:00:00';  // Valor predeterminado
        $stmt_usuario_insert = mysqli_prepare($conn, $sql_usuario_insert);

        if ($stmt_usuario_insert === false) {
            echo "<p>Error en la preparación de la consulta para insertar usuario: " . mysqli_error($conn) . "</p>";
        } else {
            mysqli_stmt_bind_param($stmt_usuario_insert, "sssss", $nombre_usuario, $contrasena, $rol, $intentos_fallidos, $ultimo_intento);
            
            if (mysqli_stmt_execute($stmt_usuario_insert)) {
                $usuario_id = mysqli_insert_id($conn);
                
                $sql_cliente = "INSERT INTO clientes (usuario_id, nombre, apellidos, telefono, email) VALUES (?, ?, ?, ?, ?)";
                $stmt_cliente = mysqli_prepare($conn, $sql_cliente);
            
                if ($stmt_cliente === false) {
                    echo "<p>Error en la preparación de la consulta para insertar cliente: " . mysqli_error($conn) . "</p>";
                } else {
                    mysqli_stmt_bind_param($stmt_cliente, "issss", $usuario_id, $nombre_cliente, $apellidos, $telefono, $email);
            
                    if (mysqli_stmt_execute($stmt_cliente)) {
                        $_SESSION['nombre_usuario'] = $nombre_usuario;
                        $_SESSION['usuario_id'] = $usuario_id;

                        // Establecer mensaje de éxito en la sesión
                        $_SESSION['registro_exitoso'] = "¡Registro exitoso! Ahora puedes iniciar sesión.";

                        header('Location: ../registro/login.php');
                        exit();
                    } else {
                        echo "<p>Error al registrar cliente: " . mysqli_stmt_error($stmt_cliente) . "</p>";
                    }
                    mysqli_stmt_close($stmt_cliente);
                }
            } else {
                echo "<p>Error al registrar usuario: " . mysqli_stmt_error($stmt_usuario_insert) . "</p>";
            }
            mysqli_stmt_close($stmt_usuario_insert);
        }

    } else {
        if ($usuario_existe) {
            echo "<p class='error'>El nombre de usuario ya está en uso.</p>";
        }
        if ($email_existe) {
            echo "<p class='error'>El correo electrónico ya está en uso.</p>";
        }
    }

    mysqli_stmt_close($stmt_usuario);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleLogin.css">
    <title>Registro de Clientes</title>
    <script>
        function validarFormulario() {
            var telefono = document.getElementById('telefono').value;
            var contrasena = document.getElementById('contrasena').value;
            var nombreUsuario = document.getElementById('nombre_usuario').value;
            var mensaje = '';

            if (telefono.length != 8) {
                mensaje += "El teléfono debe tener exactamente 8 dígitos.<br>";
            }

            if (contrasena.length < 8) {
                mensaje += "La contraseña debe tener al menos 8 caracteres.<br>";
            }

            if (nombreUsuario.length < 4) {
                mensaje += "El nombre de usuario debe tener al menos 4 caracteres.<br>";
            }

            if (mensaje) {
                document.getElementById('client-errors').innerHTML = mensaje;
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="login-container">
    <h2 class="login-title">Registro de Cliente</h2>

    <div id="client-errors" class="error"></div> <!-- Contenedor de errores -->

    <form method="post" onsubmit="return validarFormulario()">
        <div class="form-group">
            <label for="nombre_usuario">Nombre de usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
        </div>

        <div class="form-group">
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
        </div>

        <div class="form-group">
            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <button type="submit" class="submit-btn">Registrar</button>
        <div class="options-links">
        <a href="login.php" class="link">Ir al inicio de sesión</a>
    </div>
    </form>
</div>
</body>
</html>
