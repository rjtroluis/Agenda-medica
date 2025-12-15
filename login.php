<?php
session_start();
include "includes/db_config.php";
include "includes/mysqli_aux.php";

$error = "";
$mensaje_exito = "";

if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    $mensaje_exito = "¡Cuenta creada con éxito! Por favor inicia sesión.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_form = $_POST['username'];
    $password_form = $_POST['password'];

    $query = "SELECT * FROM usuarios WHERE nombre_usuario = '$usuario_form'";
    $resultados = seleccionar($query, DB_HOST, DB_NAME, DB_USER, DB_PASS);

    if (count($resultados) > 0) {
        $usuario_bd = $resultados[0];
        $password_hash = $usuario_bd[2];
        $rol = $usuario_bd[3];           
        $id_usuario = $usuario_bd[0]; 

        if (password_verify($password_form, $password_hash)) {
            $_SESSION['id_usuario'] = $id_usuario;
            $_SESSION['usuario'] = $usuario_form;
            $_SESSION['rol'] = $rol;

            if ($rol == 'medico') {
                header("Location: panel_medico.php");
            } elseif ($rol == 'paciente') {
                header("Location: panel_paciente.php");
            } elseif ($rol == 'admin') { 
                header("Location: panel_admin.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "El usuario no existe.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - MediAgenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-login {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            background: #198754;
            padding: 30px;
            text-align: center;
            color: white;
        }
        .btn-login {
            background: #198754;
            border: none;
            padding: 12px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .btn-login:hover { background: #146c43; }
    </style>
</head>
<body>

<div class="card card-login">
    <div class="login-header">
        <h2 class="mb-0"><i class="bi bi-hospital-fill"></i></h2>
        <h4 class="fw-bold">MediAgenda</h4>
        <small>Bienvenido de nuevo</small>
    </div>
    
    <div class="card-body p-4">
        
        <?php if($mensaje_exito): ?>
            <div class="alert alert-success text-center small border-0 bg-success-subtle text-success">
                <i class="bi bi-check-circle-fill"></i> <?php echo $mensaje_exito; ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-danger text-center small border-0 bg-danger-subtle text-danger">
                <i class="bi bi-exclamation-circle-fill"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">USUARIO</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control border-start-0 bg-light" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">CONTRASEÑA</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 bg-light" required>
                </div>
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary btn-login rounded-pill">INGRESAR</button>
            </div>

            <div class="text-center border-top pt-3">
                <small class="text-muted">¿Aún no tienes cuenta?</small><br>
                <a href="seleccion_registro.php" class="text-decoration-none fw-bold text-success">Regístrate aquí</a>
                <br>
                <a href="index.php" class="text-decoration-none text-muted small mt-2 d-inline-block">
                    <i class="bi bi-arrow-left"></i> Volver al inicio
                </a>
            </div>
        </form>
    </div>
</div>

</body>
</html>