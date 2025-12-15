<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'medico') {
    header("Location: login.php");
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

$q_medico = "SELECT * FROM medicos WHERE id_usuario = $id_usuario";
$datos_medico = seleccionar($q_medico, DB_HOST, DB_NAME, DB_USER, DB_PASS);

if (count($datos_medico) == 0) {
    echo "Error: Usuario sin perfil médico asociado.";
    exit;
}

$mi_perfil = $datos_medico[0];
$id_medico = $mi_perfil[0]; 
$nombre_doc = $mi_perfil[2]; 
$especialidad = $mi_perfil[4];

$q_citas = "SELECT * FROM citas WHERE id_medico = $id_medico ORDER BY fecha_cita ASC";
$mis_citas = seleccionar($q_citas, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$q_preguntas_pendientes = "SELECT * FROM preguntas_salud 
                           WHERE respuesta IS NULL 
                           AND (especialidad = '$especialidad' OR especialidad = 'Medicina General')";
$preguntas_pendientes = seleccionar($q_preguntas_pendientes, DB_HOST, DB_NAME, DB_USER, DB_PASS);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Médico - MediAgenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/datatables.min.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 70px; }
        .sidebar-stat { border-left: 4px solid #198754; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#"><i class="bi bi-hospital"></i> Panel Médico</a>
    <div class="d-flex text-white align-items-center">
        <span class="me-3 d-none d-md-block">Bienvenido, <?php echo $nombre_doc; ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Salir</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <div class="row">
        
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 mb-3 sidebar-stat">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Tu Especialidad</h6>
                    <h4 class="fw-bold text-dark"><?php echo $especialidad; ?></h4>
                </div>
            </div>
            
            <div class="card shadow-sm border-0 sidebar-stat">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small">Citas Agendadas</h6>
                    <h2 class="fw-bold text-success"><?php echo count($mis_citas); ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-warning bg-opacity-10 text-warning fw-bold">
                    <i class="bi bi-question-circle"></i> Preguntas Pendientes de la Comunidad
                </div>
                <div class="card-body">
                    <?php if(count($preguntas_pendientes) > 0): ?>
                        <?php foreach($preguntas_pendientes as $p): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <p class="mb-1 fw-bold"><?php echo $p[1]; ?></p>
                                <small class="badge bg-light text-dark"><?php echo $p[5]; ?></small>
                            </div>
                            <small class="text-muted">Por: <?php echo $p[2]; ?></small>
                            <form action="responder_pregunta.php" method="POST" class="mt-2">
                                <input type="hidden" name="id_pregunta" value="<?php echo $p[0]; ?>">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="respuesta" class="form-control" placeholder="Escribe tu respuesta médica..." required>
                                    <button class="btn btn-primary" type="submit">Responder</button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small mb-0">¡Todo al día! No hay preguntas pendientes para tu especialidad.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check"></i> Tu Agenda</h5>
                </div>
                <div class="card-body">
                    <?php if(count($mis_citas) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover dt-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha/Hora</th>
                                        <th>Paciente</th>
                                        <th>Teléfono</th>
                                        <th>Motivo</th>
                                        </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($mis_citas as $cita): 
                                        $fecha = date("d/m/Y H:i", strtotime($cita[4]));
                                    ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark border"><?php echo $fecha; ?></span></td>
                                        <td class="fw-semibold"><?php echo $cita[2]; ?></td>
                                        <td><?php echo $cita[3]; ?></td>
                                        <td><?php echo $cita[5]; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <h5 class="text-muted">No tienes citas programadas aún.</h5>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('.dt-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json' },
            order: [[0, 'asc']]
        });
    });
</script>

</body>
</html>