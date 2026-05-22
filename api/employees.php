<?php
// Step 1 of login: returns an HTML fragment with one tile per active employee.
// Each tile, when clicked, asks the server for the appropriate secret form.

require_once __DIR__ . '/util/conec.php';

header('Content-Type: text/html; charset=utf-8');

$db = new ConexionBD();
$conn = $db->getConexion();

if (!$conn) {
	http_response_code(500);
	echo '<p class="error">No fue posible conectar a la base de datos.</p>';
	exit;
}

$stmt = $conn->prepare(
	"SELECT e.id, e.nombre, e.apellido, c.acceso
	 FROM empleados e
	 JOIN credenciales c ON c.id_empleado = e.id
	 WHERE e.activo = TRUE
	 ORDER BY e.nombre"
);
$stmt->execute();
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$empleados) {
	echo '<p class="muted">No hay empleados registrados.</p>';
	exit;
}
?>
<div class="employee-grid">
	<?php foreach ($empleados as $emp): ?>
		<button type="button"
				class="employee-tile"
				hx-get="secret_form.php?id=<?= (int)$emp['id'] ?>"
				hx-target="#login-box"
				hx-swap="innerHTML">
			<span class="name"><?= htmlspecialchars($emp['nombre'] . ' ' . $emp['apellido'], ENT_QUOTES, 'UTF-8') ?></span>
			<span class="role"><?= htmlspecialchars($emp['acceso'], ENT_QUOTES, 'UTF-8') ?></span>
		</button>
	<?php endforeach ?>
</div>
