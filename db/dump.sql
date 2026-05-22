-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: pollos_brothers
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (2,'Bebidas'),(1,'Carnes'),(3,'Combos'),(10,'Complementos'),(11,'Postres'),(12,'Salsas');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compra`
--

DROP TABLE IF EXISTS `compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compra` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_proveedor` int NOT NULL,
  `estado` enum('pendiente','recibida','cancelada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_compra_proveedor` (`id_proveedor`),
  KEY `idx_compra_fecha` (`fecha`),
  CONSTRAINT `fk_compra_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compra`
--

LOCK TABLES `compra` WRITE;
/*!40000 ALTER TABLE `compra` DISABLE KEYS */;
INSERT INTO `compra` VALUES (1,2,'recibida',2400.00,'2026-05-16 10:15:00','2026-05-22 06:34:41','2026-05-22 06:34:41'),(2,3,'recibida',1200.00,'2026-05-17 11:20:00','2026-05-22 06:34:41','2026-05-22 06:34:41');
/*!40000 ALTER TABLE `compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credenciales`
--

DROP TABLE IF EXISTS `credenciales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credenciales` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_empleado` int NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pin_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acceso` enum('admin','cajero','cocinero') COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login_at` datetime DEFAULT NULL,
  `failed_attempts` int NOT NULL DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_empleado` (`id_empleado`),
  UNIQUE KEY `usuario` (`usuario`),
  CONSTRAINT `fk_credenciales_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`),
  CONSTRAINT `chk_secret_por_rol` CHECK ((((`acceso` = _utf8mb4'admin') and (`password_hash` is not null) and (`pin_hash` is null)) or ((`acceso` in (_utf8mb4'cajero',_utf8mb4'cocinero')) and (`pin_hash` is not null) and (`password_hash` is null))))
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credenciales`
--

LOCK TABLES `credenciales` WRITE;
/*!40000 ALTER TABLE `credenciales` DISABLE KEYS */;
INSERT INTO `credenciales` VALUES (1,1,'admin','$2y$12$Fn36MZEssQfn/tpiot/x0.CaW802elvwrKSXJve94LiKzTr/sGc6K',NULL,'admin','2026-05-21 20:13:50',0,NULL,'2026-05-22 01:35:34','2026-05-22 02:13:50'),(2,2,'cajera',NULL,'$2y$12$CbrbYndO4VWxdrngjDljPOaD/TeTKrzsxAoJ9eCe7cnFZJvkHUgei','cajero','2026-05-22 00:19:25',0,NULL,'2026-05-22 01:35:34','2026-05-22 06:19:25'),(3,3,'cocinero',NULL,'$2y$12$bHlRllYAhbz7F2l6kmU2tO/O0fJ95grk5MrjXVfRLzHtR3SOA4P7K','cocinero','2026-05-22 00:20:54',0,NULL,'2026-05-22 01:35:34','2026-05-22 06:20:54'),(4,4,'carlosc',NULL,'$2y$12$aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa','cajero',NULL,0,NULL,'2026-05-22 06:30:09','2026-05-22 06:30:09'),(5,5,'maria_g',NULL,'$2y$12$bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb','cocinero',NULL,0,NULL,'2026-05-22 06:30:09','2026-05-22 06:30:09');
/*!40000 ALTER TABLE `credenciales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descripcionCompra`
--

DROP TABLE IF EXISTS `descripcionCompra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `descripcionCompra` (
  `id_compra` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_compra`,`id_producto`),
  KEY `fk_descripcionCompra_producto` (`id_producto`),
  CONSTRAINT `fk_descripcionCompra_compra` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_descripcionCompra_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  CONSTRAINT `chk_desccmp_cantidad` CHECK ((`cantidad` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descripcionCompra`
--

LOCK TABLES `descripcionCompra` WRITE;
/*!40000 ALTER TABLE `descripcionCompra` DISABLE KEYS */;
INSERT INTO `descripcionCompra` VALUES (1,20,80,12.00,960.00),(1,21,60,8.00,480.00),(2,29,100,4.00,400.00),(2,30,80,5.00,400.00);
/*!40000 ALTER TABLE `descripcionCompra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `descripcionVenta`
--

DROP TABLE IF EXISTS `descripcionVenta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `descripcionVenta` (
  `id_venta` int NOT NULL,
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_venta`,`id_producto`),
  KEY `fk_descripcionVenta_producto` (`id_producto`),
  CONSTRAINT `fk_descripcionVenta_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  CONSTRAINT `fk_descripcionVenta_venta` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_descvta_cantidad` CHECK ((`cantidad` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `descripcionVenta`
--

LOCK TABLES `descripcionVenta` WRITE;
/*!40000 ALTER TABLE `descripcionVenta` DISABLE KEYS */;
INSERT INTO `descripcionVenta` VALUES (2,1,2,120.00,240.00),(2,20,2,25.00,50.00),(2,25,1,45.00,45.00),(2,29,1,12.00,12.00),(2,30,1,14.00,14.00),(3,4,1,640.00,640.00),(3,21,1,18.00,18.00),(3,28,1,55.00,55.00),(4,20,2,25.00,50.00),(4,24,1,155.00,155.00);
/*!40000 ALTER TABLE `descripcionVenta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empleados`
--

DROP TABLE IF EXISTS `empleados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empleados` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicilio` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `curp` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfc` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_bancaria` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `fecha_na` date DEFAULT NULL,
  `fecha_con` date NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empleados`
--

LOCK TABLES `empleados` WRITE;
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
INSERT INTO `empleados` VALUES (1,'Ana','Martínez',NULL,NULL,NULL,NULL,NULL,NULL,'2024-01-15',1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(2,'Lucía','Hernández',NULL,NULL,NULL,NULL,NULL,NULL,'2024-03-01',1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(3,'Pedro','Ramírez',NULL,NULL,NULL,NULL,NULL,NULL,'2024-04-10',1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(4,'Carlos','López','Calle Mina 44','LOPC940404HCHPZR04','LOPC940404EF4','SANTANDER-EMP-004',9200.00,'1994-04-04','2024-05-12',1,'2026-05-22 06:30:09','2026-05-22 06:30:09'),(5,'María','Gómez','Av. Libertad 222','GOMM970707MCHLRS05','GOMM970707GH5','BANORTE-EMP-005',11000.00,'1997-07-07','2024-06-18',1,'2026-05-22 06:30:09','2026-05-22 06:30:09');
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario` (
  `id_producto` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `cantidad_min` int NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_producto`),
  CONSTRAINT `fk_inventario_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  CONSTRAINT `chk_inv_cantidad` CHECK ((`cantidad` >= 0)),
  CONSTRAINT `chk_inv_cantidad_min` CHECK ((`cantidad_min` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario`
--

LOCK TABLES `inventario` WRITE;
/*!40000 ALTER TABLE `inventario` DISABLE KEYS */;
INSERT INTO `inventario` VALUES (1,21,10,'2026-05-22 02:46:15'),(2,3,10,'2026-05-22 03:00:46'),(3,0,6,'2026-05-22 01:35:34'),(4,12,4,'2026-05-22 01:35:34'),(18,20,5,'2026-05-22 06:32:14'),(19,20,5,'2026-05-22 06:32:14'),(20,20,5,'2026-05-22 06:32:14'),(21,20,5,'2026-05-22 06:32:14'),(22,20,5,'2026-05-22 06:32:14'),(23,20,5,'2026-05-22 06:32:14'),(24,20,5,'2026-05-22 06:32:14'),(25,20,5,'2026-05-22 06:32:14'),(26,20,5,'2026-05-22 06:32:14'),(27,20,5,'2026-05-22 06:32:14'),(28,20,5,'2026-05-22 06:32:14'),(29,20,5,'2026-05-22 06:32:14'),(30,20,5,'2026-05-22 06:32:14');
/*!40000 ALTER TABLE `inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimiento_inventario`
--

DROP TABLE IF EXISTS `movimiento_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimiento_inventario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `tipo` enum('entrada','salida','ajuste','venta','compra') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `id_venta` int DEFAULT NULL,
  `id_compra` int DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_movimiento_venta` (`id_venta`),
  KEY `fk_movimiento_compra` (`id_compra`),
  KEY `idx_movimiento_producto_fecha` (`id_producto`,`fecha`),
  CONSTRAINT `fk_movimiento_compra` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id`),
  CONSTRAINT `fk_movimiento_producto` FOREIGN KEY (`id_producto`) REFERENCES `producto` (`id`),
  CONSTRAINT `fk_movimiento_venta` FOREIGN KEY (`id_venta`) REFERENCES `venta` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimiento_inventario`
--

LOCK TABLES `movimiento_inventario` WRITE;
/*!40000 ALTER TABLE `movimiento_inventario` DISABLE KEYS */;
INSERT INTO `movimiento_inventario` VALUES (1,1,'venta',2,1,NULL,'2026-05-21 20:43:12'),(2,1,'venta',2,2,NULL,'2026-05-21 20:46:15'),(3,2,'venta',2,3,NULL,'2026-05-21 21:00:46'),(14,20,'compra',80,NULL,1,'2026-05-16 10:20:00'),(15,21,'compra',60,NULL,1,'2026-05-16 10:21:00'),(16,29,'compra',100,NULL,2,'2026-05-17 11:25:00'),(17,30,'compra',80,NULL,2,'2026-05-17 11:26:00'),(18,1,'venta',-2,4,NULL,'2026-05-20 13:16:00'),(19,20,'venta',-2,4,NULL,'2026-05-20 13:16:00'),(20,25,'venta',-1,4,NULL,'2026-05-20 13:16:00'),(21,4,'venta',-1,5,NULL,'2026-05-20 14:01:00'),(22,24,'venta',-1,6,NULL,'2026-05-21 12:21:00'),(23,21,'ajuste',-2,NULL,NULL,'2026-05-18 09:15:00');
/*!40000 ALTER TABLE `movimiento_inventario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_categoria` int DEFAULT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unidad` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_producto_categoria` (`id_categoria`),
  CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`),
  CONSTRAINT `chk_producto_precios` CHECK (((`precio_venta` >= 0) and (`precio_compra` >= 0)))
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES (1,1,'Pechuga de Pollo','pieza',120.00,70.00,1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(2,1,'Alitas','orden',95.00,55.00,1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(3,2,'Refresco','lata',25.00,12.00,1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(4,3,'Combo Familiar','combo',640.00,380.00,1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(18,1,'Pierna y Muslo','pieza',85.00,48.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(19,1,'Pollo Entero','pieza',240.00,145.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(20,2,'Refresco Coca-Cola','lata',25.00,12.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(21,2,'Agua Natural','botella',18.00,8.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(22,2,'Té Helado','botella',30.00,14.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(23,3,'Combo Pareja','combo',320.00,185.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(24,3,'Combo Individual','combo',155.00,92.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(25,10,'Papas Fritas','orden',45.00,20.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(26,10,'Ensalada','porcion',40.00,18.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(27,10,'Pure de Papa','porcion',38.00,17.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(28,11,'Pay de Queso','rebanada',55.00,25.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(29,12,'Salsa BBQ','vaso',12.00,4.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14'),(30,12,'Salsa Buffalo','vaso',14.00,5.00,1,'2026-05-22 06:32:14','2026-05-22 06:32:14');
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rfc` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referencia_bancaria` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'Granja Don Pollo','Av. Reforma 123, Col. Centro','ventas@donpollo.mx','555-0100','GDP010203AB1',NULL,1,'2026-05-22 01:35:34','2026-05-22 01:35:34'),(2,'Bebidas del Norte','Calle Juarez 456, Col. Industrial','contacto@bebidasnorte.mx','555-0101','BDN040506CD2','BBVA-221',1,'2026-05-22 06:30:09','2026-05-22 06:30:09'),(3,'Salsas Mexicanas SA','Av. Tecnologico 891','ventas@salsasmex.mx','555-0102','SMS070809EF3','SANTANDER-882',1,'2026-05-22 06:30:09','2026-05-22 06:30:09'),(4,'Distribuidora La Canasta','Periferico Sur 741','pedidos@lacanasta.mx','555-0103','DLC101112GH4','HSBC-441',1,'2026-05-22 06:30:09','2026-05-22 06:30:09');
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venta`
--

DROP TABLE IF EXISTS `venta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_empleado` int NOT NULL,
  `cliente` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('pendiente','en_proceso','listo','entregado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `metodo_pago` enum('efectivo','tarjeta','transferencia') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_venta_empleado` (`id_empleado`),
  KEY `idx_venta_fecha` (`fecha`),
  KEY `idx_venta_estado` (`estado`),
  CONSTRAINT `fk_venta_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venta`
--

LOCK TABLES `venta` WRITE;
/*!40000 ALTER TABLE `venta` DISABLE KEYS */;
INSERT INTO `venta` VALUES (1,1,NULL,'pendiente','efectivo',240.00,'2026-05-21 20:43:12','2026-05-22 02:43:12','2026-05-22 02:43:12'),(2,2,NULL,'pendiente','efectivo',240.00,'2026-05-21 20:46:15','2026-05-22 02:46:15','2026-05-22 02:46:15'),(3,2,NULL,'pendiente','efectivo',190.00,'2026-05-21 21:00:46','2026-05-22 03:00:46','2026-05-22 03:00:46'),(4,2,'Juan Pérez','entregado','efectivo',370.00,'2026-05-20 13:15:00','2026-05-22 06:34:41','2026-05-22 06:34:41'),(5,2,'María López','entregado','tarjeta',713.00,'2026-05-20 14:00:00','2026-05-22 06:34:41','2026-05-22 06:34:41'),(6,4,'Cliente General','listo','efectivo',205.00,'2026-05-21 12:20:00','2026-05-22 06:34:41','2026-05-22 06:34:41');
/*!40000 ALTER TABLE `venta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-22  0:37:18
