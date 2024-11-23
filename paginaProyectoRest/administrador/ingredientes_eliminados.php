<?php
session_start();

// Validar que el usuario está logueado y tiene rol de 'administrador'
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

$nombre_admin = $_SESSION['nombre'];

include '../conexion.php';

// Conexión a la base de datos
$conn = conexion();

// Consulta para obtener los ingredientes eliminados
$sql_ingredientes_eliminados = "SELECT * FROM ingredientes WHERE activo = 0";
$result_ingredientes_eliminados = mysqli_query($conn, $sql_ingredientes_eliminados);

if (!$result_ingredientes_eliminados) {
    die("Error en la consulta: " . mysqli_error($conn));
}
// Array de mapeo de valores a texto visible
$motivo_map = [
    'descontinuado' => 'Producto descontinuado',
    'error_registro' => 'Error en el registro',
    'sin_stock' => 'Sin stock permanente',
    'cambio_proveedor' => 'Cambio de proveedor',
    'otro' => 'Otro motivo'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <title>Ingredientes Eliminados</title>
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
                <span><i class="fas fa-user-shield"></i> <?= htmlspecialchars($nombre_admin); ?></span>
                <br><br>
                <a href="../registro/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-trash-alt"></i> Ingredientes Eliminados</h2>
                <a href="inventario.php" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Volver a Inventario</a>
            </header>

            <section class="stats-card">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-utensils"></i> Nombre</th>
                                <th><i class="fas fa-balance-scale"></i> Unidad de Medida</th>
                                <th><i class="fas fa-cogs"></i> Motivo de Eliminación</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result_ingredientes_eliminados)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nombre']); ?></td>
                                    <td><?= htmlspecialchars($row['unidad_medida']); ?></td>
                                    <td>
                                        <?php 
                                        $motivo = $row['motivo_eliminacion'] ?? 'Sin motivo registrado';
                                        echo htmlspecialchars($motivo_map[$motivo] ?? $motivo); 
                                        ?>
                                    </td>
                                    <td>
                                        <a href="restaurar_ingrediente.php?id=<?= $row['id']; ?>" class="btn btn-success btn-sm">
                                            <i class="fas fa-undo"></i> Restaurar
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>


                    </table>
                </div>
            </section>
        </main>
    </div>
    <!-- Modal para capturar motivo de restauración -->
    <div class="modal fade" id="restaurarModal" tabindex="-1" aria-labelledby="restaurarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="restaurarModalLabel">Motivo de Restauración</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="restaurarForm" action="restaurar_ingrediente.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="ingredienteId">
                        <div class="mb-3">
                            <label for="motivo_restauracion" class="form-label">Escribe el motivo de la restauración:</label>
                            <textarea class="form-control" name="motivo_restauracion" id="motivo_restauracion" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
