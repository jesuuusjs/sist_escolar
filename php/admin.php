<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FESC UNAM - Administración</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" href="../images/logo-FESC.ico" type="image/webp">
    <!-- CSS personalizado -->
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .admin-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .admin-card .card-body {
            padding: 2rem 1.5rem;
        }
        
        .admin-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--unam-gold);
        }
        
        .admin-card-header {
            background-color: var(--unam-blue);
            color: white;
            padding: 1rem;
            text-align: center;
            font-weight: 600;
        }
        
        .welcome-admin {
            background-color: var(--unam-dark-blue);
            color: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border-left: 5px solid var(--unam-gold);
        }
        
        .logout-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background-color: #c82333;
        }
        
        .real-time-clock {
            font-size: 1.2rem;
            font-family: 'Courier New', monospace;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: inline-block;
        }
        
        .account-number {
            color: var(--unam-gold);
            font-weight: bold;
        }
        
        .last-access {
            font-size: 0.9rem;
            opacity: 0.8;
        }
    </style>
</head>
<body class="fesc-bg">
    <div class="container-fluid">
        <!-- Encabezado -->
        <header class="fesc-header py-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="../images/logo.webp" alt="FESC UNAM" class="fesc-logo">
                    </div>
                    <div class="col">
                        <h1 class="fesc-title">Facultad de Estudios Superiores Cuautitlán</h1>
                        <h2 class="fesc-subtitle">Panel de Administración</h2>
                    </div>
                    <div class="col-auto">
                    <button class="btn btn-danger" onclick="confirmarCierreSesion()">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenido principal -->
        <main class="fesc-main">
            <div class="container">
                <!-- Bienvenida -->
                <div class="welcome-admin">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="fas fa-user-shield me-2"></i> Bienvenido, <span class="account-number"><?php echo $_SESSION['numcuenta'] ?? 'Administrador'; ?></span></h2>
                            <p class="mb-0">Panel de control del sistema académico</p>
                            <p class="last-access mb-0">Último acceso: <?php echo isset($_SESSION['ultimo_acceso']) ? date('d/m/Y H:i', strtotime($_SESSION['ultimo_acceso'])) : 'Primer acceso'; ?></p>
                        </div>
                        <div class="text-end">
                            <div class="real-time-clock">
                                <i class="fas fa-clock me-2"></i>
                                <span id="live-clock"><?php echo date('d/m/Y H:i:s'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Secciones de administración -->
                <div class="row g-4">
                    <!-- Gestión de Usuarios -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <i class="fas fa-user-plus admin-icon"></i>
                                <h3>Gestión de Usuarios</h3>
                            </div>
                            <div class="card-body text-center">
                                <p>Consulta y Verifica los datos de los usuarios.</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-golden" onclick="location.href='ver_usuarios.php'">
                                        <i class="fas fa-users me-2"></i>Gestionar Usuarios
                                    </button>
                                
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Gestión Académica -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <i class="fas fa-book admin-icon"></i>
                                <h3>Gestión Académica</h3>
                            </div>
                            <div class="card-body text-center">
                                <p>Registra,Elimina y Modica Materias de licenciaturas.</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-golden" onclick="location.href='modificar_materias.php'">
                                        <i class="fas fa-edit me-2"></i>Gestionar Materias
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Dosificación -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card">
                            <div class="admin-card-header">
                                <i class="fas fa-tasks admin-icon"></i>
                                <h3>Dosificación Académica</h3>
                            </div>
                            <div class="card-body text-center">
                                <p>Verifica Hora de Inscripciones de Alumnos.</p>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-golden" onclick="location.href='dosificacion.php'">
                                        <i class="fas fa-edit me-2"></i>Gestionar Dosificación
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                </div>
            </div>
        </main>

        <!-- Pie de página -->
        <footer class="fesc-footer py-3">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">A Teoloyucan Manzana 001, San Sebastian Xhala, 54840 Cuautitlán Izcalli, Méx.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0">© <?php echo date('Y'); ?> FESC UNAM - Todos los derechos reservados</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script personalizado -->
    <script>
        // Función para actualizar el reloj en tiempo real
        function updateClock() {
            const now = new Date();
            const date = now.toLocaleDateString('es-MX');
            const time = now.toLocaleTimeString('es-MX');
            document.getElementById('live-clock').textContent = `${date} ${time}`;
        }
        
        // Actualizar el reloj cada segundo
        setInterval(updateClock, 1000);
        
        // Inicializar el reloj inmediatamente
        updateClock();
        
        // Registrar último acceso (podría usarse para analytics)
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Panel de administración cargado para: <?php echo $_SESSION['numcuenta'] ?? 'Admin'; ?>');
            
            // Aquí podrías añadir una llamada AJAX para registrar la actividad
            // fetch('../php/registrar_acceso.php', { method: 'POST' });
        });
    </script>

<script>
// Función para confirmar cierre de sesión
function confirmarCierreSesion() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        text: "¿Estás seguro de que deseas salir del sistema?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'index.html';
        }
    });
}
</script>
</body>
</html>