<?php
require_once __DIR__ . '/conec.php';

class Producto
{
	private $id;
	private $id_categoria;
	private $nombre;
	private $unidad;
	private $precio_venta;
	private $precio_compra;
	private $activo;

	private $conexion;

	public function __construct()
	{
		$this->conexion = (new ConexionBD())->getConexion();
	}

	// =========================
	// GETTERS
	// =========================

	public function getId()
	{
		return $this->id;
	}

	public function getIdCategoria()
	{
		return $this->id_categoria;
	}

	public function getNombre()
	{
		return $this->nombre;
	}

	public function getUnidad()
	{
		return $this->unidad;
	}

	public function getPrecioVenta()
	{
		return $this->precio_venta;
	}

	public function getPrecioCompra()
	{
		return $this->precio_compra;
	}

	public function getActivo()
	{
		return $this->activo;
	}

	// =========================
	// SETTERS
	// =========================

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setIdCategoria($id_categoria)
	{
		$this->id_categoria = $id_categoria;
	}

	public function setNombre($nombre)
	{
		$this->nombre = $nombre;
	}

	public function setUnidad($unidad)
	{
		$this->unidad = $unidad;
	}

	public function setPrecioVenta($precio_venta)
	{
		$this->precio_venta = $precio_venta;
	}

	public function setPrecioCompra($precio_compra)
	{
		$this->precio_compra = $precio_compra;
	}

	public function setActivo($activo)
	{
		$this->activo = $activo;
	}

	// =========================
	// UPDATE
	// =========================

	public function update()
	{
		$sql = "UPDATE producto SET
                    id_categoria = ?,
                    nombre = ?,
                    unidad = ?,
                    precio_venta = ?,
                    precio_compra = ?,
                    activo = ?
                WHERE id = ?";

		$stmt = $this->conexion->prepare($sql);

		return $stmt->execute([
			$this->id_categoria,
			$this->nombre,
			$this->unidad,
			$this->precio_venta,
			$this->precio_compra,
			$this->activo,
			$this->id
		]);
	}
}
