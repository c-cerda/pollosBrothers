-- ============================================
-- DATOS COMPLEMENTARIOS (SIN DUPLICADOS)
-- Ejecutar DESPUES de tu archivo actual
-- ============================================

USE pollos_brothers;

-- ============================================
-- CATEGORIAS NUEVAS
-- ============================================
INSERT INTO categorias (nombre) VALUES
    ('Complementos'),
    ('Postres'),
    ('Salsas');

-- ============================================
-- PROVEEDORES NUEVOS
-- ============================================
INSERT INTO proveedores (
    nombre,
    direccion,
    email,
    telefono,
    rfc,
    referencia_bancaria
) VALUES
    (
        'Bebidas del Norte',
        'Calle Juarez 456, Col. Industrial',
        'contacto@bebidasnorte.mx',
        '555-0101',
        'BDN040506CD2',
        'BBVA-221'
    ),
    (
        'Salsas Mexicanas SA',
        'Av. Tecnologico 891',
        'ventas@salsasmex.mx',
        '555-0102',
        'SMS070809EF3',
        'SANTANDER-882'
    ),
    (
        'Distribuidora La Canasta',
        'Periferico Sur 741',
        'pedidos@lacanasta.mx',
        '555-0103',
        'DLC101112GH4',
        'HSBC-441'
    );

-- ============================================
-- EMPLEADOS NUEVOS
-- IDs esperados:
-- 4 = Carlos
-- 5 = Maria
-- ============================================
INSERT INTO empleados (
    nombre,
    apellido,
    domicilio,
    curp,
    rfc,
    referencia_bancaria,
    salario,
    fecha_na,
    fecha_con,
    activo
) VALUES
    (
        'Carlos',
        'López',
        'Calle Mina 44',
        'LOPC940404HCHPZR04',
        'LOPC940404EF4',
        'SANTANDER-EMP-004',
        9200.00,
        '1994-04-04',
        '2024-05-12',
        TRUE
    ),
    (
        'María',
        'Gómez',
        'Av. Libertad 222',
        'GOMM970707MCHLRS05',
        'GOMM970707GH5',
        'BANORTE-EMP-005',
        11000.00,
        '1997-07-07',
        '2024-06-18',
        TRUE
    );

-- ============================================
-- CREDENCIALES NUEVAS
-- ============================================
INSERT INTO credenciales (
    id_empleado,
    usuario,
    password_hash,
    pin_hash,
    acceso
) VALUES
    (
        4,
        'carlosc',
        NULL,
        '$2y$12$aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
        'cajero'
    ),
    (
        5,
        'maria_g',
        NULL,
        '$2y$12$bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb',
        'cocinero'
    );

-- ============================================
-- PRODUCTOS NUEVOS
-- ============================================
INSERT INTO producto (
    id_categoria,
    nombre,
    unidad,
    precio_venta,
    precio_compra,
    activo
) VALUES
    -- Carnes (id_categoria = 1)
    (1, 'Pierna y Muslo',        'pieza',    85.00,  48.00, TRUE),
    (1, 'Pollo Entero',          'pieza',   240.00, 145.00, TRUE),

    -- Bebidas (id_categoria = 2)
    (2, 'Refresco Coca-Cola',    'lata',     25.00,  12.00, TRUE),
    (2, 'Agua Natural',          'botella',  18.00,   8.00, TRUE),
    (2, 'Té Helado',             'botella',  30.00,  14.00, TRUE),

    -- Combos (id_categoria = 3)
    (3, 'Combo Pareja',          'combo',   320.00, 185.00, TRUE),
    (3, 'Combo Individual',      'combo',   155.00,  92.00, TRUE),

    -- Complementos (id_categoria = 4)
    (4, 'Papas Fritas',          'orden',    45.00,  20.00, TRUE),
    (4, 'Ensalada',              'porcion',  40.00,  18.00, TRUE),
    (4, 'Pure de Papa',          'porcion',  38.00,  17.00, TRUE),

    -- Postres (id_categoria = 5)
    (5, 'Pay de Queso',          'rebanada', 55.00,  25.00, TRUE),

    -- Salsas (id_categoria = 6)
    (6, 'Salsa BBQ',             'vaso',     12.00,   4.00, TRUE),
    (6, 'Salsa Buffalo',         'vaso',     14.00,   5.00, TRUE);

-- ============================================
-- INVENTARIO NUEVO
-- Asumiendo IDs:
-- 5  = Pierna y Muslo
-- 6  = Pollo Entero
-- 7  = Refresco Coca-Cola
-- 8  = Agua Natural
-- 9  = Té Helado
-- 10 = Combo Pareja
-- 11 = Combo Individual
-- 12 = Papas Fritas
-- 13 = Ensalada
-- 14 = Pure de Papa
-- 15 = Pay de Queso
-- 16 = Salsa BBQ
-- 17 = Salsa Buffalo
-- ============================================
INSERT INTO inventario (
    id_producto,
    cantidad,
    cantidad_min
) VALUES
    (5, 20, 8),
    (6, 12, 4),
    (7, 60, 20),
    (8, 40, 15),
    (9, 25, 10),
    (10, 14, 5),
    (11, 30, 8),
    (12, 22, 10),
    (13, 12, 5),
    (14, 10, 4),
    (15, 8, 3),
    (16, 35, 10),
    (17, 30, 10);

-- ============================================
-- COMPRAS
-- ============================================
INSERT INTO compra (
    id_proveedor,
    estado,
    total,
    fecha
) VALUES
    (2, 'recibida', 2400.00, '2026-05-16 10:15:00'),
    (3, 'recibida', 1200.00, '2026-05-17 11:20:00'),
    (4, 'pendiente', 3400.00, '2026-05-21 14:00:00');

-- ============================================
-- DESCRIPCION COMPRA
-- IDs compra:
-- 1,2,3 dependiendo de lo existente
-- ============================================
INSERT INTO descripcionCompra (
    id_compra,
    id_producto,
    cantidad,
    precio_unitario,
    subtotal
) VALUES
    (1, 7, 80, 12.00, 960.00),
    (1, 8, 60,  8.00, 480.00),
    (1, 9, 40, 14.00, 560.00),

    (2,16,100,  4.00, 400.00),
    (2,17, 80,  5.00, 400.00),

    (3,12, 50, 20.00,1000.00),
    (3,13, 40, 18.00, 720.00),
    (3,14, 35, 17.00, 595.00);

-- ============================================
-- VENTAS
-- ============================================
INSERT INTO venta (
    id_empleado,
    cliente,
    estado,
    metodo_pago,
    total,
    fecha
) VALUES
    (2, 'Juan Pérez',      'entregado', 'efectivo',     370.00, '2026-05-20 13:15:00'),
    (2, 'María López',     'entregado', 'tarjeta',      690.00, '2026-05-20 14:00:00'),
    (4, 'Cliente General', 'listo',     'efectivo',     155.00, '2026-05-21 12:20:00'),
    (4, 'Roberto Díaz',    'en_proceso','transferencia',412.00, '2026-05-21 13:40:00');

-- ============================================
-- DESCRIPCION VENTA
-- ============================================
INSERT INTO descripcionVenta (
    id_venta,
    id_producto,
    cantidad,
    precio_unitario,
    subtotal
) VALUES
    -- Venta 1
    (1, 1, 2, 120.00, 240.00),
    (1, 7, 2,  25.00,  50.00),
    (1,12, 1,  45.00,  45.00),
    (1,16, 1,  12.00,  12.00),
    (1,17, 1,  14.00,  14.00),

    -- Venta 2
    (2, 4, 1, 640.00, 640.00),
    (2, 8, 1,  18.00,  18.00),
    (2,15, 1,  55.00,  55.00),

    -- Venta 3
    (3,11, 1, 155.00, 155.00),

    -- Venta 4
    (4, 2, 2,  95.00, 190.00),
    (4, 7, 2,  25.00,  50.00),
    (4,12, 2,  45.00,  90.00),
    (4,16, 2,  12.00,  24.00),
    (4,17, 2,  14.00,  28.00);

-- ============================================
-- MOVIMIENTOS INVENTARIO
-- ============================================
INSERT INTO movimiento_inventario (
    id_producto,
    tipo,
    cantidad,
    id_venta,
    id_compra,
    fecha
) VALUES
    -- Compras
    (7, 'compra',  80, NULL, 1, '2026-05-16 10:20:00'),
    (8, 'compra',  60, NULL, 1, '2026-05-16 10:21:00'),
    (9, 'compra',  40, NULL, 1, '2026-05-16 10:22:00'),

    -- Ventas
    (1, 'venta',   -2, 1, NULL, '2026-05-20 13:16:00'),
    (7, 'venta',   -2, 1, NULL, '2026-05-20 13:16:00'),
    (12,'venta',   -1, 1, NULL, '2026-05-20 13:16:00'),

    (4, 'venta',   -1, 2, NULL, '2026-05-20 14:01:00'),

    (11,'venta',   -1, 3, NULL, '2026-05-21 12:21:00'),

    (2, 'venta',   -2, 4, NULL, '2026-05-21 13:41:00'),

    -- Ajustes
    (5, 'ajuste',   5, NULL, NULL, '2026-05-18 09:00:00'),
    (8, 'ajuste',  -2, NULL, NULL, '2026-05-18 09:15:00');
