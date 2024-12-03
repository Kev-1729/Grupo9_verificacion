<?php include 'db_connect.php'; ?>
<?php
$data = []; // Inicializamos $data como un array vacío para evitar el error de variable no definida.

if (isset($_GET['id'])) {
    $type_arr = array('', "Admin", "User");

    // Consulta parametrizada para mayor seguridad
    $stmt = $conn->prepare("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM users WHERE id = ?");
    $stmt->bind_param('i', $_GET['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($qry = $result->fetch_assoc()) {
        $data = $qry; // Guardamos los datos en $data solo si hay resultados
    } else {
        // Si no hay resultados, puedes mostrar un mensaje o redirigir
        echo "No se encontraron resultados para este usuario.";
        exit; // Detiene la ejecución del código si no hay resultados
    }
}
?>

<div class="container-fluid">
	<div class="card card-widget widget-user shadow">
        <div class="widget-user-header bg-dark">
            <h3 class="widget-user-username"><?php echo isset($data['name']) ? ucwords($data['name']) : 'Nombre no disponible'; ?></h3>
            <h5 class="widget-user-desc"><?php echo isset($data['email']) ? $data['email'] : 'Email no disponible'; ?></h5>
        </div>
        <div class="widget-user-image">
            <?php if (empty($data['avatar']) || (!empty($data['avatar']) && !is_file('../assets/uploads/' . $data['avatar']))): ?>
            <span class="brand-image img-circle elevation-2 d-flex justify-content-center align-items-center bg-primary text-white font-weight-500" style="width: 90px; height:90px">
                <h4><?php echo isset($data['firstname'], $data['lastname']) ? strtoupper(substr($data['firstname'], 0, 1) . substr($data['lastname'], 0, 1)) : 'A'; ?></h4>
            </span>
            <?php else: ?>
            <img class="img-circle elevation-2" src="../assets/uploads/<?php echo $data['avatar']; ?>" alt="Usuario Avatar">
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <div class="container-fluid">
                <dl>
                    <dt>Dirección</dt>
                    <dd><?php echo isset($data['address']) ? $data['address'] : 'Dirección no disponible'; ?></dd>
                </dl>
                <dl>
                    <dt>Tipo de Usuario</dt>
                    <dd><?php echo isset($data['type']) ? $type_arr[$data['type']] : 'Tipo no disponible'; ?></dd>
                </dl>
            </div>
        </div>
	</div>
</div>
<div class="modal-footer display p-0 m-0">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
<style>
	#uni_modal .modal-footer {
		display: none;
	}
	#uni_modal .modal-footer.display {
		display: flex;
	}
</style>
