<?php
ini_set('display_errors', E_ALL);
include "includes/db_config.php";
include "includes/mysqli_aux.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    $q_usuario = "INSERT INTO usuarios (nombre_usuario, password, rol) VALUES ('$usuario', '$password', 'paciente')";
    $id_nuevo_usuario = insertar($q_usuario, DB_HOST, DB_NAME, DB_USER, DB_PASS);

    if ($id_nuevo_usuario) {
        $q_paciente = "INSERT INTO pacientes (id_usuario, nombre, email, telefono, fecha_nacimiento) 
                       VALUES ($id_nuevo_usuario, '$nombre', '$email', '$telefono', '$fecha_nacimiento')";
        
        $resultado_paciente = insertar($q_paciente, DB_HOST, DB_NAME, DB_USER, DB_PASS);

        if ($resultado_paciente) {
            header("Location: login.php?registro=exito_paciente");
            exit;
        } else {
            $mensaje = "Error al guardar tus datos personales.";
        }
    } else {
        $mensaje = "Error: Ese nombre de usuario ya está ocupado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Paciente - MediAgenda</title>
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
        .form-label { font-weight: 600; color: #495057; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-registro">
                
                <div class="header-registro">
                    <h2><i class="bi bi-person-heart"></i> Hola, Paciente</h2>
                    <p class="mb-0 opacity-75">Crea tu cuenta para agendar citas</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    <?php if($mensaje): ?>
                        <div class="alert alert-danger text-center mb-4"><?php echo $mensaje; ?></div>
                    <?php endif; ?>

                    <form action="registro_paciente.php" method="POST">
                        
                        <h6 class="text-success text-uppercase mb-3 small fw-bold ls-1">Tu Información</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" placeholder="Ej. Ana María Polo" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                                <input type="email" name="email" class="form-control" required 
                                    placeholder="ejemplo@gmail.com" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                                    oninvalid="this.setCustomValidity('Falta el @ o el dominio (ej: .com, .mx)')"
                                    oninput="this.setCustomValidity('')">
                        </div>

                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control" required 
                                    placeholder="10 dígitos" maxlength="10" pattern="[0-9]{10}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Fecha Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" required
                                    max="<?php echo date('Y-m-d'); ?>" min="1900-01-01">
                            </div>
                        </div>

                        <hr class="my-4 opacity-10">
                        <h6 class="text-success text-uppercase mb-3 small fw-bold ls-1">Datos de Ingreso</h6>

                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Crea un usuario único" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="********" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg fw-bold shadow-sm">
                                Crear mi cuenta
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