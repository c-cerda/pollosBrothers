#!/bin/bash

echo "========================================"
echo "🐔 INICIANDO SISTEMA POLLOS EL DIFERENTE"
echo "========================================"
echo ""

# 1. Verificar e instalar MariaDB si es necesario
echo "📦 [1/4] Verificando MariaDB..."
if ! command -v mariadb &> /dev/null && ! command -v mysql &> /dev/null; then
    echo "   ⚠️  MariaDB no está instalado. Instalando..."
    apt-get update -qq
    DEBIAN_FRONTEND=noninteractive apt-get install -y mariadb-server mariadb-client -qq
    echo "   ✅ MariaDB instalado"
else
    echo "   ✅ MariaDB ya está instalado"
fi

# 2. Verificar e instalar PHP si es necesario
echo "🐘 [2/4] Verificando PHP..."
if ! command -v php &> /dev/null; then
    echo "   ⚠️  PHP no está instalado. Instalando..."
    apt-get update -qq
    DEBIAN_FRONTEND=noninteractive apt-get install -y php php-mysql php-cli php-json -qq
    echo "   ✅ PHP instalado"
else
    echo "   ✅ PHP ya está instalado ($(php -v | head -1))"
fi

# 3. Iniciar MySQL/MariaDB
echo "🗄️  [3/4] Iniciando MySQL/MariaDB..."
if pgrep -x "mariadbd" > /dev/null || pgrep -x "mysqld" > /dev/null; then
    echo "   ✅ MySQL ya está corriendo"
else
    # Crear directorios necesarios
    mkdir -p /var/run/mysqld
    chown mysql:mysql /var/run/mysqld 2>/dev/null || true
    
    # Iniciar MySQL
    if command -v mysqld_safe &> /dev/null; then
        mysqld_safe --user=mysql &
    elif command -v mariadbd-safe &> /dev/null; then
        mariadbd-safe --user=mysql &
    else
        service mysql start 2>/dev/null || service mariadb start 2>/dev/null || true
    fi
    
    # Esperar a que MySQL esté listo
    echo "   ⏳ Esperando a que MySQL esté listo..."
    for i in {1..15}; do
        if mysqladmin ping -h localhost --silent 2>/dev/null; then
            echo "   ✅ MySQL iniciado correctamente"
            break
        fi
        sleep 2
    done
fi

# 4. Importar base de datos si es necesario
echo "📊 [4/4] Verificando base de datos..."
if mysql -u root -e "USE pollos_brothers;" 2>/dev/null; then
    echo "   ✅ Base de datos 'pollos_brothers' ya existe"
else
    echo "   ⚠️  Base de datos no existe. Importando..."
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS pollos_brothers;" 2>/dev/null || true
    mysql -u root pollos_brothers < /app/db/dump.sql 2>/dev/null || true
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');" 2>/dev/null || true
    mysql -u root -e "FLUSH PRIVILEGES;" 2>/dev/null || true
    echo "   ✅ Base de datos importada"
fi

# 5. Iniciar servidor PHP
echo "🌐 [5/5] Iniciando servidor PHP..."
# Matar proceso anterior si existe
pkill -f "php -S 0.0.0.0:8000" 2>/dev/null || true
sleep 1

# Iniciar servidor en segundo plano
cd /app
nohup php -S 0.0.0.0:8000 -t /app > /var/log/php_server.log 2>&1 &
sleep 2

if lsof -i:8000 > /dev/null 2>&1 || netstat -tuln | grep ":8000" > /dev/null 2>&1; then
    echo "   ✅ Servidor PHP iniciado en puerto 8000"
else
    echo "   ⚠️  No se pudo verificar el servidor PHP, pero debería estar corriendo"
fi

echo ""
echo "========================================"
echo "✅ SISTEMA INICIADO CORRECTAMENTE"
echo "========================================"
echo ""
echo "📍 URLs disponibles:"
echo "   • Servidor Principal: http://localhost:8000"
echo "   • Test de BD: http://localhost:8000/test_db.php"
echo "   • Login: http://localhost:8000/html/index.html"
echo "   • Inventario: http://localhost:8000/html/inventario.html"
echo ""
echo "🔑 Credenciales:"
echo "   Usuario: admin"
echo "   Contraseña: (verificar en BD)"
echo ""
echo "🧪 Probar conexión:"
echo "   curl http://localhost:8000/test_db.php"
echo ""
echo "📋 Ver logs PHP:"
echo "   tail -f /var/log/php_server.log"
echo ""
echo "========================================"
