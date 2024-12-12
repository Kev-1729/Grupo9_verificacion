import unittest
from selenium import webdriver
from selenium.webdriver.common.by import By
import time
import random

n1 = random.randint(1, 99)  # Genera un número entero entre 1 y 10
n2 = random.randint(1, 99)  # Genera un número entero entre 1 y 10


class TestStaffManagement(unittest.TestCase):

    def setUp(self):
        self.driver = webdriver.Edge()
        self.web_site = "http://localhost/Grupo9_verificacion/courier/login.php"
        self.driver.maximize_window()
        self.driver.get(self.web_site)

        self.id = "admin@admin.com"
        self.contra = "admin123"
        self.id_incorrecto = "kevin@unmsm.edu"
        self.contrase_incorrecta = "kevin"

        self.primer_nombre = "Kevin"
        self.apellido = "Tupac Aguero"
        self.email = f"kevfd{n2}e{n1}@unmsm.edu.pe"
        self.contrasena = "123456"

        self.email2 = "Kevin"

        # Login
        self.driver.find_element(By.XPATH, '//*[@id="login-form"]/div[1]/input').send_keys(self.id)
        self.driver.find_element(By.XPATH, '//*[@id="login-form"]/div[2]/input').send_keys(self.contra)
        self.driver.find_element(By.XPATH, '//*[@id="login-form"]/div[3]/div[2]/button').click()
        time.sleep(1)

    def tearDown(self):
        self.driver.quit()

    def test_correct_scenario(self):
        print("Test correcto")
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/a').click()
        time.sleep(1)
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/ul/li[1]/a').click()
        time.sleep(1)
        # Primer nombre
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[1]/input').send_keys(self.primer_nombre)
        # Apellido
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[2]/input').send_keys(self.apellido)
        # Sucursal
        self.driver.find_element(By.XPATH, '//*[@id="select2--container"]').click()
        self.driver.find_element(By.XPATH, '/html/body/span/span/span[2]/ul/li[2]').click()
        # Email
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[4]/div/input').send_keys(self.email)
        # Contraseña
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[5]/div/input').send_keys(self.contrasena)
        self.driver.find_element(By.XPATH, '/html/body/div/div[1]/section/div/div/div/div[2]/div/button').click()
        time.sleep(5)
        self.assertEqual(self.driver.current_url, "http://localhost/Grupo9_verificacion/courier/index.php?page=staff_list")

    def test_incorrect_scenario_1(self): #Primer nombre vacio
        print("Test Nombre Vacio")
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/a').click()
        time.sleep(1)
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/ul/li[1]/a').click()
        time.sleep(1)
        mensaje = self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[1]/input')
        mensaje.send_keys("")
        # Apellido
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[2]/input').send_keys(self.apellido)
        # Sucursal
        self.driver.find_element(By.XPATH, '//*[@id="select2--container"]').click()      
        self.driver.find_element(By.XPATH, '/html/body/span/span/span[2]/ul/li[2]').click()
        # Email
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[4]/div/input').send_keys(self.email)
        # Contraseña
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[5]/div/input').send_keys(self.contrasena)
        self.driver.find_element(By.XPATH, '/html/body/div/div[1]/section/div/div/div/div[2]/div/button').click()
        time.sleep(1)
        # Verificar mensaje de error
        error_message = mensaje.get_attribute("validationMessage")
        self.assertEqual(error_message, "Rellena este campo.")

    def test_incorrect_scenario_2(self): # email sin @
        print("Test @")
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/a').click()
        time.sleep(1)
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/ul/li[1]/a').click()
        time.sleep(1)
        # Primer nombre
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[1]/input').send_keys(self.primer_nombre)
        # Apellido
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[2]/input').send_keys(self.apellido)
        # Sucursal
        self.driver.find_element(By.XPATH, '//*[@id="select2--container"]').click()
        self.driver.find_element(By.XPATH, '/html/body/span/span/span[2]/ul/li[2]').click()
        # Email
        mensaje_email = self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[4]/div/input')
        mensaje_email.send_keys(self.email2)
        # Contraseña
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[5]/div/input').send_keys(self.contrasena)
        self.driver.find_element(By.XPATH, '/html/body/div/div[1]/section/div/div/div/div[2]/div/button').click()
        time.sleep(1)
        # Verificar mensaje de error
        error_message = mensaje_email.get_attribute("validationMessage")
        self.assertEqual(error_message, f"Incluye \"@\" en la dirección de correo electrónico. En \"{self.email2}\" falta un símbolo \"@\".")
        
    def test_incorrect_scenario_3(self): # email ya existe
        print("Test email existente")
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/a').click()
        time.sleep(1)
        self.driver.find_element(By.XPATH, '/html/body/div/aside[1]/div[2]/div[4]/div/div/nav/ul/li[3]/ul/li[1]/a').click()
        time.sleep(1)
        # Primer nombre
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[1]/input').send_keys(self.primer_nombre)
        # Apellido
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[2]/div[2]/input').send_keys(self.apellido)
        # Sucursal
        self.driver.find_element(By.XPATH, '//*[@id="select2--container"]').click()
        self.driver.find_element(By.XPATH, '/html/body/span/span/span[2]/ul/li[2]').click()
        # Email
        mensaje_email = self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[4]/div/input')
        mensaje_email.send_keys("admin@admin.com")
        # Contraseña
        self.driver.find_element(By.XPATH, '//*[@id="manage-staff"]/div/div/div[5]/div/input').send_keys(self.contrasena)
        self.driver.find_element(By.XPATH, '/html/body/div/div[1]/section/div/div/div/div[2]/div/button').click()
        time.sleep(1)
        # Verificar mensaje de error
        error_message = self.driver.find_element(By.XPATH, '//*[@id="msg"]/div').text
        self.assertEqual(error_message, "Email already exist.")

if __name__ == "__main__":
    unittest.main()