<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'paciente') {
    echo "Error: No autorizado";
    exit;
}

$id_cita = $_POST['id_cita'];
$id_usuario = $_SESSION['id_usuario'];

$q_paciente = "SELECT telefono FROM pacientes WHERE id_usuario = $id_usuario";
$res_pac = seleccionar($q_paciente, DB_HOST, DB_NAME, DB_USER, DB_PASS);
$telefono_paciente = $res_pac[0][0];

$q_verificar = "SELECT id FROM citas WHERE id = $id_cita AND telefono_paciente = '$telefono_paciente'";
$es_mia = seleccionar($q_verificar, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if (count($es_mia) == 0) {
    echo "Error: Esa cita no es tuya";
    exit;
}

$query = "DELETE FROM citas WHERE id = $id_cita";
$res = ejecutar($query, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if ($res) {
    echo "exito";
} else {
    echo "Error al borrar";
}
?>