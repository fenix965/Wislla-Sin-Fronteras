<?php include '../conexion.php'; 
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wislla Sin Fronteras - Experiencia Gastronómica Boliviana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/22b0eb3ac8.js" crossorigin="anonymous"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="../imagenes/WisllaLogo.jpg" alt="Logo de Wislla" style="max-width: 100px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#inicio">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#sobreNosotros">Sobre Nosotros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu.php">Menú</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#ubicacion">Ubicación</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a>
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

    <section class="hero-section" id="inicio">
        <div class="container text-center">
            <h1 class="display-3 mb-4">Wislla Sin Fronteras</h1>
            <p class="lead mb-4">Una experiencia gastronómica única que fusiona la tradición boliviana con la innovación contemporánea</p>
            <a href="reservas.php" class="btn btn-warning btn-lg">Reserva Ahora</a>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-utensils fa-3x text-warning mb-3"></i>
                            <h3>Cocina Tradicional</h3>
                            <p>Sabores auténticos de Bolivia con un toque contemporáneo</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-leaf fa-3x text-warning mb-3"></i>
                            <h3>Ingredientes Frescos</h3>
                            <p>Trabajamos con productores locales para garantizar la mejor calidad</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="fas fa-wine-glass-alt fa-3x text-warning mb-3"></i>
                            <h3>Ambiente Único</h3>
                            <p>Un espacio acogedor que refleja nuestra rica cultura</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sobre Nosotros -->
    <section id="sobreNosotros" class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="display-4 mb-4">Nuestra Historia</h2>
                    <p class="lead">En Wislla Sin Fronteras, cada plato cuenta una historia de tradición y innovación.</p>
                    <p>Nos dedicamos a servir platillos hechos con ingredientes locales de la más alta calidad, trabajando con proveedores que nos proporcionan los mejores productos frescos de cada temporada.</p>
                    <div class="mt-4">
                        <h4>Horario de Atención:</h4>
                        <p class="mb-2">Lunes a Viernes: 9:00 AM - 9:00 PM</p>
                        <p>Sábado y Domingo: 10:00 AM - 10:00 PM</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://www.abasturhub.com/img/blog/interiorismo-restaurantero---interiorismo-restaurantero.jpg" alt="Interior del restaurante" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- Ubicación -->
    <section id="ubicacion" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h2 class="mb-4">Encuentranos</h2>
                        <p><i class="fas fa-map-marker-alt text-warning me-2"></i> Carril Principal Rio Abajo, La Paz, Bolivia</p>
                        <p><i class="fas fa-phone text-warning me-2"></i> (+591) 74859633</p>
                        <p><i class="fas fa-envelope text-warning me-2"></i> info@wisllasinfronteras.com</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1654.7886464435867!2d-68.07589360253611!3d-16.59013531651838!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x915f27a459674057%3A0x5f510307532d4502!2sWislla%20sin%20fronteras!5e0!3m2!1ses-419!2sbo!4v1730853350461!5m2!1ses-419!2sbo" 
                            class="w-100 rounded shadow" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen 
                            loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5>Wislla Sin Fronteras</h5>
                    <p>Fusionando tradición e innovación en cada plato.</p>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5>Contacto Directo</h5>
                    <p>Reservas: (+591) 74859633</p>
                    <p>Email: info@wisllasinfronteras.com</p>
                </div>
                <div class="col-lg-4">
                    <h5>Síguenos</h5>
                    <div class="social-links mt-3">
                        <a href="https://www.pinterest.com/wisllasinfronteras/" class="text-white me-3"><i class="fa-brands fa-pinterest"></i></a>
                        <a href="https://www.facebook.com/WisllaSinFronteras/" class="text-white me-3"><i class="fa-brands fa-facebook"></i></a>
                        <a href="https://www.youtube.com/channel/WisllaSinFronteras" class="text-white me-3"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://www.instagram.com/wisllasinfronteras/" class="text-white me-3"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://wa.me/591123456789" class="text-white"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cambiar el fondo del navbar al hacer scroll
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                document.querySelector('.navbar').classList.add('scrolled');
            } else {
                document.querySelector('.navbar').classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>