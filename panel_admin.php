<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'admin') {
    header("Location: login.php");
    exit;
}

$query = "SELECT id, nombre, especialidad, telefono, destacado FROM medicos ORDER BY id DESC";
$medicos = seleccionar($query, DB_HOST, DB_NAME, DB_USER, DB_PASS);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrador - MediAgenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
  <div class="container">
    <span class="navbar-brand mb-0 h1"><i class="bi bi-shield-lock"></i> Administración</span>
    <a href="logout.php" class="btn btn-outline-light btn-sm">Cerrar Sesión</a>
  </div>
</nav>

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Gestión de Médicos</h4>
            <span class="badge bg-primary"><?php echo count($medicos); ?> Registrados</span>
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($medicos as $med): 
                        $id = $med[0];
                        $nombre = $med[1];
                        $esp = $med[2];
                        $es_destacado = $med[4]; 
                    ?>
                    <tr>
                        <td class="fw-bold"><?php echo $nombre; ?></td>
                        <td><?php echo $esp; ?></td>
                        <td>
                            <?php if($es_destacado == 1): ?>
                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> Destacado</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Normal</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button onclick="cambiarDestacado(<?php echo $id; ?>, <?php echo $es_destacado; ?>)" 
                                    class="btn btn-sm <?php echo ($es_destacado == 1) ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                <?php if($es_destacado == 1): ?>
                                    <i class="bi bi-star"></i> Quitar
                                <?php else: ?>
                                    <i class="bi bi-star-fill"></i> Destacar
                                <?php endif; ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function cambiarDestacado(idMedico, estadoActual) {
        const accion = estadoActual == 1 ? 'quitar' : 'poner';
        const titulo = estadoActual == 1 ? '¿Quitar destacado?' : '¿Destacar médico?';

        Swal.fire({
            title: titulo,
            text: "Esto cambiará la visibilidad en la página principal.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Sí, cambiar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('id_medico', idMedico);
                formData.append('accion', accion);

                fetch('admin_acciones.php', { method: 'POST', body: formData })
                .then(response => response.text())
                .then(data => {
                    if(texto.trim() == 'exito') {
                        location.reload(); 
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