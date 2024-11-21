<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

$nombre_admin = $_SESSION['nombre'];

include '../conexion.php';
$conn = conexion();


$sql_stats = "
    SELECT 
        COUNT(DISTINCT o.id) as total_ordenes,
        COUNT(DISTINCT p.id) as total_pedidos,
        SUM(p.total) as ingresos_totales,
        AVG(p.total) as promedio_pedido
    FROM orden o
    JOIN pedidos p ON o.pedido_id = p.id
";

$stats_result = mysqli_query($conn, $sql_stats);
$stats = mysqli_fetch_assoc($stats_result);

$sql_ordenes = "
    SELECT o.id AS orden_id, o.cantidad, 
           p.id AS pedido_id, p.fecha, p.estado, p.total,
           plat.id AS platillo_id, plat.nombre AS platillo_nombre, plat.precio AS platillo_precio, plat.descripcion
    FROM orden o
    JOIN pedidos p ON o.pedido_id = p.id
    JOIN platillos plat ON o.platillo_id = plat.id
    ORDER BY p.fecha DESC
";

$result_ordenes = mysqli_query($conn, $sql_ordenes);

if (!$result_ordenes) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de administración para Wislla Sin Fronteras">
    <title>Detalles de Órdenes - Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="logo">
                <a class="navbar-brand" href="#">
                    <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla" style="max-width: 150px;">
                </a>
                <br>
                <h1>Administración</h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="admin.php"><i class="fas fa-users"></i> Empleados</a></li>
                    <li><a href="clientes.php"><i class="fas fa-user-friends"></i> Clientes</a></li>
                    <li><a href="detalles.php" class="active"><i class="fas fa-receipt"></i> Detalles de Órdenes</a></li>
                    <li><a href="inventario.php"><i class="fas fa-boxes"></i> Inventario</a></li>
                    <li><a href="proveedores.php"><i class="fas fa-truck"></i> Proveedores</a></li>
                </ul>
            </nav>
            <div class="user-section">
                <span>Administrador: <?= htmlspecialchars($nombre_admin); ?></span>
                <br><br>
                <a href="../registro/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="content-header">
                <h2>Detalles de Órdenes</h2>
            </header>

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-shopping-cart"></i>
                        <div class="stats-value"><?= number_format($stats['total_ordenes']); ?></div>
                        <div class="stats-label">Total Órdenes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-file-invoice"></i>
                        <div class="stats-value"><?= number_format($stats['total_pedidos']); ?></div>
                        <div class="stats-label">Total Pedidos</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-dollar-sign"></i>
                        <div class="stats-value">Bs. <?= number_format($stats['ingresos_totales'], 2); ?></div>
                        <div class="stats-label">Ingresos Totales</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-chart-line"></i>
                        <div class="stats-value">Bs. <?= number_format($stats['promedio_pedido'], 2); ?></div>
                        <div class="stats-label">Promedio por Pedido</div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" id="searchPlatillo" class="form-control" placeholder="Buscar por platillo...">
                        </div>
                        <div class="col-md-3">
                            <select id="filterEstado" class="form-select">
                                <option value="">Todos los estados</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="completado">Completado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" id="filterFecha" class="form-control">
                        </div>
                        <div class="col-md-3 text-end">
                            <button class="btn btn-primary" onclick="exportarPDF()">
                                <i class="fas fa-file-pdf"></i> Exportar PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID Orden</th>
                                    <th>ID Pedido</th>
                                    <th>Fecha Pedido</th>
                                    <th>Estado</th>
                                    <th>Total</th>
                                    <th>Platillo</th>
                                    <th>Descripción</th>
                                    <th>Precio Unit.</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_ordenes)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['orden_id']); ?></td>
                                        <td><?= htmlspecialchars($row['pedido_id']); ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['fecha'])); ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $row['estado'] == 'completado' ? 'success' : 
                                                ($row['estado'] == 'pendiente' ? 'warning' : 'info')
                                            ?>">
                                                <?= htmlspecialchars(ucfirst($row['estado'])); ?>
                                            </span>
                                        </td>
                                        <td>Bs. <?= number_format($row['total'], 2); ?></td>
                                        <td><?= htmlspecialchars($row['platillo_nombre']); ?></td>
                                        <td><?= htmlspecialchars($row['descripcion']); ?></td>
                                        <td>Bs. <?= number_format($row['platillo_precio'], 2); ?></td>
                                        <td><?= htmlspecialchars($row['cantidad']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        // Funcionalidad de filtrado
        function filtrarOrdenes() {
            const platillo = document.getElementById('searchPlatillo').value.toLowerCase();
            const estado = document.getElementById('filterEstado').value.toLowerCase();
            const fecha = document.getElementById('filterFecha').value;

            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const platilloText = row.cells[5].textContent.toLowerCase();
                const estadoText = row.cells[3].textContent.toLowerCase();
                const fechaText = row.cells[2].textContent;

                const cumplePlatillo = platillo === '' || platilloText.includes(platillo);
                const cumpleEstado = estado === '' || estadoText.includes(estado);
                const cumpleFecha = fecha === '' || fechaText.includes(fecha);

                row.style.display = cumplePlatillo && cumpleEstado && cumpleFecha ? '' : 'none';
            });
        }

        // Event listeners para filtros
        document.getElementById('searchPlatillo').addEventListener('input', filtrarOrdenes);
        document.getElementById('filterEstado').addEventListener('change', filtrarOrdenes);
        document.getElementById('filterFecha').addEventListener('input', filtrarOrdenes);

        function exportarPDF() {
            alert('Función de exportar a PDF en desarrollo');
        }
    </script>
</body>
</html>