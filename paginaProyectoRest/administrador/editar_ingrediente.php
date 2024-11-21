<?php
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

// Validar que el id del ingrediente se pasa por GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Consultar el ingrediente actual para mostrar los datos
    $sql = "SELECT * FROM ingredientes WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $nombre = $row['nombre'];
        $cantidad_disponible = $row['cantidad_disponible'];
        $unidad_medida = $row['unidad_medida'];
        $precio_unitario = $row['precio_unitario'];
    } else {
        // Si no se encuentra el ingrediente, redirigir al inventario
        header('Location: inventario.php');
        exit();
    }
} else {
    // Si no se pasa un id válido, redirigir al inventario
    header('Location: inventario.php');
    exit();
}

// Variable para el mensaje de éxito o error
$mensaje = '';
$mensaje_error = '';
$mensaje_tipo = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = mysqli_real_escape_string($conn, $_POST['nombre']);
    $cantidad_disponible = mysqli_real_escape_string($conn, $_POST['cantidad_disponible']);
    $unidad_medida = mysqli_real_escape_string($conn, $_POST['unidad_medida']);
    $precio_unitario = mysqli_real_escape_string($conn, $_POST['precio_unitario']);

    // Actualizar el ingrediente en la base de datos
    $sql_update = "UPDATE ingredientes SET nombre = '$nombre', cantidad_disponible = '$cantidad_disponible', 
                   unidad_medida = '$unidad_medida', precio_unitario = '$precio_unitario' WHERE id = $id";
    
    if (mysqli_query($conn, $sql_update)) {
        $mensaje = 'Ingrediente actualizado exitosamente';
        $mensaje_tipo = 'success';
        header('Location: inventario.php');
        exit();
    } else {
        $mensaje_error = 'Error al actualizar el ingrediente';
        $mensaje_tipo = 'danger';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <title>Editar Ingrediente</title>
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
                    <li><a href="admin.php"><i class="fas fa-users"></i> Empleados</a></li>
                    <li><a href="clientes.php"><i class="fas fa-user-friends"></i> Clientes</a></li>
                    <li><a href="detalles.php"><i class="fas fa-receipt"></i> Detalles de Órdenes</a></li>
                    <li><a href="inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i> Proveedores</a></li>
                </ul>
            </nav>
            <div class="user-section">
                <span><i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['nombre']); ?></span>
                <br><br>
                <a href="../registro/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-edit"></i> Editar Ingrediente</h2>
            </header>

            <!-- Mensaje de éxito o error -->
            <?php if ($mensaje): ?>
                <div class="alert alert-<?= $mensaje_tipo; ?> alert-dismissible fade show" role="alert">
                    <strong><?= $mensaje; ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($mensaje_error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><?= $mensaje_error; ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <section class="form-section">
                <form action="editar_ingrediente.php?id=<?= $id; ?>" method="post">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Ingrediente</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="cantidad_disponible" class="form-label">Cantidad Disponible</label>
                        <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" value="<?= htmlspecialchars($cantidad_disponible); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="unidad_medida" class="form-label">Unidad de Medida</label>
                        <select class="form-select" id="unidad_medida" name="unidad_medida" required>
                            <option value="kilogramos" <?= $unidad_medida == 'kilogramos' ? 'selected' : ''; ?>>Kilogramos (kg)</option>
                            <option value="gramos" <?= $unidad_medida == 'gramos' ? 'selected' : ''; ?>>Gramos (g)</option>
                            <option value="litros" <?= $unidad_medida == 'litros' ? 'selected' : ''; ?>>Litros (l)</option>
                            <option value="mililitros" <?= $unidad_medida == 'mililitros' ? 'selected' : ''; ?>>Mililitros (ml)</option>
                            <option value="unidades" <?= $unidad_medida == 'unidades' ? 'selected' : ''; ?>>Unidades</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="precio_unitario" class="form-label">Precio Unitario (Bs)</label>
                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" value="<?= htmlspecialchars($precio_unitario); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="inventario.php" class="btn btn-secondary">Cancelar</a>
                </form>
            </section>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
