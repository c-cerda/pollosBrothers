<?php

class ConexionBD
{
	private $host = "localhost";
	private $dbname = "pollos_brothers";
	private $usuario = "AQUI VA TU ASIENTO, EL DE ROOT CSM";
	private $password = "LA PWD DEL ROOT";
	private $conexion;

	public function getConexion()
	{
		if ($this->conexion === null) {
			try {
				$this->conexion = new PDO(
					"mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
					$this->usuario,
					$this->password
				);

				$this->conexion->setAttribute(
					PDO::ATTR_ERRMODE,
					PDO::ERRMODE_EXCEPTION
				);

				$this->conexion->setAttribute(
					PDO::ATTR_DEFAULT_FETCH_MODE,
					PDO::FETCH_ASSOC
				);
			} catch (PDOException $e) {
				die("Error de conexión: " . $e->getMessage());
			}
		}

		return $this->conexion;
	}
}
