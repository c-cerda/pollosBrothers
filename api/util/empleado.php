<?php
require_once __DIR__ . '/util/conec.php';

class Empleado
{
	private $id;
	private $nombre;
	private $apellido;
	private $domicilio;
	private $curp;
	private $rfc;
	private $referencia_bancaria;
	private $salario;
	private $fecha_na;
	private $fecha_con;
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

	public function getNombre()
	{
		return $this->nombre;
	}

	public function getApellido()
	{
		return $this->apellido;
	}

	public function getDomicilio()
	{
		return $this->domicilio;
	}

	public function getCurp()
	{
		return $this->curp;
	}

	public function getRfc()
	{
		return $this->rfc;
	}

	public function getReferenciaBancaria()
	{
		return $this->referencia_bancaria;
	}

	public function getSalario()
	{
		return $this->salario;
	}

	public function getFechaNa()
	{
		return $this->fecha_na;
	}

	public function getFechaCon()
	{
		return $this->fecha_con;
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

	public function setNombre($nombre)
	{
		$this->nombre = $nombre;
	}

	public function setApellido($apellido)
	{
		$this->apellido = $apellido;
	}

	public function setDomicilio($domicilio)
	{
		$this->domicilio = $domicilio;
	}

	public function setCurp($curp)
	{
		$this->curp = $curp;
	}

	public function setRfc($rfc)
	{
		$this->rfc = $rfc;
	}

	public function setReferenciaBancaria($referencia_bancaria)
	{
		$this->referencia_bancaria = $referencia_bancaria;
	}

	public function setSalario($salario)
	{
		$this->salario = $salario;
	}

	public function setFechaNa($fecha_na)
	{
		$this->fecha_na = $fecha_na;
	}

	public function setFechaCon($fecha_con)
	{
		$this->fecha_con = $fecha_con;
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
		$sql = "UPDATE empleados SET
                    nombre = ?,
                    apellido = ?,
                    domicilio = ?,
                    curp = ?,
                    rfc = ?,
                    referencia_bancaria = ?,
                    salario = ?,
                    fecha_na = ?,
                    fecha_con = ?,
                    activo = ?
                WHERE id = ?";

		$stmt = $this->conexion->prepare($sql);

		return $stmt->execute([
			$this->nombre,
			$this->apellido,
			$this->domicilio,
			$this->curp,
			$this->rfc,
			$this->referencia_bancaria,
			$this->salario,
			$this->fecha_na,
			$this->fecha_con,
			$this->activo,
			$this->id
		]);
	}
}
