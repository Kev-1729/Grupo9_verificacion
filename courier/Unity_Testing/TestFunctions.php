<?php
use PHPUnit\Framework\TestCase;

class TestFunctions extends TestCase
{
    protected function setUp(): void
    {
        // Iniciar la sesión para probar $_SESSION
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function testLoginSuccess()
    {
        // Datos simulados de $_POST
        $_POST['email'] = 'test@example.com';
        $_POST['password'] = 'password';

        // Configurar una base de datos simulada
        $db = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db');

        // Simular la creación de un usuario
        $db->query("CREATE TEMPORARY TABLE users (id INT, firstname VARCHAR(50), lastname VARCHAR(50), email VARCHAR(50), password VARCHAR(255))");
        $db->query("INSERT INTO users VALUES (1, 'John', 'Doe', 'test@example.com', '" . md5('password') . "')");

        // Lógica de la función login
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $query = $db->query("SELECT * FROM users WHERE email = '$email' AND password = '$password'");
        if ($query->num_rows > 0) {
            $user = $query->fetch_assoc();
            $_SESSION['login_name'] = $user['firstname'] . ' ' . $user['lastname'];
            $_SESSION['login_email'] = $user['email'];
            $result = 1; // Éxito
        } else {
            $result = 2; // Fallo
        }

        // Verificar resultados
        $this->assertEquals(1, $result);
        $this->assertEquals('John Doe', $_SESSION['login_name']);
        $this->assertEquals('test@example.com', $_SESSION['login_email']);

        // Limpiar la base de datos temporal
        $db->close();
    }

    public function testLoginFailure()
    {
        // Datos simulados de $_POST
        $_POST['email'] = 'wrong@example.com';
        $_POST['password'] = 'wrongpassword';

        // Configurar una base de datos simulada
        $db = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db');

        // Simular la creación de un usuario
        $db->query("CREATE TEMPORARY TABLE users (id INT, firstname VARCHAR(50), lastname VARCHAR(50), email VARCHAR(50), password VARCHAR(255))");
        $db->query("INSERT INTO users VALUES (1, 'John', 'Doe', 'test@example.com', '" . md5('password') . "')");

        // Lógica de la función login
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        $query = $db->query("SELECT * FROM users WHERE email = '$email' AND password = '$password'");
        if ($query->num_rows > 0) {
            $user = $query->fetch_assoc();
            $_SESSION['login_name'] = $user['firstname'] . ' ' . $user['lastname'];
            $_SESSION['login_email'] = $user['email'];
            $result = 1; // Éxito
        } else {
            $result = 2; // Fallo
        }

        // Verificar resultados
        $this->assertEquals(2, $result);
        $this->assertEmpty($_SESSION);

        // Limpiar la base de datos temporal
        $db->close();
    }
    public function testSaveUserSuccess()
    {
        // Simulación de datos POST para crear un nuevo usuario
        $_POST = [
            'email' => 'newuser@example.com',
            'password' => 'newpassword',
            'firstname' => 'Jane',
            'lastname' => 'Smith'
        ];

        // Configurar una base de datos simulada
        $db = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db');
        $db->query("CREATE TEMPORARY TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, firstname VARCHAR(50), lastname VARCHAR(50), email VARCHAR(50), password VARCHAR(255))");

        // Simulación de la función save_user()
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $check = $db->query("SELECT * FROM users WHERE email = '{$_POST['email']}'")->num_rows;

        if ($check > 0) {
            $result = 2; // Correo duplicado
        } else {
            $result = $db->query("INSERT INTO users (firstname, lastname, email, password) VALUES ('{$_POST['firstname']}', '{$_POST['lastname']}', '{$_POST['email']}', '$password')") ? 1 : 0;
        }

        // Verificar que el usuario fue creado exitosamente
        $this->assertEquals(1, $result);
        $query = $db->query("SELECT * FROM users WHERE email = '{$_POST['email']}'");
        $this->assertEquals(1, $query->num_rows);

        // Limpiar base de datos
        $db->close();
    }

    public function testSaveUserDuplicateEmail()
    {
        // Simulación de datos POST para duplicar correo
        $_POST = [
            'email' => 'duplicate@example.com',
            'password' => 'password',
            'firstname' => 'John',
            'lastname' => 'Doe'
        ];

        // Configurar una base de datos simulada
        $db = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db');
        $db->query("CREATE TEMPORARY TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, firstname VARCHAR(50), lastname VARCHAR(50), email VARCHAR(50), password VARCHAR(255))");
        $db->query("INSERT INTO users (firstname, lastname, email, password) VALUES ('John', 'Doe', 'duplicate@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "')");

        // Simulación de la función save_user()
        $check = $db->query("SELECT * FROM users WHERE email = '{$_POST['email']}'")->num_rows;
        if ($check > 0) {
            $result = 2; // Correo duplicado
        } else {
            $result = 1; // Éxito (no debería suceder)
        }

        // Verificar que el correo duplicado devuelve 2
        $this->assertEquals(2, $result);

        // Limpiar base de datos
        $db->close();
    }

    public function testUpdateUserSuccess()
    {
        // Simulación de datos POST para actualizar un usuario existente
        $_POST = [
            'id' => 1,
            'email' => 'updated@example.com',
            'password' => 'newpassword',
            'firstname' => 'Updated',
            'lastname' => 'User'
        ];

        // Configurar una base de datos simulada
        $db = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db');
        $db->query("CREATE TEMPORARY TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, firstname VARCHAR(50), lastname VARCHAR(50), email VARCHAR(50), password VARCHAR(255))");
        $db->query("INSERT INTO users (firstname, lastname, email, password) VALUES ('John', 'Doe', 'old@example.com', '" . password_hash('password', PASSWORD_DEFAULT) . "')");

        // Simulación de la función save_user()
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $result = $db->query("UPDATE users SET firstname = '{$_POST['firstname']}', lastname = '{$_POST['lastname']}', email = '{$_POST['email']}', password = '$password' WHERE id = {$_POST['id']}") ? 1 : 0;

        // Verificar que la actualización fue exitosa
        $this->assertEquals(1, $result);
        $query = $db->query("SELECT * FROM users WHERE id = {$_POST['id']}");
        $user = $query->fetch_assoc();

        $this->assertEquals('updated@example.com', $user['email']);
        $this->assertEquals('Updated', $user['firstname']);
        $this->assertEquals('User', $user['lastname']);

        // Limpiar base de datos
        $db->close();
    }

    public function testSaveUserWithoutPassword()
    {
        // Simulación de datos POST sin contraseña
        $_POST = [
            'email' => 'nopassword@example.com',
            'firstname' => 'NoPass',
            'lastname' => 'User'
        ];

        // Configurar una base de datos simulada
        $db = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db');
        $db->query("CREATE TEMPORARY TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, firstname VARCHAR(50), lastname VARCHAR(50), email VARCHAR(50), password VARCHAR(255))");

        // Simulación de la función save_user()
        $result = $db->query("INSERT INTO users (firstname, lastname, email) VALUES ('{$_POST['firstname']}', '{$_POST['lastname']}', '{$_POST['email']}')") ? 1 : 0;

        // Verificar que el usuario se guarda sin contraseña
        $this->assertEquals(1, $result);
        $query = $db->query("SELECT * FROM users WHERE email = '{$_POST['email']}'");
        $user = $query->fetch_assoc();

        $this->assertEquals('nopassword@example.com', $user['email']);
        $this->assertNull($user['password']);

        // Limpiar base de datos
        $db->close();
    }


    protected function tearDown(): void
    {
        // Limpiar la sesión después de cada prueba
        session_unset();
        session_destroy();
    }
}
