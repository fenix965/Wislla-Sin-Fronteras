<?php
session_start();
include '../conexion.php';

$conn = conexion();

if (!$conn) {
    die("Error de conexión: " . mysqli_connect_error());
}

$query = "SELECT * FROM platillos"; 
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - Wislla Sin Fronteras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/22b0eb3ac8.js" crossorigin="anonymous"></script>
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla" style="max-width: 100px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="paginaInfo.php">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="menu.php">Menú</a>
                    </li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['nombre'])): ?>
                            <span class="nav-link">Bienvenido, <?php echo $_SESSION['nombre']; ?>!</span>
                        <?php endif; ?>
                    </li>
                    <li class="nav-item">
                        <?php if (isset($_SESSION['nombre'])): ?>
                            <form action="../registro/logout.php" method="POST" class="d-inline">
    <button type="submit" class="btn btn-outline-light btn-sm">Cerrar sesión</button>
</form>

                        <?php else: ?>
                            <a href="../registro/login.php" class="btn btn-outline-light btn-sm">Iniciar sesión</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="menu-hero">
        <div class="container">
            <h1 class="display-4 mb-4">Nuestro Menú</h1>
            <p class="lead">Descubre nuestra selección de platillos tradicionales con un toque contemporáneo</p>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="category-filter">
                <h4 class="mb-3">Categorías</h4>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge-category">Todos</span>
                    <span class="badge-category">Entradas</span>
                    <span class="badge-category">Platos Principales</span>
                    <span class="badge-category">Postres</span>
                    <span class="badge-category">Bebidas</span>
                </div>
            </div>

            <div class="row">
                <?php while ($row = mysqli_fetch_assoc($result)) { 
    $imagen = !empty($row['imagen']) ? 'data:image/jpeg;base64,' . base64_encode($row['imagen']) : 'imagenes/default-dish.jpg';
?>
    <div class="col-md-6 col-lg-4">
        <div class="menu-card">
            <img src="<?php echo $imagen; ?>" 
                 alt="<?php echo $row['nombre']; ?>" 
                 class="card-img-top">
            <div class="menu-card-body">
                <h3 class="menu-card-title"><?php echo $row['nombre']; ?></h3>
                <p class="text-muted"><?php echo $row['descripcion']; ?></p>
                <div>
                    <span class="menu-card-price">Bs <?php echo $row['precio']; ?></span><br>
                    <hr>
                    <button class="add-to-cart-btn" 
                            onclick="agregarAlCarrito(<?php echo $row['id']; ?>, '<?php echo $row['nombre']; ?>', <?php echo $row['precio']; ?>, '<?php echo $imagen; ?>')">
                        <i class="fas fa-plus me-2"></i>Agregar al Carrito
                    </button>

                </div>
            </div>
        </div>
    </div>
<?php } ?>
            </div>
        </div>
    </section>

    <button class="cart-floating-btn" onclick="mostrarCarrito()">
        <i class="fas fa-shopping-cart"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function agregarAlCarrito(id, nombre, precio, imagen) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "agregar_carrito.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    var datos = "id=" + id + "&nombre=" + encodeURIComponent(nombre) + "&precio=" + precio + "&imagen=" + encodeURIComponent(imagen);

    xhr.onload = function() {
        if (xhr.status == 200) {
            var carrito = JSON.parse(xhr.responseText);
            console.log(carrito);
            mostrarNotificacion('Producto agregado al carrito');
        }
    };
    xhr.send(datos);
}


        function mostrarNotificacion(mensaje) {
            const notificacion = document.createElement('div');
            notificacion.style.position = 'fixed';
            notificacion.style.top = '20px';
            notificacion.style.right = '20px';
            notificacion.style.backgroundColor = '#28a745';
            notificacion.style.color = 'white';
            notificacion.style.padding = '15px 25px';
            notificacion.style.borderRadius = '5px';
            notificacion.style.zIndex = '1000';
            notificacion.textContent = mensaje;

            document.body.appendChild(notificacion);

            setTimeout(() => {
                notificacion.remove();
            }, 3000);
        }

        function mostrarCarrito() {
            window.location.href = 'carrito.php';
        }
    </script>
</body>
</html>