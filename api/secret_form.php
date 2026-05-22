<?php
// Step 2 of login: returns the appropriate secret entry form for the selected
// employee. Admin gets a password input; cajero/cocinero get the PIN keypad.

require_once __DIR__ . '/util/conec.php';

header('Content-Type: text/html; charset=utf-8');

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
	http_response_code(400);
	echo '<p class="error">Empleado inválido.</p>';
	exit;
}

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
	 WHERE e.id = :id AND e.activo = TRUE"
);
$stmt->execute([':id' => $id]);
$emp = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$emp) {
	http_response_code(404);
	echo '<p class="error">Empleado no encontrado.</p>';
	exit;
}

$nombre_completo = htmlspecialchars($emp['nombre'] . ' ' . $emp['apellido'], ENT_QUOTES, 'UTF-8');
$es_admin = ($emp['acceso'] === 'admin');
?>
<h2>Hola, <?= $nombre_completo ?></h2>

<form hx-post="verify.php"
	  hx-target="#login-error"
	  hx-swap="innerHTML"
	  id="loginForm">

	<input type="hidden" name="id_empleado" value="<?= (int)$emp['id'] ?>">

	<?php if ($es_admin): ?>

		<input type="password"
			   name="password"
			   class="password-input"
			   placeholder="Contraseña"
			   autocomplete="current-password"
			   required>

	<?php else: ?>

		<!-- 6-digit PIN. Hidden field holds the value, display shows masked digits. -->
		<input type="hidden" name="pin" id="realPin" value="" maxlength="6">
		<div class="display" id="display">••••••</div>

		<div class="keypad">
			<button type="button" onclick="appendPin('1')">1</button>
			<button type="button" onclick="appendPin('2')">2</button>
			<button type="button" onclick="appendPin('3')">3</button>

			<button type="button" onclick="appendPin('4')">4</button>
			<button type="button" onclick="appendPin('5')">5</button>
			<button type="button" onclick="appendPin('6')">6</button>

			<button type="button" onclick="appendPin('7')">7</button>
			<button type="button" onclick="appendPin('8')">8</button>
			<button type="button" onclick="appendPin('9')">9</button>

			<button type="button" class="clear" onclick="clearPin()">C</button>
			<button type="button" onclick="appendPin('0')">0</button>
			<button type="button" class="back" onclick="backPin()">←</button>
		</div>

		<script>
			function refreshDisplay() {
				const pin = document.getElementById('realPin').value;
				const masked = '•'.repeat(pin.length) + '·'.repeat(6 - pin.length);
				document.getElementById('display').textContent = masked;
			}
			function appendPin(d) {
				const f = document.getElementById('realPin');
				if (f.value.length < 6) f.value += d;
				refreshDisplay();
			}
			function backPin() {
				const f = document.getElementById('realPin');
				f.value = f.value.slice(0, -1);
				refreshDisplay();
			}
			function clearPin() {
				document.getElementById('realPin').value = '';
				refreshDisplay();
			}
			refreshDisplay();
		</script>

	<?php endif ?>

	<button type="submit" class="btn add big">Entrar</button>
	<button type="button"
			class="btn"
			hx-get="employees.php"
			hx-target="#login-box"
			hx-swap="innerHTML">Cancelar</button>

	<div id="login-error" class="error"></div>
</form>
