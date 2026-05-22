<?php
require_once "conec.php";

$db = new ConexionBD();
$conn = $db->getConexion();

if ($conn) {
	echo "Conexion exitosa :P";
} else {
	echo $db->getMensaje();
}
