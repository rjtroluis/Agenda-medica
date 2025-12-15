<?php
date_default_timezone_set('America/Mexico_City');
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

if (!isset($_SESSION['usuario'])) {
    echo "Error: Debes iniciar sesión para realizar una pregunta.";
    exit;
}

$pregunta = $_POST['pregunta'];
$especialidad = $_POST['especialidad'];
$nombre_paciente = $_SESSION['usuario']; 
$fecha_mexico = date("Y-m-d H:i:s");

$query = "INSERT INTO preguntas_salud (pregunta, nombre_paciente, especialidad, fecha) 
          VALUES ('$pregunta', '$nombre_paciente', '$especialidad', '$fecha_mexico')";

$res = insertar($query, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if ($res) {
    echo "exito";
} else {
    echo "Error al guardar la pregunta";
}
?>