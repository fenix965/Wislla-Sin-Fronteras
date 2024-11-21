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

$nombre_empleado = $_SESSION['nombre']; // Obtén el nombre del empleado desde la sesión
include '../conexion.php'; // Incluye el archivo de conexión
$conn = conexion(); // Establece la conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styleE.css"> <!-- Asegúrate de que esta ruta sea correcta -->
    <title>Lista de Clientes</title>
</head>
<body>

<header>
    <h1>Lista de Clientes</h1>
</header>

<div class="header-buttons">
    <div class="user-greeting text-center my-4">
        Bienvenido, <?php echo htmlspecialchars($nombre_empleado); ?>!
    </div>
    <div class="text-center">
        <form action="" method="POST">
            <button type="submit" name="logout" class="btn btn-danger">Cerrar sesión</button>
        </form>
    </div>
</div>

<nav>
    <a href="indexE.php">Inicio</a>
    <a href="pedidos.php">Pedidos</a>
    <a href="reservas.php">Reservas</a>
</nav>

<div class="container">
    <?php
    $sql = "SELECT id, nombre, apellidos, telefono, email FROM clientes";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "<table>
                <tr>
                    <th>Cliente ID</th>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['id']) . "</td>
                    <td>" . htmlspecialchars($row['nombre']) . "</td>
                    <td>" . htmlspecialchars($row['apellidos']) . "</td>
                    <td>" . htmlspecialchars($row['telefono']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No hay clientes registrados.</p>";
    }

    mysqli_close($conn);
    ?>
</div>

</body>
</html>