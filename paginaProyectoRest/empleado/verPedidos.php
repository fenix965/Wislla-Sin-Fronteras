<?php
session_start();

if (!isset($_SESSION['nombre'])) {
    header('Location: ../registro/login.php');
    exit();
}

include '../conexion.php';
$conn = conexion();

// Obtener pedidos con filtros
$filter_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$filter_fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : '';
$filter_fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : '';

$query_pedidos = "SELECT 
                    pedidos.id, 
                    clientes.nombre AS cliente_nombre, 
                    clientes.apellidos AS cliente_apellido, 
                    mesas.numero AS mesa_numero, 
                    pedidos.total, 
                    pedidos.estado,
                    pedidos.fecha
                  FROM pedidos 
                  JOIN clientes ON pedidos.cliente_id = clientes.id
                  JOIN mesas ON pedidos.mesa_id = mesas.id
                  WHERE 1=1";

// Construir condiciones de filtro
$params = [];
$types = "";

if (!empty($filter_estado)) {
    $query_pedidos .= " AND pedidos.estado = ?";
    $params[] = $filter_estado;
    $types .= "s";
}

// Filtro por fechas
if (!empty($filter_fecha_inicio) && !empty($filter_fecha_fin)) {
    $query_pedidos .= " AND pedidos.fecha BETWEEN ? AND ?";
    $params[] = $filter_fecha_inicio;
    $params[] = $filter_fecha_fin;
    $types .= "ss";
}

$query_pedidos .= " ORDER BY pedidos.id DESC";

$stmt = mysqli_prepare($conn, $query_pedidos);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result_pedidos = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    
    <!-- External Resources -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.9.96/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    
    <!-- Custom Styles -->
    <style>
        :root {
    --primary-brown: #8d4004;
    --primary-hover: #b05406;
    --dark-brown: #2b1810;
    --cream-bg: #fff8f1;
    --soft-brown: #f4e6d6;
    --input-bg: #ffffff;
    --error-color: #e74c3c;
    --success-green: #2d4a3e;
    --border-light: rgba(139, 69, 19, 0.2);
}

* {
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, var(--cream-bg) 0%, #fff4e6 100%);
    color: var(--dark-brown);
    margin: 0;
    padding: 0;
}

.custom-header {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, var(--soft-brown) 100%);
    border-bottom: 2px solid var(--primary-brown);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 1rem;
}

.custom-header i {
    color: var(--primary-brown);
}

h1 {
    font-size: 2rem;
    font-weight: 600;
    color: #333;
}

.custom-btn {
    background-color: var(--primary-brown);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.custom-btn:hover {
    background-color: var(--primary-hover);
    transform: translateY(-3px);
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
}

.category-filter {
    background-color: var(--input-bg);
    border: 1px solid var(--border-light);
    border-radius: 0.5rem;
    padding: 0.5rem;
    transition: all 0.3s ease;
}

.category-filter:focus {
    border-color: var(--primary-brown);
    box-shadow: 0 0 0 3px rgba(141, 64, 4, 0.1);
}

.order-section {
    background-color: white;
    border-radius: 1.5rem;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    border: 2px solid var(--soft-brown);
    margin-bottom: 2rem;
}

.order-status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
    font-weight: 500;
    text-transform: capitalize;
}

.estado-pendiente {
    color: #ff9800;
    background-color: rgba(255, 152, 0, 0.1);
}

.estado-completado {
    color: #4caf50;
    background-color: rgba(76, 175, 80, 0.1);
}

.estado-cancelado {
    color: #f44336;
    background-color: rgba(244, 67, 54, 0.1);
}

#search {
    border: 2px solid var(--border-light);
    border-radius: 0.75rem;
    padding: 0.75rem;
    background-color: var(--input-bg);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    width: 100%;
}

#search:focus {
    border-color: var(--primary-brown);
    outline: none;
    box-shadow: 0 0 0 3px rgba(141, 64, 4, 0.1);
}

#pedidosTable tr:hover {
    background-color: rgba(141, 64, 4, 0.02);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1.5rem;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

th {
    background-color: var(--soft-brown);
    font-weight: 600;
    color: var(--dark-brown);
}

td {
    background-color: white;
}

td .custom-btn {
    width: auto;
    margin-bottom: 1rem;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(5px);
}

.modal-content {
    background: white;
    padding: 2.5rem;
    border-radius: 1.5rem;
    width: 90%;
    max-width: 700px;
    max-height: 80%;
    overflow-y: auto;
    position: relative;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    border: 2px solid var(--soft-brown);
}

.modal button {
    position: absolute;
    top: 2rem;
    right: 2rem;
    cursor: pointer;
    background: none;
    border: none;
}

@media (min-width: 768px) {
    .custom-header {
        padding: 2rem;
    }

    .order-section {
        padding: 3rem;
    }
}

@media (max-width: 767px) {
    .order-status-badge {
        font-size: 0.875rem;
    }

    #search {
        margin-top: 1rem;
    }

    .custom-btn {
        padding: 0.5rem 1rem;
    }
}

    </style>
</head>
<body class="antialiased">
    <main class="container mx-auto px-6 py-8">
        <!-- Page Header -->
        <header class="custom-header rounded-2xl mb-8 p-6 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <i data-feather="list" class="text-4xl text-primary-brown"></i>
                <h1 class="text-4xl font-bold text-gray-800">Gestión de Pedidos</h1>
            </div>
            <nav>
                <a href="../empleado/indexE.php" class="custom-btn">
                    <i data-feather="arrow-left" class="mr-2"></i>
                    Regresar
                </a>
            </nav>
        </header>

        <!-- Filters Section -->
        <section class="bg-white shadow-md p-6 rounded-2xl mb-6">
            <form method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex flex-col">
                    <label for="estado" class="text-sm mb-1 text-gray-600">Estado</label>
                    <select id="estado" name="estado" class="category-filter">
                        <option value="">Todos los Estados</option>
                        <option value="pendiente" <?php echo $filter_estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="completado" <?php echo $filter_estado == 'completado' ? 'selected' : ''; ?>>Completado</option>
                        <option value="cancelado" <?php echo $filter_estado == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>

                <div class="flex flex-col">
                    <label for="fecha_inicio" class="text-sm mb-1 text-gray-600">Desde</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($filter_fecha_inicio); ?>" class="category-filter">
                </div>

                <div class="flex flex-col">
                    <label for="fecha_fin" class="text-sm mb-1 text-gray-600">Hasta</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($filter_fecha_fin); ?>" class="category-filter">
                </div>

                <div class="flex items-end">
                    <button type="submit" class="custom-btn mt-4">
                        <i data-feather="filter" class="mr-2"></i>
                        Filtrar
                    </button>
                </div>
            </form>
        </section>

        <!-- Search Section -->
        <section class="mb-6">
            <div class="relative">
                <i data-feather="search" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="search" class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-primary-brown" placeholder="Buscar por cliente o mesa..." onkeyup="filtrarPedidos()">
            </div>
        </section>

        <!-- Order Details Modal -->
        <div id="orderDetailsModal" class="modal">
            <div class="modal-content">
                <button type="button" class="absolute top-4 right-4 cursor-pointer" onclick="closeModal()">
                    <i data-feather="x-circle" class="text-gray-700 w-8 h-8"></i>
                </button>
                <div id="orderDetailsContent" class="mt-6"></div>
                <button type="button" class="custom-btn mt-6" onclick="closeModal()">
                    <i data-feather="arrow-left" class="mr-2"></i>
                    Regresar a los Pedidos
                </button>
            </div>
        </div>

        <!-- Orders Table -->
        <section class="order-section overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-soft-brown">
                        <th class="py-3 px-4 border-b border-gray-200">ID</th>
                        <th class="py-3 px-4 border-b border-gray-200">Cliente</th>
                        <th class="py-3 px-4 border-b border-gray-200">Mesa</th>
                        <th class="py-3 px-4 border-b border-gray-200">Total</th>
                        <th class="py-3 px-4 border-b border-gray-200">Fecha</th>
                        <th class="py-3 px-4 border-b border-gray-200">Estado</th>
                        <th class="py-3 px-4 border-b border-gray-200">Acción</th>
                    </tr>
                </thead>
                <tbody id="pedidosTable">
                    <?php while ($pedido = mysqli_fetch_assoc($result_pedidos)) { ?>
                        <tr data-order-id="<?php echo $pedido['id']; ?>" class="hover:bg-soft-brown">
                            <td class="py-3 px-4 border-b border-gray-100"><?php echo $pedido['id']; ?></td>
                            <td class="py-3 px-4 border-b border-gray-100"><?php echo $pedido['cliente_nombre'] . ' ' . $pedido['cliente_apellido']; ?></td>
                            <td class="py-3 px-4 border-b border-gray-100"><?php echo $pedido['mesa_numero']; ?></td>
                            <td class="py-3 px-4 border-b border-gray-100">$<?php echo number_format($pedido['total'], 2); ?></td>
                            <td class="py-3 px-4 border-b border-gray-100"><?php echo date('d/m/Y', strtotime($pedido['fecha'])); ?></td>
                            <td class="py-3 px-4 border-b border-gray-100">
                                <span class="<?php echo 'estado-' . $pedido['estado'] . ' order-status-badge'; ?>">
                                    <?php echo ucfirst($pedido['estado']); ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b border-gray-100">
                                <div class="flex flex-col space-y-2">
                                    <button class="custom-btn" onclick="openModal(<?php echo $pedido['id']; ?>)">
                                        <i data-feather="eye" class="mr-2"></i>
                                        Detalles
                                    </button>
                                    <form action="cambiarEstadoPedido.php" method="POST">
                                        <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                        <select name="nuevo_estado" class="category-filter mb-2">
                                            <option value="pendiente" <?php echo $pedido['estado'] == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                            <option value="completado" <?php echo $pedido['estado'] == 'completado' ? 'selected' : ''; ?>>Completado</option>
                                            <option value="cancelado" <?php echo $pedido['estado'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                        </select>
                                        <button type="submit" class="custom-btn">Cambiar Estado</button>
                                    </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
         feather.replace();
        // Función para mostrar los detalles del pedido
        function openModal(orderId) {
            const row = document.querySelector(`[data-order-id='${orderId}']`);
            const details = {
                id: row.children[0].textContent,
                cliente: row.children[1].textContent,
                mesa: row.children[2].textContent,
                total: row.children[3].textContent,
                fecha: row.children[4].textContent,
                estado: row.children[5].textContent
            };

            const content = `
                <h2>Detalles del Pedido #${details.id}</h2>
                <p><strong>Cliente:</strong> ${details.cliente}</p>
                <p><strong>Mesa:</strong> ${details.mesa}</p>
                <p><strong>Total:</strong> ${details.total}</p>
                <p><strong>Fecha:</strong> ${details.fecha}</p>
                <p><strong>Estado:</strong> ${details.estado}</p>
            `;
            document.getElementById('orderDetailsContent').innerHTML = content;

            // Mostrar el modal
            document.getElementById('orderDetailsModal').style.display = 'flex';
        }

        // Función para cerrar el modal
        function closeModal() {
            document.getElementById('orderDetailsModal').style.display = 'none';
        }

        // Filtrar pedidos en la tabla
        function filtrarPedidos() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('#pedidosTable tr');
            rows.forEach(row => {
                const cliente = row.cells[1].textContent.toLowerCase();
                const mesa = row.cells[2].textContent.toLowerCase();
                if (cliente.includes(searchTerm) || mesa.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
