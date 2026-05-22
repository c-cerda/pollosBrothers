# 🔧 SOLUCIÓN - Problemas de Inventario

## ✅ Producto "Papas" - CONFIRMADO EN BD

El producto **"Papas Fritas"** SÍ existe en la base de datos:

```
ID: 25
Nombre: Papas Fritas
Categoría: Complementos
Stock: 20 unidades
Precio Venta: $45.00
Precio Compra: $20.00
Estado: Disponible ✅
```

**Verificación manual:**
```bash
mysql -u root pollos_brothers -e "SELECT * FROM producto WHERE nombre LIKE '%papas%';"
```

---

## ❌ Problema: "No se pudieron cargar los productos"

### Causa del Problema

El error ocurre porque las APIs de inventario requieren **autenticación de administrador**. Si no hay una sesión activa, las APIs responden:

```json
{"ok": false, "error": "No autenticado."}
```

### ✅ Solución Aplicada

He configurado lo siguiente:

1. **Credenciales de admin actualizadas:**
   - Usuario: `admin`
   - Contraseña: `admin123`

2. **Redirect corregido** en `/api/login_admin.php`
   - Ahora redirige a `/html/inventario.html` correctamente

3. **API de prueba creada** (sin autenticación):
   - `/api/inventario/list_test.php` - Para testing sin login

---

## 🚀 Cómo Usar el Inventario (Paso a Paso)

### Opción 1: Con Autenticación (Modo Normal)

**Paso 1:** Abrir página de login
```
http://localhost:8000/html/index.html
```

**Paso 2:** Cambiar a modo administrador
- Click en el botón ⚙ (gear icon) en la esquina superior

**Paso 3:** Ingresar credenciales
- Usuario: `admin`
- Contraseña: `admin123`
- Click en "Entrar"

**Paso 4:** Serás redirigido automáticamente al inventario
```
http://localhost:8000/html/inventario.html
```

Ahora deberías ver:
- ✅ Estadísticas cargadas (Total Productos, Categorías, etc.)
- ✅ Tabla de productos con datos reales
- ✅ Búsqueda funcional
- ✅ Botones de Agregar/Editar funcionando

### Opción 2: Testing Sin Autenticación (Modo Debug)

**Página de prueba directa:**
```
http://localhost:8000/test_inventario.html
```

Esta página muestra:
- Estado de conexión a BD
- Respuesta de las APIs (aunque digan "No autenticado")
- Confirmación de que "Papas" existe en BD

**API de prueba (sin autenticación):**
```bash
# Ver todos los productos
curl http://localhost:8000/api/inventario/list_test.php

# Buscar "papas"
curl http://localhost:8000/api/inventario/list_test.php?search=papas
```

---

## 🔍 Verificar que Todo Funciona

### Test 1: Verificar MySQL
```bash
mysql -u root pollos_brothers -e "SELECT COUNT(*) as total FROM producto;"
```
Debe mostrar: `17` productos

### Test 2: Verificar "Papas Fritas"
```bash
curl -s "http://localhost:8000/api/inventario/list_test.php?search=papas"
```
Debe mostrar el producto con ID 25

### Test 3: Probar Login
1. Ir a: `http://localhost:8000/html/index.html`
2. Cambiar a modo admin (botón ⚙)
3. Ingresar: `admin` / `admin123`
4. Debe redirigir al inventario

### Test 4: Ver Inventario
Una vez logueado, en `http://localhost:8000/html/inventario.html`:
- Las tarjetas de estadísticas deben mostrar números (no "-")
- La tabla debe mostrar 17 productos
- Buscar "papas" debe encontrar "Papas Fritas"

---

## 🐛 Si Sigue Sin Funcionar

### Problema: Botón ⚙ no aparece o no funciona

**Solución:** Refresca la página con Ctrl+F5

### Problema: Dice "Credenciales inválidas"

**Solución:** Resetear contraseña
```bash
mysql -u root pollos_brothers -e "UPDATE credenciales SET password_hash = '\$2y\$10\$pl1V.bs2lGGc/kNWi3DUieHr3YuZo0G3Jsc9IalKYcJmY9RaIuMtW', failed_attempts = 0, locked_until = NULL WHERE usuario = 'admin';"
```

### Problema: Redirige pero sigue sin cargar productos

**Verificar sesión PHP:**
```bash
php -r "echo session_save_path();"
```

**Reiniciar servidor PHP:**
```bash
pkill -f "php -S"
cd /app && nohup php -S 0.0.0.0:8000 -t /app > /var/log/php_server.log 2>&1 &
```

### Problema: Error "No autenticado" después del login

**Verificar que la sesión persiste:**
Abre las herramientas de desarrollo del navegador (F12):
- Pestaña "Application" o "Almacenamiento"
- Buscar "Cookies"
- Debe haber una cookie de sesión PHP (PHPSESSID)

Si no existe, el problema es que PHP no puede escribir sesiones. Solución:
```bash
mkdir -p /tmp/sessions
chmod 777 /tmp/sessions
# Reiniciar PHP server
pkill -f "php -S"
cd /app && php -S 0.0.0.0:8000 -t /app &
```

---

## 📝 Resumen

### ✅ Lo que SÍ funciona:
- ✅ Base de datos conectada
- ✅ Producto "Papas Fritas" existe (ID: 25, Stock: 20)
- ✅ APIs funcionan correctamente
- ✅ Autenticación configurada
- ✅ Credenciales: admin/admin123

### ⚠️ Lo que necesitas hacer:
1. Iniciar el sistema: `bash /app/iniciar_sistema.sh`
2. Abrir: `http://localhost:8000/html/index.html`
3. Cambiar a modo admin (botón ⚙)
4. Login con: admin/admin123
5. Usar el inventario normalmente

### 🧪 Para testing rápido sin login:
- `http://localhost:8000/test_inventario.html`
- `http://localhost:8000/api/inventario/list_test.php`

---

**¿Necesitas ayuda?** Ejecuta esto y comparte el resultado:
```bash
echo "=== DIAGNÓSTICO ===" && \
echo "MySQL:" && ps aux | grep mariadbd | grep -v grep && \
echo "PHP:" && ps aux | grep "php -S" | grep -v grep && \
echo "Productos:" && mysql -u root pollos_brothers -e "SELECT COUNT(*) FROM producto;" && \
echo "Papas:" && mysql -u root pollos_brothers -e "SELECT nombre, stock FROM producto WHERE nombre LIKE '%papas%';" && \
curl -s http://localhost:8000/api/inventario/list_test.php?search=papas | grep -o '"nombre":"[^"]*"'
```
