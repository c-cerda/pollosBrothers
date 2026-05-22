<?php
class ConexionBD
{
	private $dsn;
	private $username;
	private $password;
	private $conexion;
	private $mensaje;

	public function __construct()
	{
		$this->dsn = "mysql:host=localhost;port=3306;dbname= db ;charset=utf8mb4";
		$this->username = 'YOUR_USER';
		$this->password = 'YOUR_PASSWORD';

		try {
			$this->conexion = new PDO($this->dsn, $this->username, $this->password);

			//  IMPORTANTE
			$this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		} catch (PDOException $e) {
			$this->mensaje = "ERROR EN LA CONEXIÓN: " . $e->getMessage();
		}
	}

	public function __destruct()
	{
		$this->conexion = null;
	}

	public function getConexion()
	{
		return $this->conexion;
	}

	public function getMensaje()
	{
		return $this->mensaje;
	}
}
