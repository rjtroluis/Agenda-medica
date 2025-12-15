<?php
ini_set('display_errors', E_ALL);
include "includes/db_config.php";
include "includes/mysqli_aux.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $prefijo = $_POST['prefijo'];           
    $nombre_texto = $_POST['nombre_texto']; 
    $nombre = $prefijo . " " . $nombre_texto; 
    $cedula = $_POST['cedula'];
    $especialidad = $_POST['especialidad'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $costo = $_POST['costo'];

    $q_usuario = "INSERT INTO usuarios (nombre_usuario, password, rol) VALUES ('$usuario', '$password', 'medico')";
    $id_nuevo_usuario = insertar($q_usuario, DB_HOST, DB_NAME, DB_USER, DB_PASS);

    if ($id_nuevo_usuario) {
        $q_medico = "INSERT INTO medicos (id_usuario, nombre, cedula, especialidad, direccion, telefono, costo_consulta) 
                     VALUES ($id_nuevo_usuario, '$nombre', '$cedula', '$especialidad', '$direccion', '$telefono', $costo)";
        
        $resultado_medico = insertar($q_medico, DB_HOST, DB_NAME, DB_USER, DB_PASS);

        if ($resultado_medico) {
            header("Location: login.php?registro=exito");
            exit;
        } else {
            $mensaje = "Error: Se creó el usuario pero falló el perfil.";
        }
    } else {
        $mensaje = "Error: El usuario ya existe.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Médico - MediAgenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #e9ecef; padding-top: 40px; padding-bottom: 40px; }
        .card-registro { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .header-registro { 
            background: linear-gradient(135deg, #198754 0%, #20c997 100%); 
            color: white; 
            padding: 30px 20px; 
            text-align: center; 
        }
        .form-label { font-weight: 600; color: #495057; font-size: 0.9rem; }
        .form-control:focus, .form-select:focus { border-color: #20c997; box-shadow: 0 0 0 0.25rem rgba(32, 201, 151, 0.25); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-registro">
                
                <div class="header-registro">
                    <h2><i class="bi bi-person-workspace"></i> Hola, Doctor(a)</h2>
                    <p class="mb-0 opacity-75">Únete a la red médica más grande</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <?php if($mensaje): ?>
                        <div class="alert alert-danger text-center mb-4"><?php echo $mensaje; ?></div>
                    <?php endif; ?>
                    <form action="registro_medico.php" method="POST">
                        <h6 class="text-success text-uppercase mb-3 small fw-bold ls-1"><i class="bi bi-person-badge"></i> Tu Perfil</h6>
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <div class="input-group">
                                <select class="form-select flex-grow-0 bg-light" name="prefijo" style="width: 85px;" required>
                                    <option value="Dr.">Dr.</option>
                                    <option value="Dra.">Dra.</option>
                                </select>
                                <input type="text" name="nombre_texto" class="form-control" placeholder="Nombre y Apellidos" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cédula Profesional (SEP)</label>
                            <input type="text" name="cedula" class="form-control" placeholder="Ej. 12345678" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-7">
                                <label class="form-label">Especialidad</label>
                                <select name="especialidad" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Medicina General">Medicina General</option>
                                    <option value="Cardiología">Cardiología</option>
                                    <option value="Pediatría">Pediatría</option>
                                    <option value="Odontología">Odontología</option>
                                    <option value="Ginecología">Ginecología</option>
                                    <option value="Dermatología">Dermatología</option>
                                    <option value="Nutriología">Nutriología</option>
                                    <option value="Psicología">Psicología</option>
                                    <option value="Traumatología">Traumatología</option>
                                    <option value="Oftalmología">Oftalmología</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Costo ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">$</span>
                                    <input type="number" name="costo" class="form-control" required 
                                        placeholder="0.00" min="0" step="0.01"
                                        onkeypress="return event.charCode != 45">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-success text-uppercase mb-3 mt-4 small fw-bold ls-1"><i class="bi bi-geo-alt"></i> Consultorio</h6>

                        <div class="mb-3">
                            <label class="form-label">Teléfono (Citas)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="telefono" class="form-control" pattern="[0-9]{10}" placeholder="10 dígitos" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Dirección Completa</label>
                            <textarea name="direccion" class="form-control" rows="2" placeholder="Calle, Número, Colonia, Ciudad, Delegación" required></textarea>
                        </div>

                        <hr class="my-4 opacity-10">
                        <h6 class="text-success text-uppercase mb-3 small fw-bold ls-1"><i class="bi bi-shield-lock"></i> Datos de Acceso</h6>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="username" class="form-control" placeholder="ejemplo12" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="******" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm">
                                Registrar Consultorio
                            </button>
                            <a href="seleccion_registro.php" class="btn btn-light text-muted">Cancelar</a>
                        </div>

                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3 border-0">
                    <small>¿Ya tienes cuenta? <a href="login.php" class="text-success fw-bold text-decoration-none">Inicia Sesión</a></small>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>