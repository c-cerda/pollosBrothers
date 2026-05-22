# 📖 Guía de Uso - Sistema de Inventario

## 🎯 Resumen

El archivo `html/inventario.html` ha sido **conectado exitosamente a la base de datos**. Ahora todos los datos son dinámicos y se cargan directamente desde MySQL.

## ✨ ¿Qué hace el sistema ahora?

### Antes ❌
- Datos hardcodeados (falsos) en el HTML
- No se conectaba a la base de datos
- No se podían agregar ni editar productos realmente

### Ahora ✅
- **Datos reales** cargados desde la base de datos
- **Estadísticas dinámicas**: Total productos, categorías, stock bajo, sin stock
- **Búsqueda funcional**: Busca productos en tiempo real
- **Agregar productos**: Se guardan realmente en la BD
- **Editar productos**: Modifica los datos en la BD
- **Estados automáticos**: Disponible/Bajo/Sin stock según cantidad

## 🚀 Cómo Probar

### 1. Iniciar el sistema

```bash
/app/scripts/start.sh
```

### 2. Acceder al sistema

**Opción A: Probar conexión a BD primero (sin login)**
```
http://localhost:8000/test_db.php
```
Deberías ver: ✅ Conexión exitosa y lista de productos

**Opción B: Acceder al inventario completo (requiere login)**
1. Ir a: `http://localhost:8000/html/index.html`
2. Iniciar sesión como administrador
3. Navegar a Inventario

### 3. Probar funcionalidades

#### Ver productos con stock real
- La tabla muestra todos los productos de la BD
- Los números son reales (no falsos)

#### Buscar productos
- Escribe en el campo "Buscar producto..."
- La búsqueda se hace en tiempo real contra la BD

#### Ver estadísticas
Las tarjetas superiores muestran datos reales:
- **Total Productos**: Cuenta real de productos activos
- **Categorías**: Número de categorías en uso
- **Stock Bajo**: Productos con menos de 10 unidades
- **Sin Stock**: Productos con 0 unidades

#### Agregar un nuevo producto
1. Click en "+ Nuevo Producto"
2. Llenar formulario:
   - Nombre: "Producto Prueba"
   - Categoría: Seleccionar una
   - Unidad: "pieza"
   - Stock: 15
   - Precio Venta: 50.00
   - Precio Compra: 30.00
3. Click "Guardar"
4. Ver que aparece en la tabla

#### Editar un producto existente
1. Click en "Editar" en cualquier producto
2. Modificar el stock (ej: cambiar a 5 para que quede en "Bajo")
3. Guardar
4. Ver que el estado cambia a amarillo "Bajo"

## 🔧 Pruebas de APIs (con curl)

### Probar API de estadísticas (requiere sesión)
```bash
# Sin sesión
curl http://localhost:8000/api/inventario/stats.php
# Respuesta: {"ok":false,"error":"No autenticado."}
```

### Ver estructura de respuesta esperada
Las APIs responden en formato JSON:

**Lista de productos:**
```json
{
  "ok": true,
  "productos": [
    {
      "id": 1,
      "nombre": "Pechuga de Pollo",
      "categoria": "Carnes",
      "stock": 21,
      "precio_venta": 120.00,
      "estado": "disponible"
    }
  ]
}
```

**Estadísticas:**
```json
{
  "ok": true,
  "stats": {
    "total_productos": 17,
    "total_categorias": 6,
    "stock_bajo": 5,
    "sin_stock": 2
  }
}
```

## 🗄️ Estructura de la Base de Datos

### Tabla: producto
```sql
id, nombre, id_categoria, unidad, precio_venta, precio_compra, activo
```

### Tabla: inventario
```sql
id_producto, cantidad, cantidad_min
```

### Tabla: categorias
```sql
id, nombre
```

## 📊 Lógica de Estados

El sistema clasifica automáticamente los productos:

| Stock | Estado | Color | Criterio |
|-------|--------|-------|----------|
| ≥ 10 | Disponible | Verde | cantidad >= 10 |
| 1-9 | Bajo | Amarillo | 0 < cantidad < 10 |
| 0 | Sin stock | Rojo | cantidad = 0 |

## 🔍 Verificar que todo funciona

### Checklist de pruebas:

- [ ] MySQL está corriendo: `ps aux | grep mariadbd`
- [ ] PHP Server está corriendo: `ps aux | grep "php -S"`
- [ ] Test de BD funciona: `curl -s http://localhost:8000/test_db.php | grep "✅"`
- [ ] Inventario HTML carga: Abrir en navegador
- [ ] Estadísticas se cargan dinámicamente
- [ ] Búsqueda funciona
- [ ] Se puede agregar producto
- [ ] Se puede editar producto
- [ ] Estados se actualizan correctamente

## ❓ Preguntas Frecuentes

**P: ¿Dónde están los archivos de APIs?**  
R: En `/app/api/inventario/` - 5 archivos PHP nuevos

**P: ¿Cómo resetear la BD a los datos iniciales?**  
R: `mysql -u root pollos_brothers < /app/db/dump.sql`

**P: ¿El sistema guarda los cambios permanentemente?**  
R: Sí, todos los cambios se guardan en la base de datos MySQL

**P: ¿Puedo eliminar productos?**  
R: Por ahora no, solo agregar y editar. Se puede implementar después.

**P: ¿Funciona la búsqueda?**  
R: Sí, busca en el nombre del producto en tiempo real

## 🎯 Comparación Antes/Después

### ANTES:
```html
<!-- Datos hardcodeados en HTML -->
<tr>
    <td>Pechuga de Pollo</td>
    <td>Carnes</td>
    <td>25</td>
    <td>$120</td>
</tr>
```

### AHORA:
```javascript
// Datos dinámicos desde API
fetch('../api/inventario/list.php')
  .then(res => res.json())
  .then(data => {
    // Renderizar productos reales de la BD
    productos = data.productos;
    renderProductos();
  });
```

## 📞 Soporte

Si algo no funciona:

1. Verificar logs: `tail -f /var/log/supervisor/php_server.*.log`
2. Verificar MySQL: `mysql -u root pollos_brothers -e "SELECT COUNT(*) FROM producto;"`
3. Reiniciar servicios: `supervisorctl restart php_server`

## ✅ Conclusión

El sistema de inventario ahora está **completamente funcional** y conectado a la base de datos. Puedes:

✅ Ver productos reales  
✅ Buscar productos  
✅ Ver estadísticas en tiempo real  
✅ Agregar nuevos productos  
✅ Editar productos existentes  
✅ Ver estados automáticos de stock  

¡Todo está listo para usar! 🎉
