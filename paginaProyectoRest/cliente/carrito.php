<?php
session_start();
if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Verifica si el usuario está autenticado
$usuarioAutenticado = isset($_SESSION['cliente_id']) || isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - Wislla Sin Fronteras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styleCarrito.css">
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
                    <li class="nav-item"><a class="nav-link" href="paginaInfo.php">Sobre Nosotros</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menú</a></li>
                    <li class="nav-item">
                        <?php if ($usuarioAutenticado): ?>
                            <span class="nav-link">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</span>
                        <?php endif; ?>
                    </li>
                    <li class="nav-item">
                        <?php if ($usuarioAutenticado): ?>
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
        <?php if (!$usuarioAutenticado): ?>
            <div class="alert alert-warning" role="alert">
                Debes iniciar sesión para finalizar tu compra. <a href="../registro/login.php" class="alert-link">Iniciar sesión</a>
            </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['carrito'])): ?>
            <div class="cart-items">
                <?php
                $total = 0;
                foreach ($_SESSION['carrito'] as $index => $item):
                    $cantidad = isset($item['cantidad']) ? $item['cantidad'] : 1;
                    $subtotal = $item['precio'] * $cantidad;
                    $total += $subtotal;
                ?>
                    <div class="cart-item">
    <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" class="cart-item-image">
    <div class="cart-item-details">
        <h3 class="cart-item-title"><?php echo htmlspecialchars($item['nombre']); ?></h3>
        
        <!-- Campo de cantidad mejorado -->
        <div class="quantity-wrapper d-flex align-items-center">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(<?php echo $index; ?>, <?php echo $cantidad - 1; ?>)">
                <i class="fas fa-minus"></i>
            </button>
            <input type="number" class="form-control quantity-input" value="<?php echo $cantidad; ?>" min="1" onchange="updateQuantity(<?php echo $index; ?>, this.value)">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity(<?php echo $index; ?>, <?php echo $cantidad + 1; ?>)">
                <i class="fas fa-plus"></i>
            </button>
        </div>

        <span class="cart-item-price">Bs <?php echo number_format($subtotal, 2); ?></span>
    </div>
    <button class="remove-btn ms-3" onclick="removeItem(<?php echo $index; ?>)">
        <i class="fas fa-trash"></i>
    </button>
</div>

                <?php endforeach; ?>
            </div>
            <div class="cart-summary">
                <h4>Resumen del Pedido</h4>
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <span>Bs <?php echo number_format($total, 2); ?></span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <strong>Total:</strong>
                    <strong>Bs <?php echo number_format($total, 2); ?></strong>
                </div>
                <form action="finalizar_compra.php" method="POST" id="finalizarCompraForm">
                    <button type="submit" class="checkout-btn btn btn-success mt-3" <?php echo !$usuarioAutenticado ? 'disabled' : ''; ?>>Finalizar Compra</button>
                </form>
            </div>
        <?php else: ?>
            <div class="empty-cart">
                <h3>Tu carrito está vacío</h3>
                <a href="menu.php" class="btn btn-primary mt-3">Ver Menú</a>
            </div>
        <?php endif; ?>
        </div>
    </div>

    <div id="successMessage" class="alert alert-success position-fixed top-0 end-0 mt-3 me-3" role="alert" style="display: none; z-index: 1050;">
        ¡Gracias por su compra! Su pedido ha sido procesado con éxito.
    </div>

    <script>
    // Función para actualizar la cantidad de un producto en el carrito
    function updateQuantity(index, quantity) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'actualizar_cantidad.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                location.reload();
            }
        };
        xhr.send(`index=${index}&cantidad=${quantity}`);
    }

    // Función para eliminar un producto del carrito
    function removeItem(index) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'remover_producto.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                location.reload();
            }
        };
        xhr.send(`index=${index}`);
    }

    // Manejo de finalización de compra
    const form = document.getElementById('finalizarCompraForm');
    form.addEventListener('submit', function (event) {
        event.preventDefault(); // Prevenir el envío tradicional del formulario

        const formData = new FormData(form);

        fetch('finalizar_compra.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json()) // Espera una respuesta JSON
        .then(data => {
            if (data.success) {
                // Muestra el mensaje de éxito
                document.getElementById('successMessage').style.display = 'block';
                setTimeout(() => {
                    document.getElementById('successMessage').style.display = 'none';
                    window.location.href = 'menu.php'; // Redirige al menú después de unos segundos
                }, 5000);
            } else {
                // Muestra el mensaje de error si la compra no fue exitosa
                alert("Error al procesar la compra: " + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error); // Muestra un mensaje en caso de error
            alert("Hubo un error al procesar la compra.");
        });
    });
</script>

</body>
</html>
