#!/bin/bash

# Script para instalar y configurar MySQL

echo "====================================="
echo "Instalando MySQL Server..."
echo "====================================="

# Actualizar repositorios
apt-get update

# Instalar MySQL sin interacción
DEBIAN_FRONTEND=noninteractive apt-get install -y mysql-server

# Crear directorio de datos si no existe
mkdir -p /var/lib/mysql
mkdir -p /var/run/mysqld
chown -R mysql:mysql /var/lib/mysql
chown -R mysql:mysql /var/run/mysqld

# Inicializar base de datos si es necesario
if [ ! -d "/var/lib/mysql/mysql" ]; then
    echo "Inicializando base de datos MySQL..."
    mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql
fi

# Iniciar MySQL
echo "Iniciando MySQL..."
mysqld_safe --user=mysql &

# Esperar a que MySQL esté listo
echo "Esperando a que MySQL esté listo..."
for i in {1..30}; do
    if mysqladmin ping -h localhost --silent 2>/dev/null; then
        echo "MySQL está listo!"
        break
    fi
    echo "Esperando... ($i/30)"
    sleep 2
done

# Importar base de datos
echo "====================================="
echo "Importando base de datos..."
echo "====================================="

if [ -f "/app/db/dump.sql" ]; then
    mysql -u root < /app/db/dump.sql
    echo "Base de datos importada exitosamente!"
else
    echo "Error: No se encontró el archivo dump.sql"
    exit 1
fi

echo "====================================="
echo "MySQL configurado correctamente!"
echo "====================================="
