# 🚀 GUÍA RÁPIDA - INICIO DEL SISTEMA

## ⚡ Inicio Rápido (1 comando)

Si estás en tu repositorio local y obtienes "error de red", simplemente ejecuta:

```bash
bash /app/iniciar_sistema.sh
```

Este script hace TODO por ti:
- ✅ Instala MariaDB y PHP si no están instalados
- ✅ Inicia MySQL/MariaDB
- ✅ Importa la base de datos automáticamente
- ✅ Inicia el servidor PHP en puerto 8000

## 🔍 Verificar que funciona

Después de ejecutar el script, prueba:

```bash
curl http://localhost:8000/test_db.php
```

Deberías ver: ✅ Conexión exitosa a la base de datos

## 🌐 Acceder al Sistema

Una vez iniciado, abre en tu navegador:

**Para probar la conexión a BD (sin login necesario):**
```
http://localhost:8000/test_db.php
```

**Para usar el inventario completo:**
1. Ir a: `http://localhost:8000/html/index.html`
2. Iniciar sesión como administrador
3. Click en "Inventario" en el menú

## 🔑 Credenciales

Para acceder al inventario necesitas estar logueado como admin. Las credenciales están en la base de datos en la tabla `credenciales`:

```bash
# Ver credenciales
mysql -u root pollos_brothers -e "SELECT usuario, acceso FROM credenciales WHERE acceso='admin';"
```

Usuario por defecto: `admin`

## ❓ Solución de Problemas

### Error: "Connection refused" o "Error de red"

**Solución:** Ejecuta el script de inicio
```bash
bash /app/iniciar_sistema.sh
```

### Error: MySQL no responde

**Verificar si está corriendo:**
```bash
ps aux | grep mariadbd
```

**Si no está corriendo, iniciarlo:**
```bash
mysqld_safe --user=mysql &
```

**Esperar 10 segundos y volver a intentar**

### Error: Servidor PHP no responde

**Verificar si está corriendo:**
```bash
ps aux | grep "php -S"
```

**Si no está corriendo, iniciarlo:**
```bash
cd /app && nohup php -S 0.0.0.0:8000 -t /app > /var/log/php_server.log 2>&1 &
```

### Ver logs del servidor PHP

```bash
tail -f /var/log/php_server.log
```

## 📊 Verificar Servicios

```bash
# Ver todos los servicios
echo "MySQL:" && ps aux | grep mariadbd | grep -v grep
echo "PHP:" && ps aux | grep "php -S" | grep -v grep
echo "Puerto 8000:" && lsof -i:8000 || netstat -tuln | grep 8000
```

## 🔄 Reiniciar Todo

Si algo no funciona, puedes reiniciar todo:

```bash
# Matar procesos
pkill -f "php -S"
pkill mysqld

# Esperar 2 segundos
sleep 2

# Volver a iniciar
bash /app/iniciar_sistema.sh
```

## 📁 Estructura del Proyecto

```
/app/
├── html/inventario.html          → Página del inventario (CONECTADA A BD)
├── api/inventario/*.php          → APIs del inventario
├── api/util/conec.php            → Conexión a base de datos
├── test_db.php                   → Prueba de conexión
└── iniciar_sistema.sh            → Script de inicio automático ⭐
```

## ✅ Checklist de Funcionamiento

Verifica que todo funcione:

- [ ] El script `iniciar_sistema.sh` se ejecuta sin errores
- [ ] MySQL está corriendo: `ps aux | grep mariadbd`
- [ ] PHP está corriendo: `ps aux | grep "php -S"`
- [ ] Test de BD funciona: `curl -s http://localhost:8000/test_db.php | grep "✅"`
- [ ] Puedes abrir `http://localhost:8000/html/inventario.html` en el navegador

## 🎯 Resumen

**Para tu repositorio local:**

1. Clona el repositorio
2. Ejecuta: `bash /app/iniciar_sistema.sh`
3. Abre: `http://localhost:8000/test_db.php`
4. ¡Listo! El inventario está conectado a la BD

**El error "error de red" simplemente significa que los servicios no estaban iniciados. El script `iniciar_sistema.sh` resuelve todo automáticamente.**

---

📝 **Nota:** El script es seguro ejecutarlo múltiples veces. Si los servicios ya están corriendo, simplemente los detecta y no hace nada.
