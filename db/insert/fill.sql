-- Datos de prueba para pollos_brothers.
-- Ejecutar DESPUES de schema.sql.
--
-- Credenciales de prueba (NO usar en producción):
--   admin    / admin123   (acceso: admin, contraseña)
--   cajera   / 123456     (acceso: cajero, PIN)
--   cocinero / 654321     (acceso: cocinero, PIN)

USE pollos_brothers;

-- =========================
-- CATEGORIAS
-- =========================
INSERT INTO categorias (nombre) VALUES
    ('Carnes'),
    ('Bebidas'),
    ('Combos');

-- =========================
-- PROVEEDORES
-- =========================
INSERT INTO proveedores (nombre, direccion, email, telefono, rfc) VALUES
    ('Granja Don Pollo', 'Av. Reforma 123, Col. Centro', 'ventas@donpollo.mx', '555-0100', 'GDP010203AB1');

-- =========================
-- EMPLEADOS
-- =========================
INSERT INTO empleados (nombre, apellido, fecha_con) VALUES
    ('Ana',    'Martínez', '2024-01-15'),  -- id=1, admin
    ('Lucía',  'Hernández', '2024-03-01'), -- id=2, cajera
    ('Pedro',  'Ramírez',   '2024-04-10'); -- id=3, cocinero

-- =========================
-- CREDENCIALES
-- Hashes bcrypt generados con password_hash() de PHP.
-- =========================
INSERT INTO credenciales (id_empleado, usuario, password_hash, pin_hash, acceso) VALUES
    (1, 'admin',    '$2y$12$Fn36MZEssQfn/tpiot/x0.CaW802elvwrKSXJve94LiKzTr/sGc6K', NULL, 'admin'),
    (2, 'cajera',   NULL, '$2y$12$CbrbYndO4VWxdrngjDljPOaD/TeTKrzsxAoJ9eCe7cnFZJvkHUgei', 'cajero'),
    (3, 'cocinero', NULL, '$2y$12$bHlRllYAhbz7F2l6kmU2tO/O0fJ95grk5MrjXVfRLzHtR3SOA4P7K', 'cocinero');

-- =========================
-- PRODUCTOS
-- =========================
INSERT INTO producto (id_categoria, nombre, unidad, precio_venta, precio_compra) VALUES
    (1, 'Pechuga de Pollo', 'pieza', 120.00,  70.00),
    (1, 'Alitas',           'orden',  95.00,  55.00),
    (2, 'Refresco',         'lata',   25.00,  12.00),
    (3, 'Combo Familiar',   'combo', 640.00, 380.00);

-- =========================
-- INVENTARIO
-- =========================
INSERT INTO inventario (id_producto, cantidad, cantidad_min) VALUES
    (1, 25, 10),
    (2,  5, 10),
    (3,  0,  6),
    (4, 12,  4);
