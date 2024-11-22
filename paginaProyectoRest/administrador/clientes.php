<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

$sql = "SELECT 
    c.*,
    COUNT(r.id) as total_reservas,
    SUM(CASE WHEN r.fecha < CURDATE() THEN 1 ELSE 0 END) as reservas_completadas,
    SUM(r.total) as total_gastado,
    MAX(r.fecha) as ultima_reserva,
    GROUP_CONCAT(
        CONCAT(
            DATE_FORMAT(r.fecha, '%d/%m/%Y'),
            '|', r.numero_personas,
            '|', COALESCE(r.total, 0)
        ) ORDER BY r.fecha DESC SEPARATOR ';'
    ) as historial_reservas
FROM clientes c
LEFT JOIN reservas r ON c.id = r.user_id
GROUP BY 
    c.id,
    c.usuario_id,
    c.nombre,
    c.apellidos,
    c.telefono,
    c.email";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}

$sql_stats = "SELECT 
    COUNT(DISTINCT c.id) as total_clientes,
    COUNT(r.id) as total_reservas,
    SUM(r.total) as ingresos_totales,
    AVG(r.total) as promedio_reserva
FROM clientes c
LEFT JOIN reservas r ON c.id = r.user_id";

$stats_result = mysqli_query($conn, $sql_stats);
$stats = mysqli_fetch_assoc($stats_result);

$nombre_admin = $_SESSION['nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de administración de clientes para Wislla Sin Fronteras">
    <title>Gestión de Clientes - Panel de Administración</title>
    <link rel="stylesheet" href="../css/styleCliAdmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

</head>
<body>
    <div class="layout">
        <aside class="sidebar">
        <div class="logo">
                <a class="navbar-brand" href="#">
                    <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla" style="max-width: 150px;">
                </a>
                <br>
                <h1>Administracion</h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="admin.php"><i class="fas fa-users"></i> Empleados</a></li>
                    <li><a href="clientes.php" class="active"><i class="fas fa-user-friends"></i> Clientes</a></li>
                    <li><a href="detalles.php"><i class="fas fa-receipt"></i> Detalles de Órdenes</a></li>
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

        <!-- Contenido principal -->
        <main class="main-content">
            <header class="content-header">
                <h2>Gestión de Clientes</h2>
            </header>

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-users"></i>
                        <div class="stats-value"><?= number_format($stats['total_clientes']); ?></div>
                        <div class="stats-label">Total Clientes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stats-card text-center">
                        <i class="fas fa-calendar-check"></i>
                        <div class="stats-value"><?= number_format($stats['total_reservas']); ?></div>
                        <div class="stats-label">Total Reservas</div>
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
                        <div class="stats-value">Bs. <?= number_format($stats['promedio_reserva'], 2); ?></div>
                        <div class="stats-label">Promedio por Reserva</div>
                    </div>
                </div>
            </div>

            <!-- Filtros y acciones -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" id="searchNombre" class="form-control" placeholder="Buscar por nombre...">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="searchEmail" class="form-control" placeholder="Buscar por email...">
                        </div>
                        <div class="col-md-3">
                            <select id="filterReservas" class="form-select">
                                <option value="">Filtrar por reservas</option>
                                <option value="con">Con reservas</option>
                                <option value="sin">Sin reservas</option>
                            </select>
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
                        <table id="clientesTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Total Reservas</th>
                                    <th>Última Reserva</th>
                                    <th>Total Gastado</th>
                                    <th>Reservas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id']); ?></td>
                                    <td><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></td>
                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                    <td><?= htmlspecialchars($row['telefono']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= htmlspecialchars($row['total_reservas']); ?></span>
                                    </td>
                                    <td><?= $row['ultima_reserva'] ? date('d/m/Y', strtotime($row['ultima_reserva'])) : 'Sin reservas'; ?></td>
                                    <td>Bs. <?= number_format($row['total_gastado'], 2); ?></td>
                                    <td>
                                        <a href="reservas_cliente.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">
                                            Ver Reservas
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de detalles -->
    <div class="modal fade" id="detallesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles del Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detallesCliente">
                    <!-- El contenido se carga dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de eliminación -->
    <div class="modal fade" id="eliminarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro que desea eliminar este cliente? Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="eliminarCliente()">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        let clienteIdActual = null;
        const detallesModal = new bootstrap.Modal(document.getElementById('detallesModal'));
        const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));
    </script>
    <script>
    function filtrarClientes() {
        const nombre = document.getElementById('searchNombre').value.toLowerCase();
        const email = document.getElementById('searchEmail').value.toLowerCase();
        const reservas = document.getElementById('filterReservas').value;

        // Seleccionamos las filas de la tabla correcta
        const rows = document.querySelectorAll('#clientesTable tbody tr');

        rows.forEach(row => {
            const nombreText = row.cells[1].textContent.toLowerCase();
            const emailText = row.cells[2].textContent.toLowerCase();
            const totalReservas = parseInt(row.cells[4].textContent);

            const cumpleNombre = nombre === '' || nombreText.includes(nombre);
            const cumpleEmail = email === '' || emailText.includes(email);
            const cumpleReservas = reservas === '' || 
                (reservas === 'con' && totalReservas > 0) || 
                (reservas === 'sin' && totalReservas === 0);

            row.style.display = cumpleNombre && cumpleEmail && cumpleReservas ? '' : 'none';
        });
    }

    document.getElementById('searchNombre').addEventListener('input', filtrarClientes);
    document.getElementById('searchEmail').addEventListener('input', filtrarClientes);
    document.getElementById('filterReservas').addEventListener('change', filtrarClientes);
    function exportarPDF() {
            alert('Función de exportar a PDF en desarrollo');
        }
</script>

</body>
</html>