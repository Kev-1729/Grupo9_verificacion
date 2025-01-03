pipeline {
    agent any
    environment {
        SCANNER_HOME = tool 'sonar-scanner'
    }
    stages {
        stage('Git Checkout') {
            steps {
                checkout scmGit(branches: [[name: '*/main']], extensions: [], userRemoteConfigs: [[credentialsId: 'GithubToken', url: 'https://github.com/Kev-1729/Grupo9_verificacion']])
            }
        }
        stage('Install Dependencies') {
            steps {
                script {
                    dir('courier') {
                        bat 'composer update'
                        bat 'composer install'
                    }
                }
            }
        }
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
        stage('Run Specific Unit Test') {
            steps {
                script {
                        bat './courier/vendor/bin/phpunit Test/Unity_Testing/TestFunctions.php --log-junit test-results.xml'
                }
            }
        }
        stage('Functional Tests') {
            steps {
                script {
                    dir('Test/Test_Funcional') {
                        bat 'pip install -r requirements.txt'

                        bat 'python -m unittest discover -s . -p "*.py"'
                    }
                }
            }
        }
        stage('Prepare environment') {
            steps {
                script {
                    echo "Creacion de carpetas"
                    bat '''
                        if not exist "%WORKSPACE%\\JmeterCodigo" mkdir "%WORKSPACE%\\JmeterCodigo"
                        if not exist "%WORKSPACE%\\Prueba01" mkdir "%WORKSPACE%\\Prueba01"
                        if not exist "%WORKSPACE%\\Prueba01\\html-report" mkdir "%WORKSPACE%\\Prueba01\\html-report"
                    '''
                    echo "Verificando instalación de JMeter..."
                    bat "jmeter -v"
                }
            }
        }
        stage('Run JMeter test') {
            steps {
                script {
                    echo "Ejecucion de JMeter"
                    bat '''
                    jmeter -n -t "%WORKSPACE%\\JmeterCodigo\\Test_plan.jmx" -l "%WORKSPACE%\\Prueba01\\logs.jtl" -e -o "%WORKSPACE%\\Prueba01\\html-report"
                    '''
                }
            }
        }
        stage('Start ZAP') {
            steps {
                dir("C:/ZAP/ZAP/Zed Attack Proxy") {
                    bat ''' 
                    zap.bat -port 8081 -cmd -quickurl http://localhost/Grupo9_verificacion/courier/login.php -quickout ./report.html -quickprogress 
                    '''
                }
            }
        }
    }
}
