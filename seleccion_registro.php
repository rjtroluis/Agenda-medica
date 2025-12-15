<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - MediAgenda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; padding-top: 50px; }
        .card-option {
            transition: transform 0.3s, border-color 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
        }
        .card-option:hover {
            transform: translateY(-10px);
            border-color: #198754; 
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
        }
        .icon-role { font-size: 4rem; color: #198754; margin-bottom: 1rem; }
    </style>
</head>
<body>

<div class="container">
    <div class="text-center mb-5">
        <h1 class="fw-bold text-success"><i class="bi bi-hospital-fill"></i> MediAgenda</h1>
        <h3 class="mt-3">¿Cómo deseas registrarte?</h3>
        <p class="text-muted">Selecciona el tipo de cuenta que necesitas crear.</p>
    </div>

    <div class="row justify-content-center g-4">
        
        <div class="col-md-5 col-lg-4">
            <div class="card h-100 shadow-sm card-option text-center p-4">
                <div class="card-body">
                    <div class="icon-role"><i class="bi bi-person-heart"></i></div>
                    <h3 class="card-title fw-bold">Soy Paciente</h3>
                    <p class="card-text text-muted my-3">
                        Busca doctores, agenda citas y lleva el control de tu salud.
                    </p>
                    <a href="registro_paciente.php" class="btn btn-outline-success w-100 rounded-pill mt-2">
                        Crear cuenta de Paciente
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-lg-4">
            <div class="card h-100 shadow-sm card-option text-center p-4">
                <div class="card-body">
                    <div class="icon-role"><i class="bi bi-person-workspace"></i></div>
                    <h3 class="card-title fw-bold">Soy Médico</h3>
                    <p class="card-text text-muted my-3">
                        Gestiona tu agenda, publica tu perfil y recibe más pacientes.
                    </p>
                    <a href="registro_medico.php" class="btn btn-success w-100 rounded-pill mt-2">
                        Crear cuenta de Médico
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="text-center mt-5">
        <a href="index.php" class="text-decoration-none text-muted">
            <i class="bi bi-arrow-left"></i> Volver al inicio
        </a>
        <span class="mx-3">|</span>
        <a href="login.php" class="text-decoration-none fw-bold text-success">
            ¿Ya tienes cuenta? Inicia Sesión
        </a>
    </div>
</div>
</body>
</html>