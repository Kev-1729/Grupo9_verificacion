<?php
session_start();
ini_set('display_errors', 1);

class Action {
    private $db;

    public function __construct() {
        ob_start();
        include 'db_connect.php';
        $this->db = $conn;
    }

    function __destruct() {
        $this->db->close();
        ob_end_flush();
    }

    // Logueo de usuario
    function login() {
        if (isset($_POST['email'], $_POST['password'])) {
            $email = $this->db->real_escape_string($_POST['email']);
            $password = $_POST['password'];

            $qry = $this->db->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE email = '$email'");
            if ($qry->num_rows > 0) {
                $user = $qry->fetch_assoc();
                if (password_verify($password, $user['password'])) {  // Usando password_verify para contraseñas hash
                    foreach ($user as $key => $value) {
                        if ($key != 'password' && !is_numeric($key)) {
                            $_SESSION['login_' . $key] = $value;
                        }
                    }
                    return 1;  // Login exitoso
                }
            }
            return 2;  // Usuario o contraseña incorrectos
        }
        return 0;  // Datos faltantes en el POST
    }

    // Cerrar sesión
    function logout() {
        session_destroy();
        $_SESSION = [];
        header("Location: login.php");
        exit;
    }

    // Logueo de estudiante (con código de estudiante)
    function login2() {
        if (isset($_POST['student_code'])) {
            $student_code = $this->db->real_escape_string($_POST['student_code']);
            $qry = $this->db->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', middlename) AS name FROM students WHERE student_code = '$student_code'");
            if ($qry->num_rows > 0) {
                $student = $qry->fetch_assoc();
                foreach ($student as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['rs_' . $key] = $value;
                    }
                }
                return 1;  // Login exitoso
            }
            return 3;  // Estudiante no encontrado
        }
        return 0;  // Datos faltantes
    }

    // Guardar o actualizar usuario
    function save_user() {
        if (isset($_POST['email'], $_POST['firstname'], $_POST['lastname'])) {
            $data = [];
            $email = $this->db->real_escape_string($_POST['email']);
            $id = $_POST['id'] ?? null;
            $password = $_POST['password'] ?? '';

            // Evitar la sobreescritura de variables de sesión con datos peligrosos
            foreach ($_POST as $k => $v) {
                if (!in_array($k, ['id', 'cpass', 'password']) && !is_numeric($k)) {
                    $data[$k] = $this->db->real_escape_string($v);
                }
            }

            // Si hay una contraseña, hashearla con password_hash
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            }

            // Verificar si el email ya está registrado
            $check = $this->db->query("SELECT * FROM users WHERE email = '$email'" . (!empty($id) ? " AND id != $id" : ''))->num_rows;
            if ($check > 0) {
                return 2;  // El email ya existe
            }

            // Si no hay ID, se inserta un nuevo usuario
            if (empty($id)) {
                $columns = implode(', ', array_keys($data));
                $values = "'" . implode("', '", array_values($data)) . "'";
                $save = $this->db->query("INSERT INTO users ($columns) VALUES ($values)");
                $id = $this->db->insert_id;
            } else {
                $updateData = [];
                foreach ($data as $key => $value) {
                    $updateData[] = "$key = '$value'";
                }
                $updateData = implode(', ', $updateData);
                $save = $this->db->query("UPDATE users SET $updateData WHERE id = $id");
            }

            if ($save) {
                foreach ($data as $key => $value) {
                    if ($key != 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $value;
                    }
                }
                $_SESSION['login_id'] = $id;
                return 1;  // Usuario guardado o actualizado correctamente
            }
        }
        return 0;  // Datos faltantes
    }

    // Función para subir imágenes
    function save_image() {
        if (isset($_FILES['file'])) {
            $file = $_FILES['file'];
            if ($file['error'] == UPLOAD_ERR_OK && is_uploaded_file($file['tmp_name'])) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (in_array($file['type'], $allowedTypes)) {
                    $fname = strtotime(date("Y-m-d H:i")) . "_" . str_replace(" ", "-", $file['name']);
                    $uploadPath = '../assets/uploads/' . $fname;
                    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
                        $hostName = $_SERVER['HTTP_HOST'];
                        $path = explode('/', $_SERVER['PHP_SELF']);
                        $currentPath = '/' . $path[1];  // Ajuste de ruta relativa
                        return $protocol . '://' . $hostName . $currentPath . '/assets/uploads/' . $fname;
                    }
                }
            }
        }
        return false;  // Error al subir la imagen
    }

    // Guardar configuración del sistema
    function save_system_settings() {
        if (isset($_POST)) {
            $data = '';
            foreach ($_POST as $k => $v) {
                if (!is_numeric($k)) {
                    $data .= "$k='" . $this->db->real_escape_string($v) . "', ";
                }
            }
            $data = rtrim($data, ', ');

            // Subir imagen de portada
            if (isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != '') {
                $cover = $_FILES['cover'];
                $coverName = strtotime(date("Y-m-d H:i")) . '_' . str_replace(" ", "-", $cover['name']);
                move_uploaded_file($cover['tmp_name'], '../assets/uploads/' . $coverName);
                $data .= ", cover_img = '$coverName'";
            }

            $chk = $this->db->query("SELECT * FROM system_settings");
            if ($chk->num_rows > 0) {
                $this->db->query("UPDATE system_settings SET $data WHERE id = 1");
            } else {
                $this->db->query("INSERT INTO system_settings SET $data");
            }

            // Guardar la configuración en la sesión
            foreach ($_POST as $k => $v) {
                if (!is_numeric($k)) {
                    $_SESSION['system'][$k] = $v;
                }
            }
            if (isset($coverName)) {
                $_SESSION['system']['cover_img'] = $coverName;
            }

            return 1;
        }
        return 0;
    }

    // Guardar una sucursal
    function save_branch() {
        if (isset($_POST['branch_name'], $_POST['branch_location'])) {
            $data = [];
            $branch_code = $this->generate_branch_code();
            $data['branch_code'] = $branch_code;

            foreach ($_POST as $k => $v) {
                if (!is_numeric($k)) {
                    $data[$k] = $this->db->real_escape_string($v);
                }
            }

            if (isset($_POST['id'])) {
                $id = $_POST['id'];
                $updateData = [];
                foreach ($data as $key => $value) {
                    $updateData[] = "$key = '$value'";
                }
                $updateData = implode(', ', $updateData);
                $save = $this->db->query("UPDATE branches SET $updateData WHERE id = $id");
            } else {
                $columns = implode(', ', array_keys($data));
                $values = "'" . implode("', '", array_values($data)) . "'";
                $save = $this->db->query("INSERT INTO branches ($columns) VALUES ($values)");
            }

            return $save ? 1 : 0;
        }
        return 0;  // Datos faltantes
    }

    // Eliminar sucursal
    function delete_branch() {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $delete = $this->db->query("DELETE FROM branches WHERE id = $id");
            return $delete ? 1 : 0;
        }
        return 0;
    }

    // Generar código de sucursal
    private function generate_branch_code() {
        return strtoupper(bin2hex(random_bytes(4)));
    }
}
?>