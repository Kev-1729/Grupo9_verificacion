# Índice

1. [WEB Courier - Grupo 9](#web-courier---grupo-9)
2. [Grupo 9 - Integrantes](#grupo-9---integrantes)
3. [README - WEB COURIER](#readme---web-courier)
   - [Propósito del Proyecto]()
   - [Objetivo]()
   - [Funcionalidades principales]()
5. [Integración con Jenkins](#integración-con-Jenkins)
   - [Construcción Automática](#construcción-automática)
   - [Análisis Estático](#análisis-estático)
   - [Pruebas Unitarias](#pruebas-unitarias)
   - [Pruebas Funcionales](#pruebas-funcionales)
   - [Pruebas de Rendimiento](#pruebas-de-rendimiento)
   - [Pruebas de Seguridad](#pruebas-de-seguridad)
6. [Gestión de GitHub Issues](#gestión-de-github-issues)

# WEB Courier - Grupo 9
- **Fecha**: 04/12/2024
- **Versión**: v2.10

## Grupo 9 - Integrantes:
- [Kevin Tupac Agüero](https://github.com/Kev-1729)
- [Fabio Sthefano Sneyder Zapata Aguinaga](https://github.com/Sneyder25)
- [Carlos Ascue Orosco](https://github.com/CarlosAscueOrosco)
- [Jesus Angel Saenz Chang](https://github.com/XoChang)
- [Jocelyn Estrella Sotelo Arce](https://github.com/Jocelynsa23)
- [Kenneth Evander Ortega Moran](https://github.com/lKennethl)

# README - WEB COURIER

## Proposito del Software
Mejorar significativamente la eficiencia en la gestión de pedidos de la empresa 
ASOLUR. Se busca implementar un sistema que permita una toma de órdenes de producción 
rápida y estandarizada, así como una visualización clara y eficiente de las órdenes a través de 
reportes en tiempo real. En última instancia, el objetivo es optimizar la operación de 
ASOLUR y fortalecer su capacidad para ofrecer un servicio excepcional a sus clientes.

## Alcance
El alcance del proyecto se centra en diseñar, desarrollar e implementar un sistema 
altamente eficiente y personalizable para la gestión de pedidos en la empresa ASOLUR. La 
visión es transformar la manera en que estas empresas gestionan clientes, pedidos, 
excepciones e inventario, ofreciendo una plataforma integral que centralice y automatice 
estas operaciones. El proyecto busca optimizar procesos, mejorar la toma de decisiones, 
personalizar la plataforma según las necesidades específicas de cada empresa y proporcionar 
una solución completa e innovadora que eleve la eficiencia operativa y la satisfacción del 
cliente en la gestión de pedidos para la empresa ASOLUR.

## Principales funciones 
- Menu de Resumen
![image](https://github.com/user-attachments/assets/e9b5b3b2-4670-4afc-a96d-5675a3fbc374)
- Agregacion de Nuevo Personal
![image](https://github.com/user-attachments/assets/e53db367-8024-4bb4-ae0b-daf6f1924c57)
- Estado del Paquete
![image](https://github.com/user-attachments/assets/e362eec0-b3ea-4804-acac-7c97eae92ef5)

## Integración con Jenkins
### Construcción Automática
**Herramienta/Framework:** composer
#### Comandos:
```bash
composer install
composer validate
```
#### Integración con Jenkins:
```groovy
        stage('Install Dependencies') {
            steps {
                script {
                    dir('courier') {
                        bat 'composer install'
                    }
                }
            }
        }
```
### Análisis estático
**Herramienta/Framework:** SonarQube - Sonar Scanner
#### Evidencia
![image](https://github.com/user-attachments/assets/5f814e81-910e-49e9-b154-8e4fc8b56b91)

#### Integración con Jenkins:
```groovy
        stage("SonarQube Analysis") {
            steps {
                bat """
                $SCANNER_HOME/bin/sonar-scanner -Dsonar.url=http://localhost:9000/ ^
                -Dsonar.login=squ_9b0c8ece4c3265e51b0e9f175914ed6bd5fdab71 ^
                -Dsonar.projectKey=test-php-proyecto-grupo-9 ^
                -Dsonar.projectName=test-php-proyecto-grupo-9 ^
                -Dsonar.java.binaries=. ^
                -Dsonar.exclusions=**/.idea/**,**/assets/**,**/database/**
                """
            }
        }
```
### Pruebas unitarias
**Herramienta/Framework:** Jest para Node.js
#### Evidencia:
#### Integración con Jenkins:
- Sneyder y Joscelyn
### Pruebas Funcionales
**Herramienta/Framework:** Selenium
#### Evidencia:
```python
import unittest
from selenium import webdriver
from selenium.webdriver.common.by import By
import time

class TestStaffManagement(unittest.TestCase):

    def setUp(self):
        self.driver = webdriver.Edge()
        self.web_site = "http://localhost/courier/login.php"
        self.driver.maximize_window()
        self.driver.get(self.web_site)

        self.id = "admin@admin.com"
        self.contra = "admin123"
        self.id_incorrecto = "kevin@unmsm.edu"
        self.contrase_incorrecta = "kevin"

        self.primer_nombre = "Kevin"
        self.apellido = "Tupac Aguero"
        self.email = "kev@unmsm.edu.pe"
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
        self.assertEqual(self.driver.current_url, "http://localhost/courier/index.php?page=staff_list")

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
        print(error_message)
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
```
#### Test Funcional test_staff_management.py
![image](https://github.com/user-attachments/assets/dc6fd994-f2b8-4d01-a5a1-5115c5cab2db)
#### Test Funcional test_login.py
![image](https://github.com/user-attachments/assets/0ae99fe3-078d-4aa2-838d-51afdfffd42c)

#### Integración con Jenkins:
```groovy
stage('Run Functional Tests') {
    steps {
        script {
            dir('client') {
                bat 'npm test'
            }
        }
    }
}
```
### Pruebas de Rendimiento

**Herramienta:** Apache JMeter
- Carlos

#### Integración con Jenkins:

### Pruebas de Seguridad
- Kenneth Evander
### Gestión de GitHub Issues
#### Github Proyects:
[Repositorio de Issues en GitHub](https://github.com/users/Cristh715/projects/2)
#### Evidencia:




