<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

$esta_logueado = isset($_SESSION['rol']);
$es_paciente = (isset($_SESSION['rol']) && $_SESSION['rol'] == 'paciente') ? 'true' : 'false';

$busqueda = "";
$titulo_seccion = "Nuestros Especialistas Destacados"; 

if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
    $filtro_sql = " WHERE nombre LIKE '%$busqueda%' OR especialidad LIKE '%$busqueda%' ";
    $titulo_seccion = "Resultados de búsqueda";
} else {
    $filtro_sql = " WHERE destacado = 1 ";
    $titulo_seccion = "Nuestros Especialistas Destacados";
}

$query = "SELECT * FROM medicos $filtro_sql ORDER BY destacado DESC, id DESC";
$medicos = seleccionar($query, DB_HOST, DB_NAME, DB_USER, DB_PASS);
//obtienbe ultimas 3 preguntas con res
$q_preguntas = "SELECT * FROM preguntas_salud WHERE respuesta IS NOT NULL ORDER BY id DESC LIMIT 3";
$preguntas_comunidad = seleccionar($q_preguntas, DB_HOST, DB_NAME, DB_USER, DB_PASS);

$q_especialidades = "SELECT DISTINCT especialidad FROM medicos ORDER BY especialidad ASC";
$lista_especialidades = seleccionar($q_especialidades, DB_HOST, DB_NAME, DB_USER, DB_PASS);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediAgenda - Tu salud primero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        .hover-card { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
        .hover-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .hero-section {
            background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);
            color: white; padding: 4rem 0; margin-bottom: 3rem; border-radius: 0 0 30px 30px;
        }
        body { padding-top: 76px; }
        .bg-chat { background-color: #f8f9fa; border-left: 4px solid #198754; }
        .accordion-button:not(.collapsed) {
            color: #198754; background-color: #e8f5e9; box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-success" href="index.php"><i class="bi bi-hospital-fill"></i> MediAgenda</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if ($esta_logueado): ?>
            <li class="nav-item"><span class="nav-link text-dark">Hola, <b><?php echo $_SESSION['usuario']; ?></b></span></li>
            <li class="nav-item">
                <a class="btn btn-outline-success btn-sm ms-2" href="<?php echo ($_SESSION['rol'] == 'medico') ? 'panel_medico.php' : 'panel_paciente.php'; ?>">Mi Panel</a>
            </li>
            <li class="nav-item"><a class="nav-link text-danger ms-3" href="logout.php">Salir</a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Iniciar Sesión</a></li>
            <li class="nav-item ms-2"><a class="btn btn-success btn-sm rounded-pill px-3" href="seleccion_registro.php">Crear Cuenta</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="hero-section text-center shadow">
    <div class="container">
        <h1 class="display-5 fw-bold mb-3">Encuentra a tu especialista</h1>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="index.php" method="GET">
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="busqueda" class="form-control border-0" placeholder="Buscar médico..." value="<?php echo htmlspecialchars($busqueda); ?>">
                        <button class="btn btn-light text-success fw-bold" type="submit">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">

    <?php if(empty($busqueda)): ?>
    <div class="row mb-5">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-dark"><i class="bi bi-chat-heart-fill text-danger"></i> Foro de Salud</h3>
            <button class="btn btn-primary rounded-pill" onclick="hacerPregunta()">
                <i class="bi bi-pencil-square"></i> Preguntar a un Médico
            </button>
        </div>

        <?php foreach($preguntas_comunidad as $preg): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body bg-chat">
                    <div class="d-flex justify-content-between">
                        <small class="badge bg-secondary mb-2"><?php echo isset($preg[5]) ? $preg[5] : 'General'; ?></small>
                    </div>
                    <p class="fw-bold text-dark mb-1">"<?php echo $preg[1]; ?>"</p>
                    <small class="text-muted d-block mb-3">- <?php echo $preg[2]; ?></small>
                    
                    <div class="bg-white p-2 rounded border">
                        <p class="mb-0 text-success small"><i class="bi bi-check-circle-fill"></i> <?php echo $preg[3]; ?></p>
                        <small class="text-secondary fst-italic" style="font-size: 0.7rem;">Resp: <?php echo $preg[4]; ?></small>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-12 d-flex justify-content-between align-items-center border-bottom pb-2">
            <h3 class="fw-bold text-dark mb-0"><?php echo $titulo_seccion; ?></h3>
            
            <?php if($busqueda): ?>
                <a href="index.php" class="btn btn-outline-secondary btn-sm">Limpiar búsqueda</a>
            <?php endif; ?>
        </div>

        <?php if (count($medicos) > 0): ?>
            <?php foreach($medicos as $medico): 
                $id_medico = $medico[0];
                $nombre = $medico[2];
                $especialidad = $medico[4];
                $direccion = $medico[5];
                $telefono = $medico[6];
                $costo = $medico[9];
                $es_destacado = isset($medico[11]) ? $medico[11] : 0; 
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm hover-card <?php echo ($es_destacado == 1) ? 'border-warning border-2' : 'border-0'; ?>">
                    
                    <?php if($es_destacado == 1): ?>
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Top</span>
                        </div>
                    <?php endif; ?>

                    <div class="card-body p-4 text-center">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($nombre); ?>&background=random&color=fff&size=100" class="rounded-circle mb-3">
                        <h5 class="fw-bold"><?php echo $nombre; ?></h5>
                        <span class="badge bg-success-subtle text-success rounded-pill"><?php echo $especialidad; ?></span>
                        
                        <div class="text-start mt-3 ps-3">
                             <p class="card-text small mb-1 text-muted"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo $direccion; ?></p>
                             <p class="card-text small mb-1 text-muted"><i class="bi bi-telephone-fill text-success"></i> <?php echo $telefono; ?></p>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3">
                            <span class="fw-bold fs-5">$<?php echo number_format((float)$costo, 0); ?></span>
                            <button class="btn btn-success btn-sm fw-bold" onclick="procesarCita(<?php echo $id_medico; ?>, '<?php echo $nombre; ?>')">
                                Agendar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                 <h4 class="text-muted">No se encontraron resultados.</h4>
                 <p>Prueba con otra especialidad.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const esPaciente = <?php echo $es_paciente; ?>;
    const estaLogueado = <?php echo $esta_logueado ? 'true' : 'false'; ?>;

    function procesarCita(idMedico, nombreMedico) {
        if (!estaLogueado) {
            mostrarLoginRequerido('Necesitas una cuenta de paciente para agendar.');
            return;
        }
        if (!esPaciente) {
            Swal.fire('Acceso denegado', 'Solo los pacientes pueden agendar citas.', 'error');
            return;
        }
        abrirFormularioCita(idMedico, nombreMedico);
    }

    function mostrarLoginRequerido(mensaje) {
        Swal.fire({
            title: 'Inicia sesión',
            text: mensaje,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ir al Login',
            cancelButtonText: 'Cancelar'
        }).then((result) => { if (result.isConfirmed) window.location.href = 'login.php'; });
    }

    async function abrirFormularioCita(idMedico, nombreMedico) {
        const { value: formValues } = await Swal.fire({
            title: 'Agendar con ' + nombreMedico,
            html:
                '<div class="alert alert-info py-1 small">Horario: 9:00 AM - 8:00 PM</div>' +
                '<label class="form-label">Fecha y Hora</label>' +
                '<input id="swal-fecha" type="datetime-local" class="form-control mb-3">' +
                '<label class="form-label">Motivo</label>' +
                '<input id="swal-motivo" type="text" class="form-control" placeholder="Ej. Dolor de cabeza">',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Confirmar',
            preConfirm: () => {
                return [
                    document.getElementById('swal-fecha').value,
                    document.getElementById('swal-motivo').value
                ]
            }
        });

        if (formValues) {
            const [fecha, motivo] = formValues;
            if(!fecha || !motivo) return Swal.fire('Error', 'Llena todos los campos', 'error');

            const formData = new FormData();
            formData.append('id_medico', idMedico);
            formData.append('fecha_hora', fecha);
            formData.append('motivo', motivo);

            fetch('agendar_cita.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'exito') {
                    Swal.fire('¡Listo!', 'Cita agendada correctamente', 'success').then(() => {
                        window.location.href = 'panel_paciente.php';
                    });
                } else {
                    Swal.fire('Error', data, 'error');
                }
            });
        }
    }

    async function hacerPregunta() {
        if (!estaLogueado) {
            mostrarLoginRequerido('Necesitas iniciar sesión para hacer una pregunta.');
            return;
        }
        let opciones = '<option value="Medicina General">Medicina General</option>';
        <?php foreach($lista_especialidades as $esp): ?>
            opciones += '<option value="<?php echo $esp[0]; ?>"><?php echo $esp[0]; ?></option>';
        <?php endforeach; ?>

        const { value: formValues } = await Swal.fire({
            title: 'Haz tu pregunta médica',
            html:
                '<label>Especialidad:</label><select id="swal-esp" class="form-select mb-3">' + opciones + '</select>' +
                '<label>Pregunta:</label><textarea id="swal-preg" class="form-control"></textarea>',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            preConfirm: () => {
                return [
                    document.getElementById('swal-esp').value,
                    document.getElementById('swal-preg').value
                ]
            }
        });

        if (formValues) {
            const [especialidad, pregunta] = formValues;
            if(!pregunta) return;

            const formData = new FormData();
            formData.append('pregunta', pregunta);
            formData.append('especialidad', especialidad);

            fetch('guardar_pregunta.php', { method: 'POST', body: formData })
            .then(response => response.text())
            .then(data => {
                if(data.trim() === 'exito') {
                    Swal.fire('¡Enviado!', 'Tu pregunta ha sido publicada.', 'success')
                    .then(() => location.reload());
                } else {
                    Swal.fire('Error', 'No se pudo guardar la pregunta', 'error');
                }
            });
        }
    }
</script>
</body>
</html>