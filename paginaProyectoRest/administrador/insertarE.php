<?php
session_start();

// Verificación de sesión y rol de usuario
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre']) && isset($_POST['apellidos']) && isset($_POST['puesto']) && isset($_POST['telefono']) && isset($_POST['email']) && 
        isset($_POST['nombre_usuario']) && isset($_POST['contraseña']) && !empty($_POST['contraseña'])) {

        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $puesto = $_POST['puesto'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];

        // Asignación del salario según el puesto
        switch ($puesto) {
            case 'Mesero':
                $salario = 1500;
                break;
            case 'Cocinero':
                $salario = 2000;
                break;
            case 'Cocinero Vegano':
                $salario = 2200;
                break;
            case 'Limpiador':
                $salario = 1200;
                break;
            case 'Supervisor':
                $salario = 2500;
                break;
            default:
                $salario = 0;
        }

        $fecha_ingreso = !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : date('Y-m-d');

        $conn = conexion();

        $nombre_usuario = $_POST['nombre_usuario'];
        $contrasena = $_POST['contraseña'];  
        $rol = 'empleado'; 

        $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, contrasena, rol) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre_usuario, $contrasena, $rol);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;

            if ($user_id > 0) {
                $stmt = $conn->prepare("INSERT INTO empleados (usuario_id, nombre, apellidos, puesto, telefono, email, fecha_ingreso, salario) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssssi", $user_id, $nombre, $apellidos, $puesto, $telefono, $email, $fecha_ingreso, $salario);

                if ($stmt->execute()) {
                    echo "Empleado y Usuario insertados correctamente";
                    header("Location: admin.php");
                    exit();
                } else {
                    echo "Error al insertar empleado: " . mysqli_error($conn);
                }
            } else {
                echo "Error al obtener el ID del usuario insertado.";
            }
        } else {
            echo "Error al insertar usuario: " . mysqli_error($conn);
        }

        $stmt->close();
        mysqli_close($conn);
    } else {
        echo "Error: La contraseña no puede estar vacía.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <title>Insertar Empleado - Panel de Administración</title>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <a class="navbar-brand" href="#">
                    <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla">
                </a>
                <h1>Administración</h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="admin.php" class="active"><i class="fas fa-users"></i> Empleados</a></li>
                    <li><a href="clientes.php"><i class="fas fa-user-friends"></i> Clientes</a></li>
                    <li><a href="detalles.php"><i class="fas fa-receipt"></i> Detalles</a></li>
                    <li><a href="inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i> Proveedores</a></li>
                </ul>
            </nav>
            <div class="user-section">
                <span><i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../registro/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-user-plus"></i> Nuevo Empleado</h2>
                <a href="admin.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </header>

            <div class="form-container">
                <form method="POST" class="employee-form">
                    <div class="form-sections">
                        <div class="form-section personal-info">
                            <div class="section-header">
                                <i class="fas fa-user-circle"></i>
                                <h3>Información Personal</h3>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Nombre</label>
                                <input type="text" name="nombre" required placeholder="Ingrese nombre">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user"></i> Apellidos</label>
                                <input type="text" name="apellidos" required placeholder="Ingrese apellidos">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-briefcase"></i> Puesto</label>
                                <select name="puesto" required>
                                    <option value="">Seleccione un puesto</option>
                                    <option value="Mesero">Mesero</option>
                                    <option value="Cocinero">Cocinero</option>
                                    <option value="Cocinero Vegano">Cocinero Vegano</option>
                                    <option value="Limpiador">Limpiador</option>
                                    <option value="Supervisor">Supervisor</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-section contact-info">
                            <div class="section-header">
                                <i class="fas fa-address-card"></i>
                                <h3>Información de Contacto</h3>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Teléfono</label>
                                <input type="tel" name="telefono" placeholder="Ingrese teléfono">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" placeholder="Ingrese email">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-calendar-alt"></i> Fecha de Ingreso</label>
                                <input type="date" name="fecha_ingreso" value="<?= date('Y-m-d') ?>" readonly>
                            </div>
                        </div>

                        <div class="form-section account-info">
                            <div class="section-header">
                                <i class="fas fa-lock"></i>
                                <h3>Información de Cuenta</h3>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user-tag"></i> Nombre de Usuario</label>
                                <input type="text" name="nombre_usuario" required placeholder="Ingrese nombre de usuario">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-key"></i> Contraseña</label>
                                <input type="password" name="contraseña" required placeholder="Ingrese contraseña">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Empleado
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Limpiar Formulario
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>