import unittest
from selenium import webdriver
from selenium.webdriver.common.by import By
import time

class LoginTest(unittest.TestCase):

    def setUp(self):
        self.driver = webdriver.Edge()
        self.web_site = "http://localhost/Grupo9_verificacion/courier/login.php"
        self.driver.get(self.web_site)

    def tearDown(self):
        self.driver.quit()

    def test_correct_login(self):
        print("Login Correcto")
        driver = self.driver
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[1]/input').send_keys("admin@admin.com")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[2]/input').send_keys("admin123")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[3]/div[2]/button').click()
        time.sleep(2)  # Espera para asegurarse de que la redirección ocurra
        self.assertEqual(driver.current_url, "http://localhost/Grupo9_verificacion/courier/index.php?page=home")  # Verifica la URL correcta

    def test_incorrect_password(self):
        print("Contraseña Incorrecta")
        driver = self.driver
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[1]/input').send_keys("admin@admin.com")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[2]/input').send_keys("kevin")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[3]/div[2]/button').click()
        time.sleep(1)
        error_message = driver.find_element(By.XPATH, '//*[@id="login-form"]/div[1]').text
        self.assertEqual(error_message, "Usuario o contraseña incorrectos.")  # Asegúrate de que el mensaje de error sea el esperado

    def test_incorrect_id(self):
        print("ID Incorrecta")
        driver = self.driver
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[1]/input').send_keys("kevin@unmsm.edu")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[2]/input').send_keys("kevin")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[3]/div[2]/button').click()
        time.sleep(1)
        error_message = driver.find_element(By.XPATH, '//*[@id="login-form"]/div[1]').text
        self.assertEqual(error_message, "Usuario o contraseña incorrectos.")  # Asegúrate de que el mensaje de error sea el esperado

    def test_empty_id(self):
        print("Contraseña faltante")
        driver = self.driver
        id_input = driver.find_element(By.XPATH, '//*[@id="login-form"]/div[1]/input')
        id_input.send_keys("")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[2]/input').send_keys("kevin")
        driver.find_element(By.XPATH, '//*[@id="login-form"]/div[3]/div[2]/button').click()
        time.sleep(1)
        validation_message = id_input.get_attribute("validationMessage")
        self.assertEqual(validation_message, "Rellena este campo.")

if __name__ == "__main__":
    unittest.main()