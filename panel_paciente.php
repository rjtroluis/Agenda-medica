<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'paciente') {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];
$nombre_usuario = $_SESSION['usuario'];

$q_paciente = "SELECT * FROM pacientes WHERE id_usuario = $id_usuario";
$datos_paciente = seleccionar($q_paciente, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if (empty($datos_paciente)) {
    echo "<div class='alert alert-danger m-5'>Error: No se encontró tu perfil de paciente.</div>";
    exit;
}

$mi_perfil = $datos_paciente[0];
$mi_telefono = $mi_perfil[4];
$mi_nombre = $mi_perfil[2];   

$q_citas = "SELECT 
                citas.id, 
                citas.fecha_cita, 
                citas.motivo, 
                medicos.nombre AS nombre_doctor, 
                medicos.especialidad, 
                medicos.direccion,
                medicos.costo_consulta
            FROM citas 
            JOIN medicos ON citas.id_medico = medicos.id
            WHERE citas.telefono_paciente = '$mi_telefono'
            ORDER BY citas.fecha_cita ASC";

$mis_citas = seleccionar($q_citas, DB_HOST, DB_NAME, DB_USER, DB_PASS);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - MediAgenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/datatables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; padding-top: 70px; }
        .card-cita { border-left: 5px solid #198754; transition: transform 0.2s; }
        .card-cita:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-hospital-fill"></i> MediAgenda</a>
    <div class="d-flex text-white align-items-center">
        <span class="me-3 d-none d-md-block">Hola, <?php echo $mi_nombre; ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="bi bi-box-arrow-right"></i> Salir
        </a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-calendar-event"></i> Mis Citas Programadas</h3>
        <a href="index.php" class="btn btn-primary rounded-pill">
            <i class="bi bi-plus-lg"></i> Agendar Nueva
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <?php if(count($mis_citas) > 0): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover dt-table mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Fecha y Hora</th>
                                        <th>Especialista</th>
                                        <th>Motivo</th>
                                        <th>Ubicación</th>
                                        <th>Costo</th>
                                        <th class="text-end pe-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($mis_citas as $cita): 
                                        $id_cita = $cita[0];
                                        $fecha_obj = new DateTime($cita[1]);
                                        $fecha_formato = $fecha_obj->format('d/M/Y h:i A');
                                        $doctor = $cita[3];
                                        $especialidad = $cita[4];
                                        $motivo = $cita[2];
                                        $direccion = $cita[5];
                                        $costo = $cita[6];
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                                <i class="bi bi-clock"></i> <?php echo $fecha_formato; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo $doctor; ?></div>
                                            <small class="text-muted"><?php echo $especialidad; ?></small>
                                        </td>
                                        <td><?php echo $motivo; ?></td>
                                        <td><small class="text-muted"><i class="bi bi-geo-alt"></i> <?php echo $direccion; ?></small></td>
                                        <td class="fw-bold text-secondary">$<?php echo $costo; ?></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-outline-danger rounded-pill" onclick="cancelarCita(<?php echo $id_cita; ?>)">
                                                <i class="bi bi-trash"></i> Cancelar
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <div class="mb-3"><i class="bi bi-calendar-x" style="font-size: 4rem; color: #dee2e6;"></i></div>
                    <h4>No tienes citas próximas</h4>
                    <p>Busca un especialista y agenda tu primera visita.</p>
                    <a href="index.php" class="btn btn-outline-success mt-2">Ir al Directorio</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        if ($('.dt-table').length) {
            $('.dt-table').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
                order: [[0, 'asc']] 
            });
        }
    });

function cancelarCita(idCita) {
        Swal.fire({
            title: '¿Cancelar cita?',
            text: "El horario quedará libre.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id_cita', idCita);

                fetch('eliminar_cita.php', { method: 'POST', body: formData })
                .then(response => response.text()) 
                .then(texto => {
                    if (texto.trim() == 'exito') {
                        Swal.fire('¡Cancelada!', 'Cita eliminada.', 'success')
                        .then(() => location.reload());
                    } else {
                        Swal.fire('Error', texto, 'error');
                    }
                });
            }
        });
    }
</script>

</body>
</html>