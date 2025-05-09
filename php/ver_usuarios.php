<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.html");
    exit;
}

require_once('conexion.php');

// Operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Crear o Actualizar usuario
    $numcuenta = $_POST['numcuenta'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save') {
        // Validar y procesar datos
        $errors = [];
        $rol = $_POST['rol'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validaciones comunes
        if (strlen($numcuenta) !== 9 || !ctype_digit($numcuenta)) {
            $errors[] = "El número de cuenta debe tener 9 dígitos";
        }
        
        if (!in_array($rol, ['alumno', 'administrador'])) {
            $errors[] = "Rol no válido";
        }
        
        // Validaciones para creación
        if ($_POST['form_type'] === 'create' && strlen($password) < 8) {
            $errors[] = "La contraseña debe tener al menos 8 caracteres";
        }
        
        if ($_POST['form_type'] === 'create' && $password !== $confirm_password) {
            $errors[] = "Las contraseñas no coinciden";
        }
        
        // Validaciones para alumno
        if ($rol === 'alumno') {
            $nombre_alumno = $_POST['nombre_alumno'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $promedio = $_POST['promedio'] ?? 0;
            
            if (empty($nombre_alumno)) {
                $errors[] = "Nombre del alumno es requerido";
            }
            
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Correo electrónico no válido";
            }
            
            if ($promedio < 0 || $promedio > 10) {
                $errors[] = "El promedio debe estar entre 0 y 10";
            }
        }
        
        if (empty($errors)) {
            $conn->begin_transaction();
            try {
                if ($_POST['form_type'] === 'create') {
                    // Crear nuevo usuario
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO usuarios (numcuenta, password, rol) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $numcuenta, $hashed_password, $rol);
                } else {
                    // Actualizar usuario existente
                    $stmt = $conn->prepare("UPDATE usuarios SET rol = ? WHERE numcuenta = ?");
                    $stmt->bind_param("ss", $rol, $numcuenta);
                }
                $stmt->execute();
                
                // Procesar datos de alumno
                if ($rol === 'alumno') {
                    if ($_POST['form_type'] === 'create') {
                        $stmt = $conn->prepare("INSERT INTO Alumno (numCuenta_alumno, nombre_alumno, correo, promedio) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("sssd", $numcuenta, $nombre_alumno, $correo, $promedio);
                    } else {
                        $stmt = $conn->prepare("UPDATE Alumno SET nombre_alumno = ?, correo = ?, promedio = ? WHERE numCuenta_alumno = ?");
                        $stmt->bind_param("ssds", $nombre_alumno, $correo, $promedio, $numcuenta);
                    }
                    $stmt->execute();
                } else {
                    // Eliminar datos de alumno si cambia de rol
                    $stmt = $conn->prepare("DELETE FROM Alumno WHERE numCuenta_alumno = ?");
                    $stmt->bind_param("s", $numcuenta);
                    $stmt->execute();
                }
                
                $conn->commit();
                $_SESSION['swal'] = [
                    'title' => '¡Éxito!',
                    'text' => 'Usuario ' . ($_POST['form_type'] === 'create' ? 'creado' : 'actualizado') . ' correctamente',
                    'icon' => 'success'
                ];
            } catch (mysqli_sql_exception $e) {
                $conn->rollback();
                $_SESSION['swal'] = [
                    'title' => 'Error',
                    'text' => $e->getCode() == 1062 ? 'El número de cuenta ya existe' : 'Error en la base de datos: ' . $e->getMessage(),
                    'icon' => 'error'
                ];
            }
        } else {
            $_SESSION['swal'] = [
                'title' => 'Error de validación',
                'text' => implode('<br>', $errors),
                'icon' => 'error'
            ];
        }
    }
} elseif (isset($_GET['delete'])) {
    // Eliminar usuario
    $numcuenta = $_GET['delete'];
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("DELETE FROM Alumno WHERE numCuenta_alumno = ?");
        $stmt->bind_param("s", $numcuenta);
        $stmt->execute();
        
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE numcuenta = ?");
        $stmt->bind_param("s", $numcuenta);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['swal'] = [
            'title' => '¡Eliminado!',
            'text' => 'Usuario eliminado correctamente',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['swal'] = [
            'title' => 'Error',
            'text' => 'No se pudo eliminar el usuario: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }
}

// Obtener lista de usuarios
$query = "SELECT u.numcuenta, u.rol, a.nombre_alumno 
          FROM usuarios u 
          LEFT JOIN Alumno a ON u.numcuenta = a.numCuenta_alumno
          ORDER BY u.rol, u.numcuenta";
$usuarios = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Obtener datos para edición si existe
$editing_user = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE numcuenta = ?");
    $stmt->bind_param("s", $_GET['edit']);
    $stmt->execute();
    $editing_user = $stmt->get_result()->fetch_assoc();
    
    if ($editing_user['rol'] === 'alumno') {
        $stmt = $conn->prepare("SELECT * FROM Alumno WHERE numCuenta_alumno = ?");
        $stmt->bind_param("s", $_GET['edit']);
        $stmt->execute();
        $alumno_data = $stmt->get_result()->fetch_assoc();
        $editing_user = array_merge($editing_user, $alumno_data);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="../images/logo-FESC.ico" type="image/webp">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-top: 2rem;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: var(--unam-blue);
            color: white;
            text-align: center;
        }
        .alumno-fields {
            display: none;
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 1rem;
            border-left: 4px solid var(--unam-gold);
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
        }
        .password-container {
            position: relative;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            z-index: 5;
        }
    </style>
</head>
<body class="fesc-bg">
    <div class="container-fluid">
        <header class="fesc-header py-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="../images/logo.webp" alt="FESC UNAM" class="fesc-logo">
                    </div>
                    <div class="col">
                        <h1 class="fesc-title">Facultad de Estudios Superiores Cuautitlán</h1>
                        <h2 class="fesc-subtitle">Gestión de Usuarios</h2>
                    </div>
                    <div class="col-auto">
                    <button class="btn btn-danger" onclick="confirmarCierreSesion()">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </button>
                    </div>
                </div>
            </div>
        </header>

        <main class="fesc-main">
            <div class="container">
                <!-- Mostrar notificaciones -->
                <?php if (isset($_SESSION['swal'])): ?>
                    <script>
                        Swal.fire({
                            title: '<?= $_SESSION['swal']['title'] ?>',
                            html: '<?= $_SESSION['swal']['text'] ?>',
                            icon: '<?= $_SESSION['swal']['icon'] ?>',
                            confirmButtonText: 'Aceptar'
                        });
                    </script>
                    <?php unset($_SESSION['swal']); ?>
                <?php endif; ?>

                <div class="welcome-admin mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2><i class="fas fa-users me-2"></i> Gestión de Usuarios</h2>
                            <p class="mb-0">Administra los usuarios del sistema</p>
                        </div>
                        <button class="btn btn-primary" onclick="showCreateForm()">
                            <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                        </button>
                        <button class="btn btn-secondary" onclick="location.href='admin.php'">
                            <i class="fas fa-arrow-left me-2"></i>Regresar
                        </button>
                    </div>
                </div>

                <!-- Formulario de creación/edición (oculto inicialmente) -->
                <div id="userFormContainer" class="form-container" style="display: none;">
                    <h3 class="form-title" id="formTitle">Nuevo Usuario</h3>
                    <form id="userForm" method="POST">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="form_type" id="formType" value="create">
                        <input type="hidden" name="numcuenta" id="formNumCuenta" value="">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numcuenta" class="form-label">Número de Cuenta</label>
                                <input type="text" class="form-control" id="numcuenta" name="numcuenta" 
                                    pattern="[0-9]{9}" title="9 dígitos" minlength="9" maxlength="9" required
                                    <?= isset($editing_user) ? 'readonly' : '' ?>>
                                <div class="invalid-feedback">Debe contener 9 dígitos</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="rol" class="form-label">Tipo de Usuario</label>
                                <select class="form-select" id="rol" name="rol" required>
                                    <option value="">Seleccione...</option>
                                    <option value="alumno">Alumno</option>
                                    <option value="administrador">Administrador</option>
                                </select>
                                <div class="invalid-feedback">Seleccione un tipo</div>
                            </div>
                        </div>
                        
                        <div id="createPasswordFields">
                            <div class="row">
                                <div class="col-md-6 mb-3 password-container">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" minlength="8">
                                    <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('password')"></i>
                                    <div class="invalid-feedback">Mínimo 8 caracteres</div>
                                </div>
                                
                                <div class="col-md-6 mb-3 password-container">
                                    <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                    <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('confirm_password')"></i>
                                    <div class="invalid-feedback">Las contraseñas no coinciden</div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="alumnoFields" class="alumno-fields">
                            <h4 class="mb-3"><i class="fas fa-user-graduate me-2"></i> Datos del Alumno</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre_alumno" class="form-label">Nombre Completo</label>
                                    <input type="text" class="form-control" id="nombre_alumno" name="nombre_alumno">
                                    <div class="invalid-feedback">Requerido</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo">
                                    <div class="invalid-feedback">Correo válido requerido</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="promedio" class="form-label">Promedio</label>
                                    <input type="number" class="form-control" id="promedio" name="promedio" 
                                           min="0" max="10" step="0.1">
                                    <div class="invalid-feedback">Entre 0 y 10</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary me-2" onclick="hideForm()">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla de usuarios -->
                <div class="form-container">
                    <h3 class="form-title">Usuarios Registrados</h3>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número de Cuenta</th>
                                    <th>Rol</th>
                                    <th>Nombre</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= htmlspecialchars($usuario['numcuenta']) ?></td>
                                    <td><?= htmlspecialchars($usuario['rol']) ?></td>
                                    <td><?= htmlspecialchars($usuario['nombre_alumno'] ?? 'N/A') ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-action" 
                                                onclick="editUser('<?= $usuario['numcuenta'] ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-action" 
                                                onclick="confirmDelete('<?= $usuario['numcuenta'] ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>

        <footer class="fesc-footer py-3">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">A Teoloyucan Manzana 001, San Sebastian Xhala, 54840 Cuautitlán Izcalli, Méx.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <p class="mb-0">© <?= date('Y') ?> FESC UNAM - Todos los derechos reservados</p>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mostrar/ocultar formulario
        function showCreateForm() {
            resetForm();
            document.getElementById('formTitle').textContent = 'Nuevo Usuario';
            document.getElementById('formType').value = 'create';
            document.getElementById('createPasswordFields').style.display = 'block';
            document.getElementById('userFormContainer').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function hideForm() {
            document.getElementById('userFormContainer').style.display = 'none';
        }

        // Editar usuario
        function editUser(numcuenta) {
            window.location.href = `ver_usuarios.php?edit=${numcuenta}`;
        }

        // Eliminar usuario con confirmación
        function confirmDelete(numcuenta) {
            Swal.fire({
                title: '¿Eliminar usuario?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `ver_usuarios.php?delete=${numcuenta}`;
                }
            });
        }

        // Toggle para mostrar contraseña
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        }

        // Mostrar campos de alumno según rol seleccionado
        document.getElementById('rol').addEventListener('change', function() {
            const alumnoFields = document.getElementById('alumnoFields');
            if (this.value === 'alumno') {
                alumnoFields.style.display = 'block';
            } else {
                alumnoFields.style.display = 'none';
            }
        });

        // Validación de formulario
        (function() {
            'use strict';
            const form = document.getElementById('userForm');
            
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                } else {
                    // Validar contraseñas para creación
                    if (document.getElementById('formType').value === 'create') {
                        const password = document.getElementById('password').value;
                        const confirmPassword = document.getElementById('confirm_password').value;
                        
                        if (password.length < 8) {
                            event.preventDefault();
                            document.getElementById('password').classList.add('is-invalid');
                        }
                        
                        if (password !== confirmPassword) {
                            event.preventDefault();
                            document.getElementById('confirm_password').classList.add('is-invalid');
                        }
                    }
                    
                    // Validar campos de alumno si es alumno
                    if (document.getElementById('rol').value === 'alumno') {
                        const nombre = document.getElementById('nombre_alumno').value;
                        const correo = document.getElementById('correo').value;
                        
                        if (nombre.trim() === '') {
                            event.preventDefault();
                            document.getElementById('nombre_alumno').classList.add('is-invalid');
                        }
                        
                        if (!/^\S+@\S+\.\S+$/.test(correo)) {
                            event.preventDefault();
                            document.getElementById('correo').classList.add('is-invalid');
                        }
                    }
                }
                
                form.classList.add('was-validated');
            }, false);
        })();

        // Rellenar formulario si estamos editando
        <?php if (isset($editing_user)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const user = <?= json_encode($editing_user) ?>;
                
                document.getElementById('formTitle').textContent = 'Editar Usuario';
                document.getElementById('formType').value = 'edit';
                document.getElementById('formNumCuenta').value = user.numcuenta;
                document.getElementById('numcuenta').value = user.numcuenta;
                document.getElementById('rol').value = user.rol;
                document.getElementById('createPasswordFields').style.display = 'none';
                
                if (user.rol === 'alumno') {
                    document.getElementById('nombre_alumno').value = user.nombre_alumno || '';
                    document.getElementById('correo').value = user.correo || '';
                    document.getElementById('promedio').value = user.promedio || '';
                    document.getElementById('alumnoFields').style.display = 'block';
                }
                
                document.getElementById('userFormContainer').style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        <?php endif; ?>

        // Resetear formulario
        function resetForm() {
            const form = document.getElementById('userForm');
            form.reset();
            form.classList.remove('was-validated');
            document.getElementById('alumnoFields').style.display = 'none';
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
        }
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