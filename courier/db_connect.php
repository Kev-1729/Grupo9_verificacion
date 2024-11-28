<?php
/*Configuración de Base de Datos */
$conn= new mysqli('localhost','root','','cms_db')or die("No se pudo conectar a mysql".mysqli_error($con));
