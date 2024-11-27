<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['nombre'])) {
    header('Location: ../registro/login.php');
    exit();
}

// Verificar si se ha hecho clic en el botón de cerrar sesión
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../registro/login.php');
    exit();
}

$nombre_empleado = $_SESSION['nombre'];
include '../conexion.php';
$conn = conexion();

// Obtener todos los platillos disponibles
$query_platillos = "SELECT * FROM platillos";
$result_platillos = mysqli_query($conn, $query_platillos);

// Obtener los clientes, mesas y empleados disponibles
$query_clientes = "SELECT * FROM clientes";
$result_clientes = mysqli_query($conn, $query_clientes);
$query_mesas = "SELECT * FROM mesas WHERE estado = 'disponible'";
$result_mesas = mysqli_query($conn, $query_mesas);
$query_empleados = "SELECT * FROM empleados";
$result_empleados = mysqli_query($conn, $query_empleados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pedidos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        }

        body {
            background-color: var(--cream-bg);
            color: var(--dark-brown);
        }

        .custom-header {
            background: linear-gradient(135deg, white 0%, var(--soft-brown) 100%);
            border-bottom: 2px solid var(--primary-brown);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .custom-btn {
            background-color: var(--primary-brown);
            color: white;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .custom-btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .platillo-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            padding: 1rem;
            border: 1px solid var(--soft-brown);
            transition: transform 0.3s ease;
        }

        .platillo-card:hover {
            transform: scale(1.03);
        }

        .platillo-card img {
            border-radius: 0.75rem;
            object-fit: cover;
            width: 100%;
            height: 200px;
        }

        .order-section {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            padding: 1.5rem;
            border: 1px solid var(--soft-brown);
        }

        .nav-item {
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            transform: translateY(-3px);
        }

        .category-filter select {
            border: 1px solid var(--border-light);
            border-radius: 0.5rem;
            padding: 0.5rem;
            background-color: white;
            color: var(--dark-brown);
        }

        .add-to-order {
            background-color: var(--primary-brown);
            color: white;
            border-radius: 0.5rem;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }

        .add-to-order:hover {
            background-color: var(--primary-hover);
        }
    </style>
</head>
<body class="antialiased">
    <div class="container mx-auto px-4 py-8">
        <header class="custom-header shadow-md rounded-lg mb-6 p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold flex items-center" style="color: var(--primary-brown);">
                    <i data-feather="shopping-cart" class="mr-3"></i>
                    Sistema de Pedidos
                </h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600 flex items-center">
                        <i data-feather="user" class="mr-2"></i>
                        Bienvenido, <?php echo htmlspecialchars($nombre_empleado); ?>!
                    </span>
                    <form action="" method="POST" class="inline">
                        <button type="submit" name="logout" class="custom-btn hover:bg-opacity-90">
                            <i data-feather="log-out"></i>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <nav class="mb-6">
            <div class="flex space-x-4">
                <a href="indexE.php" class="custom-btn nav-item">
                    <i data-feather="home"></i>
                    Inicio
                </a>
                <a href="pedidos.php" class="custom-btn nav-item">
                    <i data-feather="shopping-cart"></i>
                    Pedidos
                </a>
                <a href="reservas.php" class="custom-btn nav-item">
                    <i data-feather="calendar"></i>
                    Reservas
                </a>
                <a href="verPedidos.php" class="custom-btn nav-item">
                    <i data-feather="calendar"></i>
                    Ver Pedidos
                </a>
            </div>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Menú de Platillos -->
            <div class="md:col-span-2">
                <div class="mb-4 flex justify-between items-center">
                    <h2 class="text-2xl font-semibold" style="color: var(--primary-brown);">Menú de Platillos</h2>
                    <div class="category-filter">
                        <select id="categoria">
                            <option value="">Todas las Categorías</option>
                            <option value="sopas">Sopas</option>
                            <option value="plato principal">Platos Principales</option>
                            <option value="postre">Postres</option>
                            <option value="bebidas">Bebidas</option>
                            <option value="aperitivos">Aperitivos</option>
                        </select>
                    </div>
                </div>
                <div id="menu-grid" class="menu-grid">
                    <?php while ($platillo = mysqli_fetch_assoc($result_platillos)) { ?>
                        <div class="platillo-card" 
                             data-categoria="<?php echo htmlspecialchars($platillo['categoria']); ?>">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($platillo['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($platillo['nombre']); ?>">
                            <div class="mt-4">
                                <h3 class="text-xl font-bold mb-2" style="color: var(--primary-brown);">
                                    <?php echo htmlspecialchars($platillo['nombre']); ?>
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    <?php echo htmlspecialchars($platillo['descripcion']); ?>
                                </p>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold" style="color: var(--primary-brown);">
                                        $<?php echo number_format($platillo['precio'], 2); ?>
                                    </span>
                                    <button class="add-to-order"
                                            data-id="<?php echo htmlspecialchars($platillo['id']); ?>"
                                            data-nombre="<?php echo htmlspecialchars($platillo['nombre']); ?>"
                                            data-precio="<?php echo htmlspecialchars($platillo['precio']); ?>">
                                        Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Orden Actual -->
            <div class="order-section">
                <h2 class="text-2xl font-semibold mb-4" style="color: var(--primary-brown);">Orden Actual</h2>
                <form id="orderForm" method="post" action="procesar_pedido.php">
                    <div id="order-items" class="mb-4">
                        <!-- Elementos de la orden se agregarán dinámicamente aquí -->
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between mb-2">
                            <span>Total:</span>
                            <span id="total-amount" class="font-bold" style="color: var(--primary-brown);">$0.00</span>
                        </div>
                        <div class="mb-4">
                            <label for="cliente_id" class="block mb-2">Cliente:</label>
                            <select name="cliente_id" id="cliente_id" class="w-full rounded p-2 border" required>
                                <?php 
                                mysqli_data_seek($result_clientes, 0);
                                while ($cliente = mysqli_fetch_assoc($result_clientes)) { ?>
                                    <option value="<?php echo htmlspecialchars($cliente['id']); ?>">
                                        <?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="mesa_id" class="block mb-2">Mesa:</label>
                            <select name="mesa_id" id="mesa_id" class="w-full rounded p-2 border" required>
                                <?php 
                                mysqli_data_seek($result_mesas, 0);
                                while ($mesa = mysqli_fetch_assoc($result_mesas)) { ?>
                                    <option value="<?php echo htmlspecialchars($mesa['id']); ?>">
                                        Mesa <?php echo htmlspecialchars($mesa['numero']); ?> (Capacidad: <?php echo htmlspecialchars($mesa['capacidad']); ?>)
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="w-full custom-btn justify-center">
                            <i data-feather="check-circle" class="mr-2"></i> Confirmar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        // Initialize Feather Icons
        feather.replace();

        // Filtro de Categorías
        document.getElementById('categoria').addEventListener('change', function() {
            const categoria = this.value;
            const platillos = document.querySelectorAll('.platillo-card');
            
            platillos.forEach(platillo => {
                if (categoria === '' || platillo.dataset.categoria === categoria) {
                    platillo.style.display = 'block';
                } else {
                    platillo.style.display = 'none';
                }
            });
        });

        // Agregar platillos a la orden
        const menuGrid = document.getElementById('menu-grid');
        const orderItems = document.getElementById('order-items');
        const totalAmount = document.getElementById('total-amount');
        const orderForm = document.getElementById('orderForm');

        let orderTotal = 0;
        const orderList = {};

        menuGrid.addEventListener('click', function(e) {
            const addButton = e.target.closest('.add-to-order');
            if (addButton) {
                const id = addButton.dataset.id;
                const nombre = addButton.dataset.nombre;
                const precio = parseFloat(addButton.dataset.precio);

                // Actualizar o agregar al pedido
                if (orderList[id]) {
                    orderList[id].cantidad++;
                } else {
                    orderList[id] = {
                        nombre: nombre,
                        precio: precio,
                        cantidad: 1
                    };
                }

                updateOrderDisplay();
            }
        });

        function updateOrderDisplay() {
            orderItems.innerHTML = '';
            orderTotal = 0;

            for (const [id, item] of Object.entries(orderList)) {
                const itemTotal = item.precio * item.cantidad;
                orderTotal += itemTotal;

                const orderItemElement = document.createElement('div');
                orderItemElement.classList.add('flex', 'justify-between', 'items-center', 'mb-2');
                orderItemElement.innerHTML = `
                    <div>
                        <span>${item.nombre}</span>
                        <span class="text-gray-500 ml-2">x${item.cantidad}</span>
                    </div>
                    <div class="flex items-center">
                        <span>$${itemTotal.toFixed(2)}</span>
                        <button type="button" class="ml-2 text-red-500 remove-item" data-id="${id}">
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                        <input type="hidden" name="platillos[${id}]" value="${item.cantidad}">
                    </div>
                `;

                orderItems.appendChild(orderItemElement);
            }

            totalAmount.textContent = `$${orderTotal.toFixed(2)}`;
            feather.replace();

            // Agregar event listeners para eliminar items
            document.querySelectorAll('.remove-item').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    delete orderList[id];
                    updateOrderDisplay();
                });
            });
        }

        // Cerrar sesión
        document.getElementById('logoutBtn').addEventListener('click', function() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¿Deseas cerrar sesión?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar sesión'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../registro/logout.php';
                }
            });
        });

orderForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (Object.keys(orderList).length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Orden Vacía',
            text: 'Por favor, agregue al menos un platillo a la orden.'
        });
        return;
    }

    Swal.fire({
        title: 'Confirmar Pedido',
        text: `Total: $${orderTotal.toFixed(2)}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Confirmar Pedido'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();  // Se envía el formulario
            window.location.href = 'verPedidos.php';  // Redirige a la página de pedidos
        }
    });
});

    </script>
</body>
</html>