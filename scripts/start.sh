#!/bin/bash

echo "======================================"
echo "🐔 Pollos El Diferente - Inicio"
echo "======================================"
echo ""

# Verificar MySQL
echo "📊 Verificando MySQL/MariaDB..."
if ! pgrep -x "mariadbd" > /dev/null; then
    echo "   ⚠️  MySQL no está corriendo. Iniciando..."
    mysqld_safe --user=mysql > /dev/null 2>&1 &
    sleep 5
    echo "   ✅ MySQL iniciado"
else
    echo "   ✅ MySQL ya está corriendo"
fi

# Verificar PHP Server
echo "🌐 Verificando servidor PHP..."
if ! lsof -i:8000 > /dev/null 2>&1; then
    echo "   ⚠️  Servidor PHP no está corriendo. Iniciando..."
    supervisorctl start php_server
    sleep 2
    echo "   ✅ Servidor PHP iniciado en puerto 8000"
else
    echo "   ✅ Servidor PHP ya está corriendo en puerto 8000"
fi

echo ""
echo "======================================"
echo "✅ Sistema listo!"
echo "======================================"
echo ""
echo "📍 URLs disponibles:"
echo "   • Servidor: http://localhost:8000"
echo "   • Login: http://localhost:8000/html/index.html"
echo "   • Inventario: http://localhost:8000/html/inventario.html"
echo "   • Test BD: http://localhost:8000/test_db.php"
echo ""
echo "🔑 Credenciales de prueba:"
echo "   Usuario: admin"
echo "   Contraseña: admin123 (verificar en BD)"
echo ""
echo "📚 Ver README.md para más información"
echo "======================================"
