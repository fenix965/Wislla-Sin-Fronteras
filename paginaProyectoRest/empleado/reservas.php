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

?>

<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="../css/styleE.css">
   <title>Gestión de Reservas</title>  
</head>

<body>

<header><h1>Gestión de Reservas</h1></header>

<div class="header-buttons">
   <div class='user-greeting text-center my-4'>
      Bienvenido, <?php echo htmlspecialchars($nombre_empleado); ?>!
   </div> 
   <div class='text-center'>
      <!-- Botón de cierre de sesión -->
      <form action="" method='POST'>
         <button type='submit' name='logout' class='btn btn-danger'>Cerrar sesión</button> 
      </form> 
   </div> 
</div>

<nav><a href='indexE.php'>Inicio</a><a href='pedidos.php'>Pedidos</a><a href='reservas.php'>Reservas</a></nav>

<div class='container'>
   <!-- Formulario para agregar reserva -->
   <!-- Código del formulario aquí -->
   
   <!-- Aquí va tu formulario para agregar reservas -->
   
   <!-- Consulta para obtener las reservas existentes -->
   <?php 
   // Consultar reservas existentes
   $sql_reservas = "SELECT reservas.id AS reserva_id, reservas.fecha, reservas.hora, reservas.numero_personas,
                           clientes.nombre AS cliente_nombre, clientes.apellidos AS cliente_apellidos,
                           mesas.numero AS mesa_numero, mesas.capacidad AS mesa_capacidad
                   FROM reservas
                   LEFT JOIN clientes ON reservas.cliente_id = clientes.id
                   LEFT JOIN mesas ON reservas.mesa_id = mesas.id";
   
   $result_reservas = mysqli_query($conn, $sql_reservas);

   if (mysqli_num_rows($result_reservas) > 0) {
       echo "<table><tr><th>Reserva ID</th><th>Fecha</th><th>Hora</th><th>Personas</th><th>Cliente</th><th>Mesa</th><th>Capacidad</th></tr>";

       while ($row = mysqli_fetch_assoc($result_reservas)) {
           echo "<tr><td>" . htmlspecialchars($row['reserva_id']) . "</td><td>" . htmlspecialchars($row['fecha']) . "</td><td>" . htmlspecialchars($row['hora']) . "</td><td>" .
           htmlspecialchars($row['numero_personas']) . "</td><td>" .
           htmlspecialchars($row['cliente_nombre'] . " " . $row['cliente_apellidos']) .
           "</td><td>" .
           htmlspecialchars($row['mesa_numero']) .
           "</td><td>" .
           htmlspecialchars($row['mesa_capacidad']) .
           "</td></tr>";
       }

       echo "</table>";
   } else {
       echo "<p>No hay reservas disponibles.</p>";
   }

   mysqli_close($conn);
   ?>

</body></html>
