DROP DATABASE IF EXISTS pollos_brothers;

CREATE DATABASE pollos_brothers
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pollos_brothers;

-- =========================
-- TABLA: empleados
-- =========================
CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    domicilio VARCHAR(255),
    curp VARCHAR(18),
    rfc VARCHAR(13),
    referencia_bancaria VARCHAR(100),
    salario DECIMAL(10,2),
    fecha_na DATE,
    fecha_con DATE NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================
-- TABLA: credenciales
-- 1 fila por empleado. Admin usa password_hash; cajero/cocinero usan pin_hash.
-- =========================
CREATE TABLE credenciales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL UNIQUE,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NULL,
    pin_hash VARCHAR(255) NULL,
    acceso ENUM('admin','cajero','cocinero') NOT NULL,
    last_login_at DATETIME NULL,
    failed_attempts INT NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_credenciales_empleado
        FOREIGN KEY (id_empleado) REFERENCES empleados(id),

    CONSTRAINT chk_secret_por_rol CHECK (
        (acceso = 'admin'    AND password_hash IS NOT NULL AND pin_hash IS NULL) OR
        (acceso IN ('cajero','cocinero') AND pin_hash IS NOT NULL AND password_hash IS NULL)
    )
) ENGINE=InnoDB;

-- =========================
-- TABLA: categorias
-- =========================
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- =========================
-- TABLA: proveedores
-- =========================
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    direccion VARCHAR(255),
    email VARCHAR(100),
    telefono VARCHAR(20),
    rfc VARCHAR(13),
    referencia_bancaria VARCHAR(100),
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================
-- TABLA: producto
-- =========================
CREATE TABLE producto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_categoria INT,
    nombre VARCHAR(100) NOT NULL,
    unidad VARCHAR(50) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    precio_compra DECIMAL(10,2) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (id_categoria) REFERENCES categorias(id),

    CONSTRAINT chk_producto_precios CHECK (
        precio_venta >= 0 AND precio_compra >= 0
    )
) ENGINE=InnoDB;

-- =========================
-- TABLA: venta
-- =========================
CREATE TABLE venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado INT NOT NULL,
    cliente VARCHAR(100),
    estado ENUM('pendiente','en_proceso','listo','entregado','cancelado')
        NOT NULL DEFAULT 'pendiente',
    metodo_pago ENUM('efectivo','tarjeta','transferencia') NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_venta_empleado
        FOREIGN KEY (id_empleado) REFERENCES empleados(id),

    INDEX idx_venta_fecha (fecha),
    INDEX idx_venta_estado (estado)
) ENGINE=InnoDB;

-- =========================
-- TABLA: compra
-- =========================
CREATE TABLE compra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT NOT NULL,
    estado ENUM('pendiente','recibida','cancelada')
        NOT NULL DEFAULT 'pendiente',
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_compra_proveedor
        FOREIGN KEY (id_proveedor) REFERENCES proveedores(id),

    INDEX idx_compra_fecha (fecha)
) ENGINE=InnoDB;

-- =========================
-- TABLA: descripcionVenta
-- =========================
CREATE TABLE descripcionVenta (
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id_venta, id_producto),

    CONSTRAINT fk_descripcionVenta_venta
        FOREIGN KEY (id_venta) REFERENCES venta(id) ON DELETE CASCADE,

    CONSTRAINT fk_descripcionVenta_producto
        FOREIGN KEY (id_producto) REFERENCES producto(id),

    CONSTRAINT chk_descvta_cantidad CHECK (cantidad > 0)
) ENGINE=InnoDB;

-- =========================
-- TABLA: descripcionCompra
-- =========================
CREATE TABLE descripcionCompra (
    id_compra INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (id_compra, id_producto),

    CONSTRAINT fk_descripcionCompra_compra
        FOREIGN KEY (id_compra) REFERENCES compra(id) ON DELETE CASCADE,

    CONSTRAINT fk_descripcionCompra_producto
        FOREIGN KEY (id_producto) REFERENCES producto(id),

    CONSTRAINT chk_desccmp_cantidad CHECK (cantidad > 0)
) ENGINE=InnoDB;

-- =========================
-- TABLA: movimiento_inventario
-- =========================
CREATE TABLE movimiento_inventario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo ENUM('entrada','salida','ajuste','venta','compra') NOT NULL,
    cantidad INT NOT NULL,
    id_venta INT NULL,
    id_compra INT NULL,
    fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_movimiento_producto
        FOREIGN KEY (id_producto) REFERENCES producto(id),

    CONSTRAINT fk_movimiento_venta
        FOREIGN KEY (id_venta) REFERENCES venta(id),

    CONSTRAINT fk_movimiento_compra
        FOREIGN KEY (id_compra) REFERENCES compra(id),

    INDEX idx_movimiento_producto_fecha (id_producto, fecha)
) ENGINE=InnoDB;

-- =========================
-- TABLA: inventario
-- =========================
CREATE TABLE inventario (
    id_producto INT PRIMARY KEY,
    cantidad INT NOT NULL DEFAULT 0,
    cantidad_min INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_inventario_producto
        FOREIGN KEY (id_producto) REFERENCES producto(id),

    CONSTRAINT chk_inv_cantidad     CHECK (cantidad     >= 0),
    CONSTRAINT chk_inv_cantidad_min CHECK (cantidad_min >= 0)
) ENGINE=InnoDB;
