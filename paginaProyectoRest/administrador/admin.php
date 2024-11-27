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
$sql_total_salario = "SELECT SUM(salario) AS total_salarios FROM empleados";
$result_total_salario = mysqli_query($conn, $sql_total_salario);
$total_salarios = mysqli_fetch_assoc($result_total_salario)['total_salarios'];

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
                                    <li class="list-group-item">Salario: Bs. <?= number_format(htmlspecialchars($row['salario']), 2); ?></li>
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
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info text-center" role="alert">
                        <strong>Total de Salarios a Pagar:</strong> Bs. <?= number_format($total_salarios, 2); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


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

    const formData = new FormData();
    formData.append('id', empleadoIdActual);
    formData.append('razon', razon);

    fetch('procesar_despido.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Create and show toast notification
            const toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                const container = document.createElement('div');
                container.id = 'toastContainer';
                container.style.position = 'fixed';
                container.style.top = '20px';
                container.style.right = '20px';
                container.style.zIndex = '1050';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.classList.add('toast', 'bg-success', 'text-white');
            toast.innerHTML = `
                <div class="toast-header bg-success text-white">
                    <strong class="me-auto">Wislla sin Fronteras</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    Empleado despedido exitosamente
                </div>
            `;

            document.getElementById('toastContainer').appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            // Remove the specific employee card
            const employeeCard = document.querySelector(`.employee-card:has(.card-link[href*="editarE.php?id=${empleadoIdActual}"])`);
            if (employeeCard) {
                employeeCard.remove();
            }
            modalDespido.hide();
        } else {
            // Error toast
            const toastContainer = document.getElementById('toastContainer') || document.createElement('div');
            toastContainer.id = 'toastContainer';
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '1050';
            document.body.appendChild(toastContainer);

            const toast = document.createElement('div');
            toast.classList.add('toast', 'bg-danger', 'text-white');
            toast.innerHTML = `
                <div class="toast-header bg-danger text-white">
                    <strong class="me-auto">Error</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    ${data.message || 'Error al despedir al empleado'}
                </div>
            `;

            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        }
    })
    .catch(error => {
        console.error('Error al procesar la solicitud:', error);
        // Error toast for network/fetch errors
        const toastContainer = document.getElementById('toastContainer') || document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.style.position = 'fixed';
        toastContainer.style.top = '20px';
        toastContainer.style.right = '20px';
        toastContainer.style.zIndex = '1050';
        document.body.appendChild(toastContainer);

        const toast = document.createElement('div');
        toast.classList.add('toast', 'bg-danger', 'text-white');
        toast.innerHTML = `
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Hubo un error al intentar procesar el despido
            </div>
        `;

        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    });
}

        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const employeeCards = document.querySelectorAll('.employee-card');
            
            employeeCards.forEach(card => {
                const employeeName = card.querySelector('.card-title').textContent.toLowerCase();
                card.style.display = employeeName.includes(searchTerm) ? '' : 'none';
            });
        });
        function updateTotalSalary(removedSalary) {
            const totalSalaryElement = document.querySelector('.alert-info strong');
            if (totalSalaryElement) {
                // Remove 'Bs. ' and convert to number
                let currentTotal = parseFloat(totalSalaryElement.textContent.replace('Total de Salarios a Pagar: Bs. ', '').replace(/,/g, ''));
                
                // Subtract the removed salary
                let newTotal = currentTotal - removedSalary;
                
                // Update the display
                totalSalaryElement.textContent = `Total de Salarios a Pagar: Bs. ${newTotal.toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            }
        }
    </script>
</body>
</html>