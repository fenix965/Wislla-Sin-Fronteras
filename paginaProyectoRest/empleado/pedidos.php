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
    <link rel="stylesheet" href="../css/styleE.css">
    <title>Realizar Pedido</title>
</head>
<body>

<header>
    <h1>Realizar Pedido</h1>
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

<form method="post" action="">
    <label for="cliente_id">Cliente:</label>
    <select name="cliente_id" id="cliente_id" required>
        <?php while ($cliente = mysqli_fetch_assoc($result_clientes)) { ?>
            <option value="<?php echo htmlspecialchars($cliente['id']); ?>"><?php echo htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellidos']); ?></option>
        <?php } ?>
    </select>

    <label for="mesa_id">Mesa:</label>
    <select name="mesa_id" id="mesa_id" required>
        <?php while ($mesa = mysqli_fetch_assoc($result_mesas)) { ?>
            <option value="<?php echo htmlspecialchars($mesa['id']); ?>"><?php echo "Mesa " . htmlspecialchars($mesa['numero']) . " (Capacidad: " . htmlspecialchars($mesa['capacidad']) . ")"; ?></option>
        <?php } ?>
    </select>

    <label for="empleado_id">Empleado:</label>
    <select name="empleado_id" id="empleado_id" required>
        <?php while ($empleado = mysqli_fetch_assoc($result_empleados)) { ?>
            <option value="<?php echo htmlspecialchars($empleado['id']); ?>"><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellidos'] . ' (' . htmlspecialchars($empleado['puesto']) . ')'); ?></option>
        <?php } ?>
    </select>

<h2>Selecciona los Platillos</h2>

<?php while ($platillo = mysqli_fetch_assoc($result_platillos)) { ?>
        <label for="platillo_<?php echo htmlspecialchars($platillo['id']); ?>"><?php echo htmlspecialchars($platillo['nombre']); ?> (<?php echo htmlspecialchars($platillo['precio']); ?>)</label>
        <input type="number" name="platillos[<?php echo htmlspecialchars($platillo['id']); ?>]" id="platillo_<?php echo htmlspecialchars($platillo['id']); ?>" min="0" step="1" value="0" class="platillo-cantidad">
        <input type="hidden" name="precio_<?php echo htmlspecialchars($platillo['id']); ?>" value="<?php echo htmlspecialchars($platillo['precio']); ?>">
<?php } ?>

<label for="total">Total:</label>
<input type="text" name="total" id="total" readonly>

<input type="submit" name="submit_pedido" value="Realizar Pedido">
</form>

<script>
// JavaScript para calcular el total
document.querySelectorAll('.platillo-cantidad').forEach(input => {
  input.addEventListener('input', calculateTotal);
});

function calculateTotal() {
  let total = 0;
  document.querySelectorAll('.platillo-cantidad').forEach(input => {
      const platilloId = input.id.split('_')[1];
      const precio = parseFloat(document.querySelector(`input[name='precio_${platilloId}']`).value);
      total += (parseInt(input.value) * precio);
  });
  document.querySelector('#total').value = total.toFixed(2);
}

// Llamar a la función al cargar la página para calcular el total inicial
calculateTotal();
</script>

</div>

</body>
</html>
