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

// Fetch total number of clients
$total_clientes_query = "SELECT COUNT(*) as total FROM clientes";
$total_clientes_result = mysqli_query($conn, $total_clientes_query);
$total_clientes = mysqli_fetch_assoc($total_clientes_result)['total'];

// Preparar filtros
$filtro_nombre = isset($_GET['nombre']) ? mysqli_real_escape_string($conn, $_GET['nombre']) : '';
$filtro_apellidos = isset($_GET['apellidos']) ? mysqli_real_escape_string($conn, $_GET['apellidos']) : '';
$filtro_telefono = isset($_GET['telefono']) ? mysqli_real_escape_string($conn, $_GET['telefono']) : '';
$filtro_email = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';

// Construir consulta dinámica con filtros
$sql = "SELECT id, nombre, apellidos, telefono, email FROM clientes WHERE 1=1";

if (!empty($filtro_nombre)) {
    $sql .= " AND nombre LIKE '%$filtro_nombre%'";
}
if (!empty($filtro_apellidos)) {
    $sql .= " AND apellidos LIKE '%$filtro_apellidos%'";
}
if (!empty($filtro_telefono)) {
    $sql .= " AND telefono LIKE '%$filtro_telefono%'";
}
if (!empty($filtro_email)) {
    $sql .= " AND email LIKE '%$filtro_email%'";
}

$result = mysqli_query($conn, $sql);
$total_filtrados = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Clientes</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
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

        .stat-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            border: 1px solid var(--soft-brown);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: scale(1.03);
        }

        .stat-card svg {
            color: var(--primary-brown);
            width: 3rem;
            height: 3rem;
        }

        .custom-table thead {
            background: linear-gradient(to right, var(--primary-brown), #b05406);
            color: white;
        }

        .custom-table tr:hover {
            background-color: rgba(141, 64, 4, 0.05);
        }

        .filter-section {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            padding: 1.5rem;
            border: 1px solid var(--soft-brown);
        }

        .filter-section input {
            border: 1px solid var(--border-light);
            background-color: var(--input-bg);
            color: var(--dark-brown);
            border-radius: 0.5rem;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .filter-section input:focus {
            border-color: var(--primary-brown);
            outline: none;
            box-shadow: 0 0 0 3px rgba(141, 64, 4, 0.1);
        }

        .nav-item {
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body class="antialiased">
    <div class="container mx-auto px-4 py-8">
        <header class="custom-header shadow-md rounded-lg mb-6 p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold flex items-center" style="color: var(--primary-brown);">
                    <i data-feather="users" class="mr-3"></i>
                    Lista de Clientes
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

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="stat-card nav-item">
                <i data-feather="users"></i>
                <div>
                    <h3 class="text-xl font-semibold" style="color: var(--primary-brown);"><?php echo $total_clientes; ?></h3>
                    <p class="text-gray-600">Clientes Registrados</p>
                </div>
            </div>
            <div class="stat-card nav-item">
                <i data-feather="list"></i>
                <div>
                    <h3 class="text-xl font-semibold" style="color: var(--primary-brown);">5</h3>
                    <p class="text-gray-600">Categorías de Clientes</p>
                </div>
            </div>
            <div class="stat-card nav-item">
                <i data-feather="trending-up"></i>
                <div>
                    <h3 class="text-xl font-semibold" style="color: var(--primary-brown);">+12%</h3>
                    <p class="text-gray-600">Crecimiento este mes</p>
                </div>
            </div>
        </div>

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
                <button class="custom-btn nav-item">
                    <i data-feather="plus"></i>
                    Añadir Cliente
                </button>
            </div>
        </nav>

        <div class="filter-section mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <input type="text" name="nombre" placeholder="Nombre" 
                        value="<?php echo htmlspecialchars($filtro_nombre); ?>" 
                        class="w-full">
                </div>
                <div class="relative">
                    <input type="text" name="apellidos" placeholder="Apellidos" 
                        value="<?php echo htmlspecialchars($filtro_apellidos); ?>" 
                        class="w-full">
                </div>
                <div class="relative">
                    <input type="text" name="telefono" placeholder="Teléfono" 
                        value="<?php echo htmlspecialchars($filtro_telefono); ?>" 
                        class="w-full">
                </div>
                <div class="relative">
                    <input type="text" name="email" placeholder="Email" 
                        value="<?php echo htmlspecialchars($filtro_email); ?>" 
                        class="w-full">
                </div>
                <div class="col-span-full flex space-x-4">
                    <button type="submit" class="custom-btn">
                        <i data-feather="search"></i> Buscar
                    </button>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="custom-btn bg-gray-500">
                        <i data-feather="refresh-cw"></i> Limpiar Filtros
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-soft-brown">
            <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                <p class="text-gray-600">
                    Mostrando <?php echo $total_filtrados; ?> de <?php echo $total_clientes; ?> clientes
                </p>
            </div>

            <?php
            if ($total_filtrados > 0) {
                echo "<div class='overflow-x-auto'>
                        <table class='w-full custom-table'>
                            <thead>
                                <tr>
                                    <th class='p-3 text-left text-xs font-medium uppercase tracking-wider'>
                                        <i data-feather='hash' class='inline-block mr-2'></i>ID
                                    </th>
                                    <th class='p-3 text-left text-xs font-medium uppercase tracking-wider'>
                                        <i data-feather='user' class='inline-block mr-2'></i>Nombre
                                    </th>
                                    <th class='p-3 text-left text-xs font-medium uppercase tracking-wider'>
                                        <i data-feather='user' class='inline-block mr-2'></i>Apellidos
                                    </th>
                                    <th class='p-3 text-left text-xs font-medium uppercase tracking-wider'>
                                        <i data-feather='phone' class='inline-block mr-2'></i>Teléfono
                                    </th>
                                    <th class='p-3 text-left text-xs font-medium uppercase tracking-wider'>
                                        <i data-feather='mail' class='inline-block mr-2'></i>Email
                                    </th>
                                </tr>
                            </thead>
                            <tbody class='bg-white divide-y divide-gray-200'>";

                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr class='hover:bg-gray-50'>
                            <td class='p-3 whitespace-nowrap'>" . htmlspecialchars($row['id']) . "</td>
                            <td class='p-3 whitespace-nowrap'>" . htmlspecialchars($row['nombre']) . "</td>
                            <td class='p-3 whitespace-nowrap'>" . htmlspecialchars($row['apellidos']) . "</td>
                            <td class='p-3 whitespace-nowrap'>" . htmlspecialchars($row['telefono']) . "</td>
                            <td class='p-3 whitespace-nowrap'>" . htmlspecialchars($row['email']) . "</td>
                          </tr>";
                }

                echo "</tbody></table></div>";
            } else {
                echo "<div class='p-6 text-center' style='color: var(--primary-brown);'>
                        <i data-feather='users-x' class='mx-auto mb-4' style='width: 4rem; height: 4rem;'></i>
                        <p class='text-xl'>No se encontraron clientes con estos filtros.</p>
                    </div>";
            }

            mysqli_close($conn);
            ?>
        </div>
    </div>

    <script>
        feather.replace();
    </script>
</body>
</html>