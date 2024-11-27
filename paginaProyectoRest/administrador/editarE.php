<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'], $_POST['nombre'], $_POST['apellidos'], $_POST['puesto'], $_POST['telefono'], $_POST['email'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $puesto = $_POST['puesto'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $fecha_ingreso = !empty($_POST['fecha_ingreso']) ? $_POST['fecha_ingreso'] : date('Y-m-d');

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
        };

        $conn = conexion();
        $stmt = $conn->prepare("UPDATE empleados SET nombre = ?, apellidos = ?, puesto = ?, telefono = ?, email = ?, fecha_ingreso = ?, salario = ? WHERE id = ?");
        $stmt->bind_param("ssssssdi", $nombre, $apellidos, $puesto, $telefono, $email, $fecha_ingreso, $salario, $id);

        if ($stmt->execute()) {
            header("Location: admin.php");
            exit(); 
        } else {
            echo "Error al actualizar: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Error: faltan datos requeridos.";
    }
} 

$id = $_GET['id'] ?? null;
if ($id) {
    $conn = conexion();
    $stmt = $conn->prepare("SELECT * FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado - Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <a class="navbar-brand" href="#">
                    <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla" style="max-width: 150px;">
                </a>
                <h1>Administración</h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="admin.php" class="active"><i class="fas fa-users"></i>Empleados</a></li>
                    <li><a href="clientes.php"><i class="fas fa-user-friends"></i>Clientes</a></li>
                    <li><a href="detalles.php"><i class="fas fa-receipt"></i>Detalles de Órdenes</a></li>
                    <li><a href="inventario.php"><i class="fas fa-box"></i>Inventario</a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i>Proveedores</a></li>
                </ul>
            </nav>
            <div class="user-section">
                <span><i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['nombre']); ?></span>
                <a href="../registro/logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-user-edit"></i> Editar Empleado</h2>
            </header>

            <?php if (isset($row)): ?>
            <div class="form-container">
                <form method="post" class="employee-form">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">
                    
                    <div class="form-sections">
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-user"></i>
                                <h3>Información Personal</h3>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-signature"></i> Nombre</label>
                                <input type="text" name="nombre" value="<?= htmlspecialchars($row['nombre']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-user-tag"></i> Apellidos</label>
                                <input type="text" name="apellidos" value="<?= htmlspecialchars($row['apellidos']) ?>" required>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-briefcase"></i>
                                <h3>Información Laboral</h3>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-id-badge"></i> Puesto</label>
                                <select name="puesto" required>
                                    <option value="Mesero" <?= $row['puesto'] === 'Mesero' ? 'selected' : '' ?>>Mesero</option>
                                    <option value="Cocinero" <?= $row['puesto'] === 'Cocinero' ? 'selected' : '' ?>>Cocinero</option>
                                    <option value="Cocinero Vegano" <?= $row['puesto'] === 'Cocinero Vegano' ? 'selected' : '' ?>>Cocinero Vegano</option>
                                    <option value="Limpiador" <?= $row['puesto'] === 'Limpiador' ? 'selected' : '' ?>>Limpiador</option>
                                    <option value="Supervisor" <?= $row['puesto'] === 'Supervisor' ? 'selected' : '' ?>>Supervisor</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-calendar-alt"></i> Fecha de Ingreso</label>
                                <input type="date" readonly name="fecha_ingreso" value="<?= htmlspecialchars($row['fecha_ingreso']) ?>">
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-address-card"></i>
                                <h3>Información de Contacto</h3>
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-phone"></i> Teléfono</label>
                                <input type="tel" name="telefono" value="<?= htmlspecialchars($row['telefono']) ?>" pattern="[0-9]{10}">
                            </div>
                            <div class="form-group">
                                <label><i class="fas fa-envelope"></i> Email</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($row['email']) ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="admin.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="status-message error">
                <i class="fas fa-exclamation-circle"></i>
                <p>Error: empleado no encontrado.</p>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>