# Sistema de Gestión de Inventario - Pollos El Diferente

## 📋 Descripción

Este es un sistema de punto de venta y gestión de inventario para el restaurante "Pollos El Diferente". El módulo de inventario ha sido conectado exitosamente a la base de datos MySQL/MariaDB.

## 🎯 ¿Qué se ha implementado?

### ✅ Conexión a Base de Datos
- Archivo de conexión `/app/api/util/conec.php` configurado con credenciales correctas
- Base de datos `pollos_brothers` importada y funcional
- MariaDB corriendo en el contenedor

### ✅ APIs del Inventario
Se han creado 5 nuevos endpoints en `/app/api/inventario/`:

1. **GET `/api/inventario/list.php`** - Lista productos con stock
   - Soporta búsqueda con parámetro `?search=término`
   - Muestra: nombre, categoría, stock, precios, estado

2. **GET `/api/inventario/stats.php`** - Estadísticas del inventario
   - Total de productos
   - Total de categorías
   - Stock bajo (< 10 unidades)
   - Sin stock (= 0 unidades)

3. **GET `/app/api/inventario/categorias.php`** - Lista de categorías

4. **POST `/api/inventario/create.php`** - Crear nuevo producto
   - Crea producto + registro de inventario
   - Transaccional (rollback en caso de error)

5. **POST `/api/inventario/update.php`** - Actualizar producto
   - Actualiza producto + stock
   - Validaciones incluidas

### ✅ Frontend Dinámico
El archivo `/app/html/inventario.html` ahora es completamente dinámico:

**Características:**
- 📊 **Estadísticas en tiempo real**: Cards con totales, stock bajo, sin stock
- 🔍 **Búsqueda de productos**: Busca en tiempo real mientras escribes
- ➕ **Agregar productos**: Modal con formulario completo
- ✏️ **Editar productos**: Click en "Editar" abre modal prellenado
- 🎨 **UI mejorada**: Estados visuales (Disponible/Bajo/Sin stock)
- 🔒 **Autenticación**: Solo usuarios admin pueden acceder

## 🚀 Cómo usar el sistema

### 1. Acceder al sistema

**URL del servidor PHP:** `http://localhost:8000`

**Páginas disponibles:**
- `/html/index.html` - Login
- `/html/inventario.html` - Gestión de Inventario (requiere sesión admin)
- `/test_db.php` - Prueba de conexión a BD

### 2. Iniciar sesión como administrador

Para usar el módulo de inventario necesitas estar autenticado como administrador.

**Credenciales de prueba (según la BD):**
- Usuario: `admin`
- Contraseña: `admin123` (verificar en tabla `credenciales`)

### 3. Usar el inventario

Una vez autenticado:

1. **Ver productos**: La tabla se carga automáticamente
2. **Buscar**: Escribe en el campo de búsqueda
3. **Agregar producto**:
   - Click en "+ Nuevo Producto"
   - Llena el formulario
   - Click en "Guardar"
4. **Editar producto**:
   - Click en "Editar" en cualquier producto
   - Modifica los campos necesarios
   - Click en "Guardar"

## 🔧 Estructura Técnica

### Base de Datos
```
Tabla: producto
- id, nombre, id_categoria, unidad, precio_venta, precio_compra, activo

Tabla: inventario
- id_producto, cantidad, cantidad_min

Tabla: categorias
- id, nombre
```

### Archivos Principales

```
/app/
├── api/
│   ├── util/
│   │   └── conec.php                # Conexión a BD ✨ NUEVO
│   └── inventario/                  # APIs de inventario ✨ NUEVO
│       ├── list.php                 # Listar productos
│       ├── stats.php                # Estadísticas
│       ├── categorias.php           # Lista categorías
│       ├── create.php               # Crear producto
│       └── update.php               # Actualizar producto
├── html/
│   └── inventario.html              # Frontend dinámico ✨ MODIFICADO
├── db/
│   └── dump.sql                     # Dump de la base de datos
└── test_db.php                      # Script de prueba ✨ NUEVO
```

## 📊 Configuración del Stock

- **Stock normal**: ≥ 10 unidades → Estado: **Disponible** (verde)
- **Stock bajo**: 1-9 unidades → Estado: **Bajo** (amarillo)
- **Sin stock**: 0 unidades → Estado: **Sin stock** (rojo)

## 🔐 Seguridad

- ✅ Todas las APIs verifican autenticación de sesión
- ✅ Solo usuarios con rol 'admin' pueden acceder al inventario
- ✅ Prepared statements para prevenir SQL injection
- ✅ Validación de datos en backend
- ✅ Control de transacciones en operaciones críticas

## 🐛 Resolución de Problemas

### MySQL/MariaDB no está corriendo
```bash
mysqld_safe --user=mysql &
```

### Servidor PHP no responde
```bash
supervisorctl restart php_server
# O manualmente:
php -S 0.0.0.0:8000 -t /app
```

### Error "No autenticado"
Asegúrate de haber iniciado sesión correctamente desde `/html/index.html`

### Probar conexión a BD
Visita: `http://localhost:8000/test_db.php`

## 📝 Notas Importantes

1. **Puerto del servidor**: El servidor PHP corre en el puerto **8000**
2. **Credenciales BD**: Usuario `root` sin contraseña
3. **Base de datos**: `pollos_brothers`
4. **Filtrado**: Implementado con búsqueda en tiempo real
5. **Stock bajo**: Definido como menos de 10 unidades

## ✨ Próximas Mejoras Sugeridas

- [ ] Eliminar productos (soft delete)
- [ ] Ajustar stock directamente desde inventario
- [ ] Historial de movimientos
- [ ] Exportar inventario a Excel/PDF
- [ ] Alertas automáticas de stock bajo
- [ ] Gráficas de tendencias

## 🎉 ¡Listo para usar!

El sistema de inventario está completamente funcional y conectado a la base de datos. Todas las operaciones CRUD funcionan correctamente.

---

**Desarrollado para:** Pollos El Diferente  
**Fecha:** Mayo 2026  
**Estado:** ✅ Funcional
