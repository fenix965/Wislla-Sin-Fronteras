# Sistema de Gestión para el Restaurante "La Wislla"

Este proyecto es un sistema de gestión para un restaurante que permite controlar reservas, pedidos, mesas, inventarios, proveedores, clientes, empleados y platillos. El sistema busca optimizar la operatividad del restaurante, mejorar la organización y facilitar el acceso a información importante en tiempo real.

## Tabla de Contenidos
- [Objetivo](#objetivo)
- [Descripción del Sistema](#descripción-del-sistema)
- [Modelo de Datos](#modelo-de-datos)
  - [Colecciones](#colecciones)
  - [Relaciones entre Colecciones](#relaciones-entre-colecciones)
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

## Modelo de Datos
El modelo de datos se basa en una estructura de colecciones en MongoDB, donde cada colección representa una entidad del sistema. Las principales colecciones son:

### Tablas

1. **Usuarios**
   - Representa a todos los usuarios registrados en el sistema.
   - Atributos:
     - `_id`: Identificador único del usuario.
     - `nombre_usuario`: Nombre del usuario.
     - `contraseña`: Contraseña del usuario.
     - `rol`: Rol del usuario (cliente, administrador, cocinero, mesero).

2. **Clientes**
   - Almacena la información de los clientes.
   - Atributos:
     - `_id`: Identificador único del cliente.
     - `usuario_id`: Identificador del usuario asociado.
     - `nombre`, `apellidos`, `telefono`, `email`, `direccion`: Información del cliente.

3. **Empleados**
   - Contiene los datos de los empleados.
   - Atributos:
     - `_id`: Identificador único del empleado.
     - `usuario_id`: Identificador del usuario asociado.
     - `nombre`, `apellidos`, `puesto`, `salario`, `telefono`, `email`: Información del empleado y su posición en el restaurante.

4. **Mesas**
   - Representa las mesas del restaurante.
   - Atributos:
     - `_id`: Identificador único de la mesa.
     - `numero`: Número de la mesa.
     - `capacidad`: Capacidad de personas.
     - `estado`: Estado de disponibilidad de la mesa.
     - `ubicacion`: Ubicación de la mesa en el restaurante.

5. **Reservas**
   - Almacena las reservas realizadas por los clientes.
   - Atributos:
     - `_id`: Identificador único de la reserva.
     - `cliente_id`, `mesa_id`: Referencias a cliente y mesa.
     - `fecha`, `hora`, `numero_personas`: Información sobre la reserva.

6. **Pedidos**
   - Contiene los pedidos realizados por los clientes.
   - Atributos:
     - `_id`: Identificador único del pedido.
     - `cliente_id`, `mesa_id`, `empleado_id`: Referencias a cliente, mesa y empleado.
     - `fecha`, `estado`, `total`: Detalles del pedido.

7. **Productos**
   - Almacena los productos que el restaurante ofrece o utiliza.
   - Atributos:
     - `_id`: Identificador único del producto.
     - `nombre`, `precio`, `categoria`: Información sobre el producto.

8. **Inventario**
   - Controla las existencias de productos en el restaurante.
   - Atributos:
     - `_id`: Identificador único del registro.
     - `producto_id`: Referencia al producto.
     - `cantidad`: Cantidad disponible.
     - `fechaIngreso`: Fecha de ingreso del producto al inventario.

9. **Proveedores**
   - Representa a los proveedores de productos o ingredientes.
   - Atributos:
     - `_id`: Identificador único del proveedor.
     - `nombre`, `contacto`, `telefono`, `email`, `direccion`: Información del proveedor.

10. **Ingredientes**
    - Almacena los ingredientes usados en los platillos.
    - Atributos:
      - `_id`: Identificador único del ingrediente.
      - `nombre`, `cantidadDisponible`, `unidadMedida`, `precioUnitario`: Información del ingrediente.

11. **Platillos**
    - Contiene los platillos disponibles en el menú.
    - Atributos:
      - `_id`: Identificador único del platillo.
      - `nombre`, `precio`, `descripcion`, `ingredientes[]`: Información y composición del platillo.

12. **Orden**
    - Detalla cada orden realizada en un pedido.
    - Atributos:
      - `_id`: Identificador único de la orden.
      - `pedido_id`, `platillo_id`, `cantidad`: Información de la orden.

13. **Detalle_Orden**
    - Almacena los detalles específicos de cada orden.
    - Atributos:
      - `_id`: Identificador único del detalle.
      - `orden_id`, `platillo_id`, `subtotal`: Información de cada platillo en la orden.

### Relaciones entre Colecciones

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

## Instrucciones de Instalación

1. Clona el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/sistema-restaurante.git
   cd sistema-restaurante
