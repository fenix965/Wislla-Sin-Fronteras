# Sistema de Gestión para el Restaurante "Wislla Sin Fronteras"

Este proyecto es un sistema de gestión para un restaurante que permite controlar reservas, pedidos, mesas, inventarios, proveedores, clientes, empleados y platillos. El sistema busca optimizar la operatividad del restaurante, mejorar la organización y facilitar el acceso a información importante en tiempo real.

## Tabla de Contenidos
- [Objetivo](#objetivo)
- [Descripción del Sistema](#descripción-del-sistema)
- [Modelo de Datos](#modelo-de-datos)
  - [Tablas](#tablas)
  - [Relaciones entre Tablas](#relaciones-entre-tablas)
- [Funcionalidades](#funcionalidades)
- [Tecnologías Utilizadas](#tecnologías-utilizadas)
- [Instrucciones de Instalación](#instrucciones-de-instalación)
- [Uso](#uso)
- [Contribución](#contribución)
- [Licencia](#licencia)

## Objetivo
El objetivo del sistema es proporcionar una herramienta integral que facilite la gestión del restaurante, cubriendo aspectos como el control de inventarios, la administración de pedidos y reservas, la gestión de empleados y proveedores, y el seguimiento de clientes.

## Descripción del Sistema
El sistema está diseñado para ser intuitivo y accesible para los empleados del restaurante, ayudando a:
- Gestionar pedidos y reservas.
- Asignar mesas a los clientes.
- Llevar un control del inventario y proveedores.
- Realizar seguimiento de los clientes.
- Gestionar empleados y sus roles dentro del restaurante.

Este sistema permite optimizar las operaciones diarias y mejorar el flujo de trabajo dentro del restaurante.

## Integrantes del Proyecto

| **Nombre Completo**               | **Rol**                         |
|------------------------------------|----------------------------------|
| Marces Pizza Mauricio Andres      | Coach                           |
| Mamani Huacoto Alexandra Yulye    | Analista de Calidad de Software |
| Balderrama Delgado Maciel         | FrontEnd                        |
| Alfaro Delgado Horacio            | Backend                         |

---

## Tecnologías Utilizadas

| **Tecnología**  | **Descripción**                                     | **Logo** |
|------------------|-----------------------------------------------------|----------|
| PHP             | Lenguaje de programación para el desarrollo backend | ![PHP](https://upload.wikimedia.org/wikipedia/commons/2/27/PHP-logo.svg) |
| HTML            | Lenguaje de marcado para la estructura web          | ![HTML](https://upload.wikimedia.org/wikipedia/commons/6/61/HTML5_logo_and_wordmark.svg) |
| CSS             | Lenguaje de diseño para estilizar la página web     | ![CSS](https://upload.wikimedia.org/wikipedia/commons/d/d5/CSS3_logo_and_wordmark.svg) |
| GitHub          | Repositorio para control de versiones               | ![GitHub](https://upload.wikimedia.org/wikipedia/commons/9/91/Octicons-mark-github.svg) |
| Visual Studio Code | Editor de código fuente                         | ![VSCode](https://upload.wikimedia.org/wikipedia/commons/9/9a/Visual_Studio_Code_1.35_icon.svg) |
| Office 365      | Suite de herramientas para gestión y colaboración   | ![Office 365](https://upload.wikimedia.org/wikipedia/commons/f/f4/Microsoft_Office_2013_logo.svg) |
| Lucidchart      | Herramienta para diseño de diagramas                | ![Lucidchart](https://upload.wikimedia.org/wikipedia/commons/e/e7/Lucidchart_logo.svg) |
| PlantUML        | Generación de diagramas UML mediante texto          | ![PlantUML](https://upload.wikimedia.org/wikipedia/commons/8/8f/PlantUML-logo.png) |
| XAMPP           | Servidor local para desarrollo web                  | ![XAMPP](https://upload.wikimedia.org/wikipedia/commons/7/71/XAMPP_logo.svg) |
| phpMyAdmin      | Herramienta de gestión para bases de datos MySQL    | ![phpMyAdmin](https://upload.wikimedia.org/wikipedia/commons/2/29/PhpMyAdmin_logo.png) |

---

## Modelo de Datos
El modelo de datos se basa en una estructura de tablas en una base de datos relacional. Las principales tablas son:

### Tablas

1. **Usuarios**
   - Representa a todos los usuarios registrados en el sistema.
   - Atributos:
     - `id`: Identificador único del usuario.
     - `nombre_usuario`: Nombre del usuario.
     - `contraseña`: Contraseña del usuario.
     - `rol`: Rol del usuario (cliente, administrador, cocinero, mesero).

2. **Clientes**
   - Almacena la información de los clientes.
   - Atributos:
     - `id`: Identificador único del cliente.
     - `usuario_id`: Identificador del usuario asociado.
     - `nombre`, `apellidos`, `telefono`, `email`, `direccion`: Información del cliente.

3. **Empleados**
   - Contiene los datos de los empleados.
   - Atributos:
     - `id`: Identificador único del empleado.
     - `usuario_id`: Identificador del usuario asociado.
     - `nombre`, `apellidos`, `puesto`, `salario`, `telefono`, `email`: Información del empleado y su posición en el restaurante.

4. **Mesas**
   - Representa las mesas del restaurante.
   - Atributos:
     - `id`: Identificador único de la mesa.
     - `numero`: Número de la mesa.
     - `capacidad`: Capacidad de personas.
     - `estado`: Estado de disponibilidad de la mesa.
     - `ubicacion`: Ubicación de la mesa en el restaurante.

5. **Reservas**
   - Almacena las reservas realizadas por los clientes.
   - Atributos:
     - `id`: Identificador único de la reserva.
     - `cliente_id`, `mesa_id`: Referencias a cliente y mesa.
     - `fecha`, `hora`, `numero_personas`: Información sobre la reserva.

6. **Pedidos**
   - Contiene los pedidos realizados por los clientes.
   - Atributos:
     - `id`: Identificador único del pedido.
     - `cliente_id`, `mesa_id`, `empleado_id`: Referencias a cliente, mesa y empleado.
     - `fecha`, `estado`, `total`: Detalles del pedido.

7. **Productos**
   - Almacena los productos que el restaurante ofrece o utiliza.
   - Atributos:
     - `id`: Identificador único del producto.
     - `nombre`, `precio`, `categoria`: Información sobre el producto.

8. **Inventario**
   - Controla las existencias de productos en el restaurante.
   - Atributos:
     - `id`: Identificador único del registro.
     - `producto_id`: Referencia al producto.
     - `cantidad`: Cantidad disponible.
     - `fechaIngreso`: Fecha de ingreso del producto al inventario.

9. **Proveedores**
   - Representa a los proveedores de productos o ingredientes.
   - Atributos:
     - `id`: Identificador único del proveedor.
     - `nombre`, `contacto`, `telefono`, `email`, `direccion`: Información del proveedor.

10. **Ingredientes**
    - Almacena los ingredientes usados en los platillos.
    - Atributos:
      - `id`: Identificador único del ingrediente.
      - `nombre`, `cantidadDisponible`, `unidadMedida`, `precioUnitario`: Información del ingrediente.

11. **Platillos**
    - Contiene los platillos disponibles en el menú.
    - Atributos:
      - `id`: Identificador único del platillo.
      - `nombre`, `precio`, `descripcion`, `ingredientes[]`: Información y composición del platillo.

12. **Orden**
    - Detalla cada orden realizada en un pedido.
    - Atributos:
      - `id`: Identificador único de la orden.
      - `pedido_id`, `platillo_id`, `cantidad`: Información de la orden.

13. **Detalle_Orden**
    - Almacena los detalles específicos de cada orden.
    - Atributos:
      - `id`: Identificador único del detalle.
      - `orden_id`, `platillo_id`, `subtotal`: Información de cada platillo en la orden.

### Relaciones entre Tablas

- **Usuarios** se relaciona con **Clientes** y **Empleados** (herencia).
- **Clientes** realiza **Pedidos** y **Reservas**.
- **Mesas** se asignan a **Pedidos** y **Reservas**.
- **Productos** se utilizan en el **Inventario**.
- **Platillos** contienen **Productos** e incluyen **Ingredientes**.
- **Pedidos** incluyen una **Orden**.
- **Orden** se detalla en **Detalle_Orden**.
- **Proveedores** suministran **Ingredientes**.
- **Inventario** se registra a partir de los **Ingredientes** proporcionados por **Proveedores**.
- **Empleados** atienden **Pedidos** y gestionan el flujo de operaciones en el restaurante.

## Funcionalidades

1. **Gestión de Clientes**: Permite registrar y gestionar la información de los clientes.
2. **Gestión de Empleados**: Permite gestionar los datos de los empleados y sus roles.
3. **Reservas**: Los clientes pueden realizar reservas y asignar mesas.
4. **Pedidos**: Los clientes pueden realizar pedidos, y estos son gestionados por los empleados.
5. **Inventario**: Controla la cantidad de productos e ingredientes disponibles en el restaurante.
6. **Gestión de Proveedores**: Lleva el control de los proveedores que suministran ingredientes y productos.
7. **Menú de Platillos**: Permite administrar los platillos y sus ingredientes.
8. **Detalle de Órdenes**: Muestra los detalles de cada platillo en los pedidos.

## Tecnologías Utilizadas
- PHP
- HTML/CSS
- JavaScript
- MySQL (u otra base de datos relacional)

## Instrucciones de Instalación

1. Clona el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/sistema-restaurante.git
   cd sistema-restaurante
2. Configura el servidor local (XAMPP, WAMP, etc.) y crea una base de datos.

3. Importa los archivos SQL para crear las tablas necesarias.

4. Configura las credenciales de la base de datos en los archivos de configuración de tu proyecto.

5. Inicia el servidor y accede a la aplicación desde tu navegador.
