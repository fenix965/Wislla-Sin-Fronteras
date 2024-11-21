<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

$nombre_admin = $_SESSION['nombre'];

include '../conexion.php';

$conn = conexion();

// Consulta para obtener los proveedores
$sql_proveedores = "SELECT * FROM proveedores";  // Obtener todos los proveedores
$result_proveedores = mysqli_query($conn, $sql_proveedores);

if (!$result_proveedores) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <title>Proveedores - Panel de Administración</title>
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
                    <li><a href="proveedores.php" class="active"><i class="fas fa-truck"></i> Proveedores</a></li>
                </ul>
            </nav>
            <div class="user-section">
                <span>Administrador: <?= htmlspecialchars($nombre_admin); ?></span>
                <a href="../registro/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-truck"></i> Gestión de Proveedores</h2>
            </header>

            <div class="actions-bar">
                <button class="btn btn-primary" onclick="window.location.href='agregar_proveedor.php'">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </button>
            </div>

            <section class="providers-section">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-building"></i> Nombre</th>
                                <th><i class="fas fa-user"></i> Contacto</th>
                                <th><i class="fas fa-phone"></i> Teléfono</th>
                                <th><i class="fas fa-envelope"></i> Email</th>
                                <th><i class="fas fa-map-marker-alt"></i> Dirección</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($result_proveedores)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nombre']); ?></td>
                                <td><?= htmlspecialchars($row['contacto']); ?></td>
                                <td><?= htmlspecialchars($row['telefono']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['direccion']); ?></td>
                                <td class="actions-column">
                                    <button class="btn btn-info btn-sm" onclick="mostrarMensaje()">
                                        <i class="fas fa-phone"></i> Contactar
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="window.location.href='editar_proveedor.php?id=<?= $row['id']; ?>'">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmarEliminar(<?= $row['id']; ?>)">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- Modal personalizado -->
    <div id="modalMensaje" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <p>Se está contactando con el proveedor. En breve lo llamarán.</p>
        </div>
    </div>

    <style>
        .modal {
            display: flex;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 300px;
            text-align: center;
        }

        .close {
            float: right;
            font-size: 18px;
            cursor: pointer;
        }
    </style>

    <script>
        function mostrarMensaje() {
            document.getElementById("modalMensaje").style.display = "flex";
        }

        function cerrarModal() {
            document.getElementById("modalMensaje").style.display = "none";
        }

        function confirmarEliminar(id) {
            if (confirm("¿Está seguro de que desea eliminar este proveedor?")) {
                window.location.href = `eliminar_proveedor.php?id=${id}`;
            }
        }
    </script>
</body>
</html>
