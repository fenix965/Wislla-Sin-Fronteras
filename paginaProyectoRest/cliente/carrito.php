<?php
session_start();
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
?>
 
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Wislla Sin Fronteras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleCarrito.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/22b0eb3ac8.js" crossorigin="anonymous"></script>
    <style>
        /* Estilos para la notificación flotante */
        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1000;
            width: 250px;
            color: #333;
        }
 
        .custom-notification p {
            margin: 0 0 10px;
        }
 
        .custom-notification .btn-confirm,
        .custom-notification .btn-cancel {
            margin: 5px;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
 
        .custom-notification .btn-confirm {
            background-color: #4CAF50;
            color: white;
        }
 
        .custom-notification .btn-cancel {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
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
                        <a class="nav-link" href="menu.php">Menú</a>
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
 
    <section class="cart-hero">
        <div class="container">
            <h1 class="display-4">Tu Carrito</h1>
            <p class="lead">Revisa y confirma tu pedido</p>
        </div>
    </section>
 
    <div class="container">
        <div class="cart-container">
            <?php if (!empty($_SESSION['carrito'])): ?>
                <div class="cart-items">
                    <?php
                    $total = 0;
                    foreach ($_SESSION['carrito'] as $index => $item):
                        $total += $item['precio'];
                    ?>
                        <div class="cart-item">
                            <img src="<?php echo $item['imagen']; ?>" alt="<?php echo $item['nombre']; ?>" class="cart-item-image">
                            <div class="cart-item-details">
                                <h3 class="cart-item-title"><?php echo $item['nombre']; ?></h3>
                                <span class="cart-item-price">Bs <?php echo number_format($item['precio'], 2); ?></span>
                            </div>
                            <button class="remove-btn ms-3" onclick="removeItem(<?php echo $index; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
 
                <div class="cart-summary">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Resumen del Pedido</h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>Bs <?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Impuestos (13%):</span>
                                <span>Bs <?php echo number_format($total * 0.13, 2); ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong>Bs <?php echo number_format($total * 1.13, 2); ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form action="finalizar_compra.php" method="POST">
                                <button type="submit" class="checkout-btn">
                                    <i class="fas fa-lock me-2"></i>Finalizar Compra
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-cart">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>Tu carrito está vacío</h3>
                    <p>¿Por qué no explores nuestro menú y añades algunos platillos deliciosos?</p>
                    <a href="menu.php" class="btn btn-primary mt-3">Ver Menú</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function removeItem(index) {
            const notification = document.createElement('div');
            notification.className = 'custom-notification';
            notification.innerHTML = `
                <p>¿Estás seguro de que deseas eliminar este item?</p>
                <button class="btn-confirm" onclick="confirmRemove(${index})">Aceptar</button>
                <button class="btn-cancel" onclick="closeNotification()">Cancelar</button>
            `;
            document.body.appendChild(notification);
        }
 
        function confirmRemove(index) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'eliminar_item.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send(`index=${index}`);
            closeNotification();
        }
 
        function closeNotification() {
            const notification = document.querySelector('.custom-notification');
            if (notification) {
                notification.remove();
            }
        }
    </script>
</body>
</html>