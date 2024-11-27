<?php
session_start();

if (!isset($_SESSION['nombre'])) {
   header('Location: ../registro/login.php');
   exit();
}

if (isset($_POST['logout'])) {
   session_unset();
   session_destroy();
   header('Location: ../registro/login.php');
   exit();
}

$nombre_empleado = $_SESSION['nombre'];
include '../conexion.php';
$conn = conexion();

// Manejar la actualización de asistencia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_asistencia'])) {
    $reserva_id = $_POST['reserva_id'];
    $asistencia = $_POST['asistencia'];

    $stmt = $conn->prepare("UPDATE reservas SET cliente_asistio = ? WHERE id = ?");
    $stmt->bind_param("ii", $asistencia, $reserva_id);
    
    if ($stmt->execute()) {
        $_SESSION['mensaje'] = $asistencia == 1 
            ? "Se marcó que el cliente ASISTIÓ a la reserva." 
            : "Se marcó que el cliente NO ASISTIÓ a la reserva.";
    } else {
        $_SESSION['error'] = "Error al actualizar la asistencia.";
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Estadísticas de reservas
$sql_stats = "
SELECT 
    COUNT(*) as total_reservas,
    SUM(CASE WHEN cliente_asistio = 1 THEN 1 ELSE 0 END) as asistieron,
    SUM(CASE WHEN cliente_asistio = 0 THEN 1 ELSE 0 END) as no_asistieron,
    SUM(CASE WHEN cliente_asistio IS NULL THEN 1 ELSE 0 END) as pendientes,
    ROUND(SUM(CASE WHEN cliente_asistio = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(*), 2) as porcentaje_asistencia
FROM reservas";

$result_stats = mysqli_query($conn, $sql_stats);
$stats = mysqli_fetch_assoc($result_stats);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .nav-item {
            transition: all 0.3s ease;
        }

        .nav-item:hover {
            transform: translateY(-3px);
        }

        .reserva-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            padding: 1rem;
            border: 1px solid var(--soft-brown);
            margin-bottom: 1rem;
        }

        .reserva-asistio-si {
            background-color: #dff0d8;
            border-left: 4px solid #2d4a3e;
        }

        .reserva-asistio-no {
            background-color: #f2dede;
            border-left: 4px solid #a94442;
        }

        .reserva-asistio-pendiente {
            background-color: #fcf8e3;
            border-left: 4px solid #8a6d3b;
        }

        .stat-card {
            background-color: white;
            border-radius: 1rem;
            box-shadow: 0 6px 12px rgba(0,0,0,0.08);
            padding: 1.5rem;
            border: 1px solid var(--soft-brown);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body class="antialiased">
    <div class="container mx-auto px-4 py-8">
        <header class="custom-header shadow-md rounded-lg mb-6 p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold flex items-center" style="color: var(--primary-brown);">
                    <i data-feather="calendar" class="mr-3"></i>
                    Gestión de Reservas
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
            </div>
        </nav>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="stat-card">
                <div>
                    <h3 class="text-xl font-semibold" style="color: var(--primary-brown);">Total Reservas</h3>
                    <p class="text-3xl font-bold"><?php echo $stats['total_reservas']; ?></p>
                </div>
                <i data-feather="calendar" class="text-primary-brown" style="color: var(--primary-brown);"></i>
            </div>
            <div class="stat-card">
                <div>
                    <h3 class="text-xl font-semibold" style="color: var(--success-green);">Asistieron</h3>
                    <p class="text-3xl font-bold"><?php echo $stats['asistieron']; ?></p>
                </div>
                <i data-feather="check-circle" class="text-green-500"></i>
            </div>
            <div class="stat-card">
                <div>
                    <h3 class="text-xl font-semibold" style="color: #a94442;">No Asistieron</h3>
                    <p class="text-3xl font-bold"><?php echo $stats['no_asistieron']; ?></p>
                </div>
                <i data-feather="x-circle" class="text-red-500"></i>
            </div>
        </div>

        <!-- Gráfico de Pastel -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="stat-card">
                <canvas id="reservasChart"></canvas>
            </div>
            <div class="stat-card">
                <div class="w-full">
                    <h3 class="text-xl font-semibold mb-4" style="color: var(--primary-brown);">Desglose de Reservas</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span>Porcentaje de Asistencia</span>
                            <span class="font-bold" style="color: var(--success-green);"><?php echo $stats['porcentaje_asistencia']; ?>%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Pendientes</span>
                            <span class="font-bold text-yellow-600"><?php echo $stats['pendientes']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reservas Existentes -->
        <div class="container mx-auto">
            <?php 
            // Mostrar mensajes de éxito o error
            if (isset($_SESSION['mensaje'])) {
                echo "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4' role='alert'>" 
                     . htmlspecialchars($_SESSION['mensaje']) . 
                     "</div>";
                unset($_SESSION['mensaje']);
            }
            if (isset($_SESSION['error'])) {
                echo "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4' role='alert'>" 
                     . htmlspecialchars($_SESSION['error']) . 
                     "</div>";
                unset($_SESSION['error']);
            }
            ?>

            <?php 
            $sql_reservas = "SELECT 
            reservas.id AS reserva_id, 
            reservas.fecha, 
            reservas.hora, 
            reservas.numero_personas,
            reservas.cliente_asistio,
            clientes.nombre AS cliente_nombre, 
            clientes.apellidos AS cliente_apellidos,
            mesas.numero AS mesa_numero, 
            mesas.capacidad AS mesa_capacidad
            FROM reservas
            LEFT JOIN clientes ON reservas.cliente_id = clientes.id
            LEFT JOIN mesas ON reservas.mesa_id = mesas.id
            ORDER BY reservas.fecha, reservas.hora";
            
            $result_reservas = mysqli_query($conn, $sql_reservas);

            if (mysqli_num_rows($result_reservas) > 0) {
                while ($row = mysqli_fetch_assoc($result_reservas)) {
                    $estado_clase = $row['cliente_asistio'] === null 
                        ? 'reserva-asistio-pendiente' 
                        : ($row['cliente_asistio'] == 1 
                            ? 'reserva-asistio-si' 
                            : 'reserva-asistio-no');

                    $estado_texto = $row['cliente_asistio'] === null 
                        ? 'Pendiente' 
                        : ($row['cliente_asistio'] == 1 
                            ? 'Asistió' 
                            : 'No Asistió');

                    echo "<div class='reserva-card {$estado_clase}'>";
                    echo "<div class='flex justify-between items-center mb-4'>";
                    echo "<div class='flex items-center'>";
                    echo "<i data-feather='calendar' class='mr-2 text-" . ($row['cliente_asistio'] === null ? 'yellow' : ($row['cliente_asistio'] == 1 ? 'green' : 'red')) . "-500'></i>";
                    echo "<h3 class='text-xl font-semibold'>" . htmlspecialchars($row['cliente_nombre'] . " " . $row['cliente_apellidos']) . "</h3>";
                    echo "</div>";
                    echo "<span class='text-sm font-medium'>" . htmlspecialchars($row['fecha'] . ' - ' . $row['hora']) . "</span>";
                    echo "</div>";
                    
                    echo "<div class='grid grid-cols-3 gap-4'>";
                    echo "<div><strong>Personas:</strong> " . htmlspecialchars($row['numero_personas']) . "</div>";
                    echo "<div><strong>Mesa:</strong> " . htmlspecialchars($row['mesa_numero']) . "</div>";
                    
                    echo "<div class='text-right'>";
                    echo "<form method='POST' class='inline-block'>";
                    echo "<input type='hidden' name='reserva_id' value='" . $row['reserva_id'] . "'>";
                    echo "<select name='asistencia' onchange='this.form.submit()' class='form-select rounded p-1'>";
                    echo "<option value='' " . ($row['cliente_asistio'] === null ? 'selected' : '') . ">Pendiente</option>";
                    echo "<option value='1' " . ($row['cliente_asistio'] == 1 ? 'selected' : '') . ">Asistió</option>";
                    echo "<option value='0' " . ($row['cliente_asistio'] == 0 ? 'selected' : '') . ">No Asistió</option>";
                    echo "</select>";
                    echo "<input type='hidden' name='marcar_asistencia' value='1'>";
                    echo "</form>";
                    echo "</div>";
                    
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<div class='text-center text-gray-500 py-8'>";
                echo "<i data-feather='calendar-off' class='mx-auto mb-4 text-primary-brown' style='width: 64px; height: 64px;'></i>";
                echo "<p class='text-xl'>No hay reservas para hoy.</p>";
                echo "</div>";
            }

            mysqli_close($conn);
            ?>
        </div>
    </div>

    <script>
        // Initialize Feather Icons
        feather.replace();
    </script>
    <script>
        feather.replace();

        var ctx = document.getElementById('reservasChart').getContext('2d');
var reservasChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Asistieron', 'No Asistieron', 'Pendientes'],
        datasets: [{
            data: [
                <?php echo $stats['asistieron']; ?>, 
                <?php echo $stats['no_asistieron']; ?>, 
                <?php echo $stats['pendientes']; ?>
            ],
            backgroundColor: [
                '#48bb78',  // Green for attended
                '#f56565',  // Red for missed
                '#ecc94b'   // Yellow for pending
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
            padding: 0
        },
        plugins: {
            legend: {
                display: false  // Hide legend completely
            },
            title: {
                display: false
            },
            tooltip: {
                enabled: true,
                bodyFont: {
                    size: 10
                },
                callbacks: {
                    label: function(context) {
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let value = context.parsed;
                        let percentage = ((value / total) * 100).toFixed(2);
                        return `${context.label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        elements: {
            arc: {
                borderWidth: 0  // Remove border between segments
            }
        }
    }
});
    </script>
</body>
</html>