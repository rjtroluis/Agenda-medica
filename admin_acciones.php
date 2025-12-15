<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    echo "Error: No eres admin";
    exit;
}

$id_medico = $_POST['id_medico'];
$accion = $_POST['accion']; 

if ($accion == 'poner') {
    $nuevo_estado = 1;
} else {
    $nuevo_estado = 0;
}

$query = "UPDATE medicos SET destacado = $nuevo_estado WHERE id = $id_medico";
$res = ejecutar($query, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if ($res) {
    echo "exito";
} else {
    echo "Error al actualizar";
}
?>