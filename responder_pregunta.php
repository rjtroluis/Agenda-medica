<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SESSION['rol'] == 'medico') {
    
    $id_pregunta = $_POST['id_pregunta'];
    $respuesta = $_POST['respuesta'];
    
    $id_user = $_SESSION['id_usuario'];
    $doc = seleccionar("SELECT nombre FROM medicos WHERE id_usuario = $id_user", DB_HOST, DB_NAME, DB_USER, DB_PASS);
    $nombre_medico = $doc[0][0];

    $query = "UPDATE preguntas_salud SET respuesta = '$respuesta', nombre_medico = '$nombre_medico' WHERE id = $id_pregunta";
    
    ejecutar($query, DB_HOST, DB_NAME, DB_USER, DB_PASS);
    
    header("Location: panel_medico.php");
}
?>