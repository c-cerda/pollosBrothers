# DISEÑO DE CASOS DE PRUEBA

> **Nota:** CP es el acrónimo de *Caso de Prueba*.
> **Nota:** TBD es el acrónimo de *TO BE DONE*. 

## CP-01

**Descripción:**  
Prueba de inicio de sesión del administrador al sistema.

**Precondiciones:**  
Estar previamente en la vista de logueo del sistema (`login.html`).

**Pasos:**  
1. Estando en la vista de logueo, introducir usuario y contraseña del administrador.
2. Dar clic izquierdo en el botón **“Entrar”**.

**Resultado esperado:**  
El logueo es exitoso y se dirigirá a la vista del administrador (`admin.html`).

**Resultado obtenido:**  
TBD

---

## CP-02

**Descripción:**  
Prueba de inicio de sesión de empleado de caja al sistema.

**Precondiciones:**  
Estar previamente en la vista de logueo del sistema (`login.html`).

**Pasos:**  
1. Estando en la vista de logueo, introducir usuario.
2. Dar clic izquierdo en el botón **“Entrar”**.
3. Requiere un pin de acceso de 6 dígitos.

**Resultado esperado:**  
El logueo es exitoso y se dirigirá a la vista del empleado de caja (`caja.html`).

**Resultado obtenido:**  
TBD

---

## CP-03

**Descripción:**  
Prueba de inicio de sesión de empleado de cocina al sistema.

**Precondiciones:**  
Estar previamente en la vista de logueo del sistema (`login.html`).

**Pasos:**  
1. Estando en la vista de logueo, introducir usuario.
2. Dar clic izquierdo en el botón **“Entrar”**.
3. Requiere un pin de acceso de 6 dígitos.

**Resultado esperado:**  
El logueo es exitoso y se dirigirá a la vista del empleado de cocina (`cocina.html`).

**Resultado obtenido:**  
TBD

---

## CP-04

**Descripción:**  
Prueba de acceso a la vista de dashboards con usuario administrador.

**Precondiciones:**  
Haber iniciado sesión como administrador en la vista de logueo (`login.html`).

**Pasos:**  
1. Iniciar sesión como administrador.
2. Automáticamente se dirigirá a la vista del administrador (`admin.html`).

**Resultado esperado:**  
Se espera un dashboard con:
- Pedidos pendientes
- Órdenes del día
- Ingresos del día
- Inventario bajo
- Pedidos recientes
- Ventas de la semana

También se espera el menú lateral izquierdo con:
- **Dashboard** (`admin.html`)
- **Inventario**
- **Cerrar Sesión**

**Resultado obtenido:**  
TBD

---

## CP-05

**Descripción:**  
Prueba de acceso a la vista de inventario con usuario administrador.

**Precondiciones:**  
Estar previamente en la vista del administrador con sesión iniciada (`admin.html`).

**Pasos:**  
1. Estando en la vista del administrador, dar clic izquierdo en el botón **“Inventario”** del menú lateral izquierdo.

**Resultado esperado:**  
Se dirigirá a la vista de inventario (`inventario.html`).

La vista debe contener:
- Entrada para búsqueda de productos
- Botón para añadir nuevo producto
- Dashboard con:
  - Total de productos
  - Categorías
  - Stock bajo
  - Sin stock
- Listado de productos

También se espera el menú lateral izquierdo con:
- **Dashboard** (`admin.html`)
- **Inventario**
- **Cerrar Sesión**

**Resultado obtenido:**  
TBD

---

## CP-06

**Descripción:**  
Prueba de cierre de sesión del usuario administrador.

**Precondiciones:**  
Tener iniciada una sesión con usuario administrador y estar en cualquier vista relacionada (`admin.html` o `inventario.html`).

**Pasos:**  
1. Dar clic en el botón **“Cerrar Sesión”** del menú lateral izquierdo.

**Resultado esperado:**  
Se cierra la sesión y se redirige a la vista de logueo (`login.html`).

**Resultado obtenido:**  
TBD

---

## CP-07

**Descripción:**  
Prueba de acceso a la vista de caja registradora con usuario empleado de caja.

**Precondiciones:**  
Tener una sesión iniciada con un usuario empleado de caja.

**Pasos:**  
1. Iniciar sesión con el usuario empleado de caja y su correspondiente pin.
2. Automáticamente se dirigirá a la vista de caja (`caja.html`).

**Resultado esperado:**  
La vista debe contener:
- Menú de productos disponibles
- Entrada de búsqueda de productos
- Apartado tipo ticket con:
  - Productos añadidos
  - Total
  - Tipo de pago (efectivo o tarjeta)
  - Botón para cobrar

También se espera el menú lateral izquierdo con:
- **Caja**
- **Cerrar Sesión**

**Resultado obtenido:**  
TBD

---

## CP-08

**Descripción:**  
Prueba de acceso a la vista pedidos con usuario empleado de cocina.

**Precondiciones:**  
Tener una sesión iniciada con un usuario empleado de cocina.

**Pasos:**  
1. Iniciar sesión con el usuario empleado de cocina.
2. Automáticamente se dirigirá a la vista de pedidos (`cocina.html`).

**Resultado esperado:**  
La vista debe contener:
- Apartado del pedido actual con:
  - Número de orden
  - Hora del pedido
  - Productos de la orden
  - Extras

- Apartado de pedidos siguientes con:
  - Breve descripción
  - Número de pedido
  - Hora de realización

**Resultado obtenido:**  
TBD

---

## CP-09

**Descripción:**  
Prueba para realizar un pedido en caja.

**Precondiciones:**  
Haber iniciado sesión como empleado de caja y estar en la vista de caja.

**Pasos:**  
1. Seleccionar los productos solicitados.
2. Verificar que aparezcan en el ticket.
3. Seleccionar método de pago:
   - **Efectivo**
   - **Tarjeta**
4. Presionar el botón **“Cobrar”**.

**Resultado esperado:**  
- Los productos seleccionados aparecen en el ticket.
- El pago se procesa correctamente.
- El pedido aparece reflejado en la vista de cocina.

**Resultado obtenido:**  
TBD

---

## CP-10

**Descripción:**  
Prueba de despacho de un pedido.

**Precondiciones:**  
- Un empleado de caja realizó un pedido exitosamente.
- El empleado de cocina está en la vista de cocina.

**Pasos:**  
1. Visualizar el pedido en la cola de pedidos.
2. Cuando el pedido esté listo, presionar **“Marcar como listo”**.

**Resultado esperado:**  
El pedido cambia de estado:
- De **Pendiente**
- A **Hecho**

Además, la fila avanza al siguiente pedido.

**Resultado obtenido:**  
TBD

---

## CP-11

**Descripción:**  
Prueba para añadir un nuevo producto a la base de datos.

**Precondiciones:**  
Tener una sesión iniciada como administrador y estar en la vista de inventario.

**Pasos:**  
1. Dar clic en el botón **“+ Nuevo Producto”**.
2. Completar los campos del modal:
   - **Producto:** Nombre del producto
   - **Categoría:** Selección de categoría
   - **Stock:** Cantidad disponible
   - **Precio:** Precio unitario
3. Presionar el botón **“Agregar”**.

Opcionalmente:
- Presionar **“Cancelar”** para abortar la inserción.

**Resultado esperado:**  
El producto aparece reflejado en el listado de productos del inventario.

**Resultado obtenido:**  
TBD

---

## CP-12

**Descripción:**  
Prueba para modificar un producto de la base de datos.

**Precondiciones:**  
Tener una sesión iniciada como administrador y estar en la vista de inventario.

**Pasos:**  
1. En el listado de productos, hacer clic en el botón **“Editar”** del producto deseado.
2. Modificar los campos necesarios:
   - Producto
   - Categoría
   - Stock
   - Precio
3. Presionar el botón **“Modificar”** para confirmar.

Opcionalmente:
- Presionar **“Cancelar”** para descartar cambios.

**Resultado esperado:**  
Los cambios realizados se reflejan correctamente en el listado de productos.

**Resultado obtenido:**  
TBD
