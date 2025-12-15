<?php
date_default_timezone_set('America/Mexico_City');
session_start();

include "includes/db_config.php";
include "includes/mysqli_aux.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'paciente') {
    echo "Error: Acceso denegado. Inicia sesión como paciente.";
    exit;
}

$id_usuario_paciente = $_SESSION['id_usuario'];
$id_medico = $_POST['id_medico'];
$fecha_hora_str = $_POST['fecha_hora'];
$motivo = $_POST['motivo'];

$tiempo_cita = strtotime($fecha_hora_str);
$tiempo_hoy = time(); 

if ($tiempo_cita < $tiempo_hoy) {
    echo "Error: No puedes agendar en el pasado.";
    exit;
}

$tres_meses_segundos = 90 * 24 * 60 * 60; 
if ($tiempo_cita > ($tiempo_hoy + $tres_meses_segundos)) {
    echo "Error: Solo puedes agendar con 3 meses de anticipación.";
    exit;
}

$hora = date('H', $tiempo_cita);
if ($hora < 9 || $hora > 20) {
    echo "Error: El consultorio está cerrado. Horario: 9:00 AM - 8:00 PM.";
    exit;
}

$mes_actual = date('m');
$anio_actual = date('Y');
$anio_cita = date('Y', $tiempo_cita);

if ($mes_actual != 12 && $anio_cita > $anio_actual) {
    echo "Error: Aún no abrimos la agenda del próximo año.";
    exit;
}

$q_verificar = "SELECT id FROM citas 
                WHERE id_medico = $id_medico 
                AND fecha_cita > DATE_SUB('$fecha_hora_str', INTERVAL 2 HOUR)
                AND fecha_cita < DATE_ADD('$fecha_hora_str', INTERVAL 2 HOUR)";

$citas_chocan = seleccionar($q_verificar, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if (count($citas_chocan) > 0) {
    echo "Error: Horario no disponible. Choca con otra cita";
    exit;
}

$q_paciente = "SELECT nombre, telefono FROM pacientes WHERE id_usuario = $id_usuario_paciente";
$datos_paciente = seleccionar($q_paciente, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if (count($datos_paciente) == 0) {
    echo "Error: No se encontró tu perfil de paciente.";
    exit;
}

$nombre_paciente = $datos_paciente[0][0];
$telefono_paciente = $datos_paciente[0][1];

$q_insertar = "INSERT INTO citas (id_medico, nombre_paciente, telefono_paciente, fecha_cita, motivo) 
               VALUES ($id_medico, '$nombre_paciente', '$telefono_paciente', '$fecha_hora_str', '$motivo')";

$resultado = insertar($q_insertar, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if ($resultado) {
    echo "exito";
} else {
    echo "Error: No se pudo guardar en la base de datos.";
}
?>