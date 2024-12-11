<?php
session_start();
ini_set('display_errors', 1);

class Action
{
	private $db;

    public function __construct($db = null)
    {
        ob_start();
        if ($db) {
            $this->db = $db;
        } else {
            $conn = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db') or die("No se pudo conectar a mysql" . mysqli_error($con));
            $this->db = $conn;
        }
    }

    function __destruct()
    {
        if ($this->db !== null && $this->db instanceof mysqli) {
            $this->db->close();
        }
        ob_end_flush();
    }

	// Helper methods
	private function prepareData(array $postData, array $excludedKeys = []): array
	{
		$data = [];
		foreach ($postData as $key => $value) {
			if (!in_array($key, $excludedKeys) && !is_numeric($key)) {
				$data[$key] = $this->sanitizeInput($value);
			}
		}
		return $data;
	}

	private function sanitizeInput($input): string
	{
		return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
	}

	private function buildSetClause(array $data): string
	{
		$setClause = [];
		foreach ($data as $key => $value) {
			$setClause[] = "$key = '$value'";
		}
		return implode(', ', $setClause);
	}

	private function handleFileUpload($fileKey, $uploadDir): ?string
	{
		if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
			$file = $_FILES[$fileKey];
			$allowedTypes = ['image/jpeg', 'image/png'];
			if (!in_array($file['type'], $allowedTypes)) {
				return null;
			}
			if ($file['size'] > 1048576) { // 1MB limit
				return null;
			}
			$fileName = uniqid() . '_' . $file['name'];
			$uploadPath = $uploadDir . $fileName;
			if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
				return $fileName;
			}
		}
		return null;
	}

	private function generateUniqueReference(): string
	{
		do {
			$ref = 100;
		} while ($this->db->query("SELECT COUNT(*) AS cnt FROM parcels WHERE reference_number = '$ref'")->fetch_assoc()['cnt'] > 0);
		return $ref;
	}

	// User Authentication Methods
	function login()
	{
		extract($_POST);
		$qry = $this->db->query("SELECT *, concat(firstname,' ',lastname) as name FROM users where email = '" . $email . "' and password = '" . md5($password) . "'  ");
		if ($qry->num_rows > 0) {
			foreach ($qry->fetch_array() as $key => $value) {
				if ($key != 'password' && !is_numeric($key))
					$_SESSION['login_' . $key] = $value;
			}
			return 1;
		} else {
			return 2;
		}
	}

	function logout()
	{
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function login2()
	{
		$student_code = isset($_POST['student_code']) ? $_POST['student_code'] : '';
		$stmt = $this->db->prepare("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM students WHERE student_code = ?");
		$stmt->bind_param("s", $student_code);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			$user = $result->fetch_assoc();
			unset($user['password']);
			foreach ($user as $key => $value) {
				if ($key != 'password' && !is_numeric($key)) {
					$_SESSION['rs_' . $key] = $value;
				}
			}
			return 1;
		} else {
			return 3;
		}
	}

	function save_user()
	{
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$data = $this->prepareData($_POST, ['id', 'cpass', 'password']);
		if (!empty($password)) {
			$data['password'] = password_hash($password, PASSWORD_DEFAULT);
		}
		$check = $this->db->query("SELECT * FROM users WHERE email = '{$data['email']}'" . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
		if ($check > 0) {
			return 2;
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users SET " . $this->buildSetClause($data));
		} else {
			$save = $this->db->query("UPDATE users SET " . $this->buildSetClause($data) . " WHERE id = $id");
		}
		if ($save) {
			return 1;
		}
		return 0;
	}

	function update_user()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$data = $this->prepareData($_POST, ['id', 'cpass', 'password']);
		if (!empty($password)) {
			$data['password'] = password_hash($password, PASSWORD_DEFAULT);
		}
		$check = $this->db->query("SELECT * FROM users WHERE email = '{$data['email']}'" . (!empty($id) ? " AND id != {$id}" : ''))->num_rows;
		if ($check > 0) {
			return 2;
		}
		if (empty($id)) {
			$save = $this->db->query("INSERT INTO users SET " . $this->buildSetClause($data));
		} else {
			$save = $this->db->query("UPDATE users SET " . $this->buildSetClause($data) . " WHERE id = $id");
		}
		if ($save) {
			return 1;
		}
		return 0;
	}

	function delete_user()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
		$stmt->bind_param("i", $id);
		$delete = $stmt->execute();
		if ($delete) {
			return 1;
		} else {
			return 0;
		}
	}

	// System Settings
	function save_system_settings()
	{
		$data = $this->prepareData($_POST);
		if ($_FILES['cover']['tmp_name'] != '') {
			$avatar = $this->handleFileUpload('cover', '../assets/uploads/');
			if ($avatar !== null) {
				$data['cover_img'] = $avatar;
			}
		}
		$chk = $this->db->query("SELECT * FROM system_settings");
		if ($chk->num_rows > 0) {
			$save = $this->db->query("UPDATE system_settings SET " . $this->buildSetClause($data) . " WHERE id = " . $chk->fetch_assoc()['id']);
		} else {
			$save = $this->db->query("INSERT INTO system_settings SET " . $this->buildSetClause($data));
		}
		if ($save) {
			foreach ($data as $key => $value) {
				if (!is_numeric($key)) {
					$_SESSION['system'][$key] = $value;
				}
			}
			if ($_FILES['cover']['tmp_name'] != '') {
				$_SESSION['system']['cover_img'] = $avatar;
			}
			return 1;
		}
		return 0;
	}

	// Image Upload
	function save_image()
	{
		if ($_FILES['file']['tmp_name'] != '') {
			$fileName = strtotime(date("Y-m-d H:i")) . "_" . str_replace(" ", "-", $_FILES['file']['name']);
			$uploadPath = '../assets/uploads/' . $fileName;
			if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
				$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
				$hostName = $_SERVER['HTTP_HOST'];
				$path = explode('/', $_SERVER['PHP_SELF']);
				$currentPath = '/' . $path[1];
				return $protocol . '://' . $hostName . $currentPath . '/assets/uploads/' . $fileName;
			}
		}
		return null;
	}

	// Branch Management
	function save_branch()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$data = $this->prepareData($_POST, ['id']);
		if (empty($id)) {
			$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			do {
				$bcode = substr(str_shuffle($chars), 0, 15);
			} while ($this->db->query("SELECT * FROM branches WHERE branch_code = '$bcode'")->num_rows > 0);
			$data['branch_code'] = $bcode;
			$save = $this->db->query("INSERT INTO branches SET " . $this->buildSetClause($data));
		} else {
			$save = $this->db->query("UPDATE branches SET " . $this->buildSetClause($data) . " WHERE id = $id");
		}
		if ($save) {
			return 1;
		}
		return 0;
	}

	function delete_branch()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$stmt = $this->db->prepare("DELETE FROM branches WHERE id = ?");
		$stmt->bind_param("i", $id);
		$delete = $stmt->execute();
		if ($delete) {
			return 1;
		} else {
			return 0;
		}
	}

	// Parcel Management
	function save_parcel()
	{
		if (!isset($_POST['price']) || !is_array($_POST['price'])) {
			return 0;
		}
		$save = [];
		$ids = [];
		foreach ($_POST['price'] as $k => $v) {
			$data = $this->prepareData($_POST, ['id', 'weight', 'height', 'width', 'length', 'price']);
			if (!isset($_POST['type'])) {
				$data['type'] = '2';
			}
			$data['height'] = $this->sanitizeInput($_POST['height'][$k]);
			$data['width'] = $this->sanitizeInput($_POST['width'][$k]);
			$data['length'] = $this->sanitizeInput($_POST['length'][$k]);
			$data['weight'] = $this->sanitizeInput($_POST['weight'][$k]);
			$data['price'] = str_replace(',', '', $this->sanitizeInput($v));
			if (empty($_POST['id'])) {
				$data['reference_number'] = $this->generateUniqueReference();
				$query = "INSERT INTO parcels SET " . $this->buildSetClause($data);
			} else {
				$query = "UPDATE parcels SET " . $this->buildSetClause($data) . " WHERE id = " . $_POST['id'];
			}
			if ($this->db->query($query)) {
				if (empty($_POST['id'])) {
					$ids[] = $this->db->insert_id;
				} else {
					$ids[] = $_POST['id'];
				}
				$save[] = true;
			} else {
				$save[] = false;
			}
		}
		if (!empty($save) && !empty($ids) && !in_array(false, $save, true)) {
			return 1;
		}
		return 0;
	}

	function delete_parcel()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$stmt = $this->db->prepare("DELETE FROM parcels WHERE id = ?");
		$stmt->bind_param("i", $id);
		$delete = $stmt->execute();
		if ($delete) {
			return 1;
		} else {
			return 0;
		}
	}

	function update_parcel()
	{
		$id = isset($_POST['id']) ? $_POST['id'] : 0;
		$status = isset($_POST['status']) ? $_POST['status'] : 0;
		$stmt = $this->db->prepare("UPDATE parcels SET status = ? WHERE id = ?");
		$stmt->bind_param("ii", $status, $id);
		$update = $stmt->execute();
		$stmt2 = $this->db->prepare("INSERT INTO parcel_tracks SET status = ?, parcel_id = ?");
		$stmt2->bind_param("ii", $status, $id);
		$save = $stmt2->execute();
		if ($update && $save) {
			return 1;
		} else {
			return 0;
		}
	}

	function get_parcel_history()
	{
		$ref_no = isset($_POST['ref_no']) ? $_POST['ref_no'] : '';
		$data = [];
		$parcel = $this->db->query("SELECT * FROM parcels WHERE reference_number = '$ref_no'");
		if ($parcel->num_rows <= 0) {
			return 2;
		} else {
			$parcel = $parcel->fetch_assoc();
			$data[] = ['status' => 'Artículo Aceptado por el Mensajero', 'date_created' => date("M d, Y h:i A", strtotime($parcel['date_created']))];
			$history = $this->db->query("SELECT * FROM parcel_tracks WHERE parcel_id = {$parcel['id']}");
			$status_arr = ["Artículo Aceptado por el Mensajero", "Recogido", "Enviado", "En Tránsito", "Llegado al Destino", "En Reparto", "Listo para Recoger", "Entregado", "Recogido", "Intento de Entrega Fallido"];
			while ($row = $history->fetch_assoc()) {
				$row['date_created'] = date("M d, Y h:i A", strtotime($row['date_created']));
				$row['status'] = $status_arr[$row['status']];
				$data[] = $row;
			}
			return json_encode($data);
		}
	}

	function get_report()
	{
		$date_from = isset($_POST['date_from']) ? $_POST['date_from'] : '';
		$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : '';
		$status = isset($_POST['status']) ? $_POST['status'] : 'all';
		$data = [];
		$query = "SELECT * FROM parcels WHERE date(date_created) BETWEEN '$date_from' AND '$date_to' ";
		if ($status != 'all') {
			$query .= " AND status = $status ";
		}
		$query .= " ORDER BY UNIX_TIMESTAMP(date_created) ASC";
		$get = $this->db->query($query);
		$status_arr = ["Artículo Aceptado por el Mensajero", "Recogido", "Enviado", "En Tránsito", "Llegado al Destino", "En Reparto", "Listo para Recoger", "Entregado", "Recogido", "Intento de Entrega Fallido"];
		while ($row = $get->fetch_assoc()) {
			$row['sender_name'] = ucwords($row['sender_name']);
			$row['recipient_name'] = ucwords($row['recipient_name']);
			$row['date_created'] = date("M d, Y", strtotime($row['date_created']));
			$row['status'] = $status_arr[$row['status']];
			$row['price'] = number_format($row['price'], 2);
			$data[] = $row;
		}
		return json_encode($data);
	}
}
