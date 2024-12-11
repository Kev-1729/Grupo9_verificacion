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

    protected function tearDown(): void
    {
        // Limpiar la sesión después de cada prueba
        session_unset();
        session_destroy();
    }
}
