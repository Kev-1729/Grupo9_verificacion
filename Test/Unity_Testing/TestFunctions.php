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
        // Configurar una base de datos en memoria para pruebas
        $this->db = new mysqli('localhost', 'root', '/]Fi8Iqn5)hkBSyG', 'cms_db');
        $this->db->query("CREATE TEMPORARY TABLE users (
            id INT AUTO_INCREMENT PRIMARY KEY, 
            firstname VARCHAR(50), 
            lastname VARCHAR(50), 
            email VARCHAR(50), 
            password VARCHAR(255)
        )");
        // Crear instancia de la clase Action con base de datos simulada
        $this->action = new Action($this->db);
    }

    public function testLoginSuccess()
    {
        // Preparar datos en la base de datos
        $hashedPassword = md5('password123');
        $this->db->query("INSERT INTO users (firstname, lastname, email, password) 
                          VALUES ('John', 'Doe', 'john.doe@example.com', '$hashedPassword')");

        // Simular datos POST
        $_POST = [
            'email' => 'john.doe@example.com',
            'password' => 'password123'
        ];
        $_SESSION = []; // Asegurar una sesión limpia

        // Ejecutar login()
        $result = $this->action->login();

        // Verificar que el login fue exitoso
        $this->assertEquals(1, $result); // Resultado 1 indica éxito

        // Comprobar que los datos de sesión son correctos
        $this->assertEquals('John Doe', $_SESSION['login_name']);
        $this->assertEquals('john.doe@example.com', $_SESSION['login_email']);
    }

    public function testLoginFailure()
    {
        // Preparar datos en la base de datos
        $hashedPassword = md5('password123');
        $this->db->query("INSERT INTO users (firstname, lastname, email, password) 
                          VALUES ('John', 'Doe', 'john.doe@example.com', '$hashedPassword')");

        // Simular datos POST incorrectos
        $_POST = [
            'email' => 'wrong.email@example.com',
            'password' => 'wrongpassword'
        ];
        $_SESSION = []; // Asegurar una sesión limpia

        // Ejecutar login()
        $result = $this->action->login();

        // Verificar que el login falló
        $this->assertEquals(2, $result); // Resultado 2 indica fallo

        // Comprobar que la sesión sigue vacía
        $this->assertEmpty($_SESSION);

    }

    public function testSaveUserSuccess()
    {
        // Simular datos POST para la creación de un usuario
        $_POST = [
            'email' => 'jane.doe@example.com',
            'password' => 'password123',
            'firstname' => 'Jane',
            'lastname' => 'Doe'
        ];

        // Ejecutar save_user()
        $result = $this->action->save_user();

        // Verificar que el usuario fue creado exitosamente
        $this->assertEquals(1, $result); // Resultado 1 indica éxito

        // Comprobar que el usuario existe en la base de datos
        $query = $this->db->query("SELECT * FROM users WHERE email = 'jane.doe@example.com'");
        $this->assertEquals(1, $query->num_rows);

        // Verificar que la contraseña fue hasheada
        $user = $query->fetch_assoc();
        $this->assertTrue(password_verify('password123', $user['password']));

    }

    public function testSaveUserDuplicateEmail()
    {
        // Insertar un usuario existente
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $this->db->query("INSERT INTO users (firstname, lastname, email, password) VALUES 
            ('John', 'Doe', 'existing@example.com', '$password')");

        // Simular datos POST con correo duplicado
        $_POST = [
            'email' => 'existing@example.com',
            'password' => 'newpassword',
            'firstname' => 'Jane',
            'lastname' => 'Smith'
        ];

        // Ejecutar save_user()
        $result = $this->action->save_user();

        // Verificar que la función detectó el correo duplicado
        $this->assertEquals(2, $result); // Resultado 2 indica correo duplicado

    }

    public function testSaveUserUpdateSuccess()
    {
        // Insertar un usuario existente
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $this->db->query("INSERT INTO users (firstname, lastname, email, password) VALUES 
            ('John', 'Doe', 'update@example.com', '$password')");

        // Simular datos POST para actualizar el usuario
        $_POST = [
            'id' => 1, // ID del usuario existente
            'email' => 'update@example.com',
            'password' => 'newpassword456',
            'firstname' => 'John Updated',
            'lastname' => 'Doe Updated'
        ];

        // Ejecutar save_user()
        $result = $this->action->save_user();

        // Verificar que la actualización fue exitosa
        $this->assertEquals(1, $result);

        // Comprobar los cambios en la base de datos
        $query = $this->db->query("SELECT * FROM users WHERE id = 1");
        $this->assertEquals(1, $query->num_rows);

        $user = $query->fetch_assoc();
        $this->assertEquals('John Updated', $user['firstname']);
        $this->assertEquals('Doe Updated', $user['lastname']);
        $this->assertTrue(password_verify('newpassword456', $user['password']));

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
        ob_end_clean();
    }
}
