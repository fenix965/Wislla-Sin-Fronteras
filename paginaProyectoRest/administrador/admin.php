<?php 
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'administrador') {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();
$sql = "SELECT * FROM empleados";  
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}

$nombre_admin = $_SESSION['nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados - Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleAdministrador.css">
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
                    <li><a href="admin.php" class="active"><i class="fas fa-users"></i> Empleados</a></li>
                    <li><a href="clientes.php"><i class="fas fa-user-friends"></i> Clientes</a></li>
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

        <div class="container">
            <h2>Empleados de Wislla sin Fronteras</h2>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="input-group w-50">
                    <input type="text" id="search" class="form-control" placeholder="Buscar empleado por nombre...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                </div>
                <a href="insertarE.php" class="btn btn-primary"><i class="fas fa-plus"></i> Contratar Empleado</a>
            </div>

            <div class="row" id="employee-cards">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-md-4 employee-card">
                        <div class="card">
                            <img src="../imagenes/empleado.png" class="card-img-top" alt="Foto del empleado">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?></h5>
                                <p class="card-text">Puesto de trabajo: <?= htmlspecialchars($row['puesto']); ?></p>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">Email: <?= htmlspecialchars($row['email']); ?></li>
                                    <li class="list-group-item">Teléfono: <?= htmlspecialchars($row['telefono']); ?></li>
                                    <li class="list-group-item">Fecha ingreso: <?= htmlspecialchars($row['fecha_ingreso']); ?></li>
                                </ul>
                                <div class="card-body">
                                    <a href="editarE.php?id=<?= $row['id']; ?>" class="card-link"><i class="fas fa-edit"></i> Editar Empleado</a><br><br>
                                    <a href="#" class="card-link text-danger" onclick="confirmarDespido(<?= $row['id']; ?>, '<?= htmlspecialchars($row['nombre'] . ' ' . $row['apellidos']); ?>')">
                                        <i class="fas fa-trash"></i> Despedir empleado
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Despido -->
    <div class="modal fade" id="modalDespido" tabindex="-1" aria-labelledby="modalDespidoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalDespidoLabel">Confirmar Despido - Wislla sin Fronteras</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Está a punto de despedir a: <strong id="nombreEmpleado"></strong></p>
                    <div class="form-group">
                        <label for="razonDespido" class="form-label">Por favor, ingrese las razones del despido:</label>
                        <textarea class="form-control" id="razonDespido" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="ejecutarDespido()">Confirmar Despido</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        let empleadoIdActual = null;
        const modalDespido = new bootstrap.Modal(document.getElementById('modalDespido'));

        function confirmarDespido(id, nombre) {
            empleadoIdActual = id;
            document.getElementById('nombreEmpleado').textContent = nombre;
            document.getElementById('razonDespido').value = '';
            modalDespido.show();
        }

        function ejecutarDespido() {
            const razon = document.getElementById('razonDespido').value.trim();
            
            if (!razon) {
                alert('Por favor, ingrese las razones del despido.');
                return;
            }

            // Aquí podrías agregar código para enviar la razón del despido al servidor
            window.location.href = `eliminarE.php?id=${empleadoIdActual}&razon=${encodeURIComponent(razon)}`;
        }

        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const employeeCards = document.querySelectorAll('.employee-card');
            
            employeeCards.forEach(card => {
                const employeeName = card.querySelector('.card-title').textContent.toLowerCase();
                card.style.display = employeeName.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>