<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.html");
    exit;
}

require_once('conexion.php');

// Operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_dosificacion = $_POST['id_dosificacion'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save') {
        $errors = [];
        $clave_lic = $_POST['clave_lic'] ?? '';
        $numCuenta_alumno = $_POST['numCuenta_alumno'] ?? '';
        $turno = $_POST['turno'] ?? '';
        $fecha_atcion = $_POST['fecha_atcion'] ?? '';
        $hora_atcion = $_POST['hora_atcion'] ?? '';
        $num_atcion = $_POST['num_atcion'] ?? '';
        
        // Validaciones
        if (empty($clave_lic)) {
            $errors[] = "La licenciatura es requerida";
        }
        
        if (empty($numCuenta_alumno)) {
            $errors[] = "El número de cuenta del alumno es requerido";
        }
        
        if (empty($turno)) {
            $errors[] = "El turno es requerido";
        }
        
        if (empty($fecha_atcion)) {
            $errors[] = "La fecha es requerida";
        }
        
        if (empty($hora_atcion)) {
            $errors[] = "La hora es requerida";
        }
        
        if (empty($num_atcion) || !is_numeric($num_atcion)) {
            $errors[] = "El número de atención debe ser un valor numérico";
        }
        
        if (empty($errors)) {
            try {
                // Obtener nombre del alumno
                $stmt = $conn->prepare("SELECT nombre_alumno FROM Alumno WHERE numCuenta_alumno = ?");
                $stmt->bind_param("s", $numCuenta_alumno);
                $stmt->execute();
                $result = $stmt->get_result();
                $alumno = $result->fetch_assoc();
                
                if ($_POST['form_type'] === 'create') {
                    // Crear nueva dosificación
                    $stmt = $conn->prepare("INSERT INTO Dosificacion 
                                          (id_dosificacion, clave_lic, numCuenta_alumno, nombre_alumno, turno, fecha_atcion, hora_atcion, num_atcion) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $new_id = time(); // ID temporal basado en timestamp
                    $stmt->bind_param("issssssi", $new_id, $clave_lic, $numCuenta_alumno, $alumno['nombre_alumno'], $turno, $fecha_atcion, $hora_atcion, $num_atcion);
                } else {
                    // Actualizar dosificación existente
                    $stmt = $conn->prepare("UPDATE Dosificacion SET 
                                          clave_lic = ?, numCuenta_alumno = ?, nombre_alumno = ?, turno = ?, 
                                          fecha_atcion = ?, hora_atcion = ?, num_atcion = ? 
                                          WHERE id_dosificacion = ?");
                    $stmt->bind_param("ssssssii", $clave_lic, $numCuenta_alumno, $alumno['nombre_alumno'], $turno, $fecha_atcion, $hora_atcion, $num_atcion, $id_dosificacion);
                }
                
                $stmt->execute();
                
                $_SESSION['swal'] = [
                    'title' => '¡Éxito!',
                    'text' => 'Dosificación ' . ($_POST['form_type'] === 'create' ? 'creada' : 'actualizada') . ' correctamente',
                    'icon' => 'success'
                ];
            } catch (mysqli_sql_exception $e) {
                $_SESSION['swal'] = [
                    'title' => 'Error',
                    'text' => 'Error en la base de datos: ' . $e->getMessage(),
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
    // Eliminar dosificación
    $id_dosificacion = $_GET['delete'];
    try {
        $stmt = $conn->prepare("DELETE FROM Dosificacion WHERE id_dosificacion = ?");
        $stmt->bind_param("i", $id_dosificacion);
        $stmt->execute();
        
        $_SESSION['swal'] = [
            'title' => '¡Eliminado!',
            'text' => 'Dosificación eliminada correctamente',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        $_SESSION['swal'] = [
            'title' => 'Error',
            'text' => 'No se pudo eliminar la dosificación: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }
}

// Obtener lista de dosificaciones
$query = "SELECT d.*, l.nombre_lic 
          FROM Dosificacion d
          JOIN Licenciatura l ON d.clave_lic = l.clave_lic
          ORDER BY d.fecha_atcion DESC, d.hora_atcion DESC";
$dosificaciones = $conn->query($query)->fetch_all(MYSQLI_ASSOC);

// Obtener alumnos y licenciaturas para selects
$alumnos = $conn->query("SELECT numCuenta_alumno, nombre_alumno FROM Alumno ORDER BY nombre_alumno")->fetch_all(MYSQLI_ASSOC);
$licenciaturas = $conn->query("SELECT clave_lic, nombre_lic FROM Licenciatura ORDER BY nombre_lic")->fetch_all(MYSQLI_ASSOC);

// Obtener datos para edición si existe
$editing_dosif = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM Dosificacion WHERE id_dosificacion = ?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $editing_dosif = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Dosificaciones</title>
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
            padding: 1rem;
            margin-top: 1rem;
            width: 100%;
            overflow-x: auto;
        }
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            -webkit-overflow-scrolling: touch;
        }
        .table {
            width: 100%;
            min-width: 600px;
        }
        .table th {
            background-color: var(--unam-blue);
            color: white;
            text-align: center;
            font-size: 0.9rem;
            padding: 0.5rem;
        }
        .table td {
            padding: 0.5rem;
            font-size: 0.85rem;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin: 0 2px;
            white-space: nowrap;
        }
        
        /* Media Queries para móviles */
        @media (max-width: 768px) {
            .form-container {
                padding: 0.75rem;
            }
            .table th, .table td {
                padding: 0.4rem;
                font-size: 0.8rem;
            }
            .btn-action {
                padding: 0.2rem 0.4rem;
                font-size: 0.75rem;
                margin: 2px 0;
                display: block;
                width: 100%;
            }
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
                        <h2 class="fesc-subtitle">Asignación de Dosificaciones</h2>
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
                            <h2><i class="fas fa-calendar-check me-2"></i> Gestión de Dosificaciones</h2>
                            <p class="mb-0">Asigna dosificaciones a los alumnos</p>
                        </div>
                        <button class="btn btn-primary" onclick="showCreateForm()">
                            <i class="fas fa-plus-circle me-2"></i>Nueva Dosificación
                        </button>
                        <button class="btn btn-secondary" onclick="location.href='admin.php'">
                            <i class="fas fa-arrow-left me-2"></i>Regresar
                        </button>
                    </div>
                </div>

                <!-- Formulario de creación/edición -->
                <div id="dosifFormContainer" class="form-container" style="display: none;">
                    <h3 class="form-title" id="formTitle">Nueva Dosificación</h3>
                    <form id="dosifForm" method="POST">
                        <input type="hidden" name="action" value="save">
                        <input type="hidden" name="form_type" id="formType" value="create">
                        <input type="hidden" name="id_dosificacion" id="formIdDosif" value="">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numCuenta_alumno" class="form-label">Alumno</label>
                                <select class="form-select" id="numCuenta_alumno" name="numCuenta_alumno" required>
                                    <option value="">Seleccione un alumno...</option>
                                    <?php foreach ($alumnos as $alumno): ?>
                                        <option value="<?= $alumno['numCuenta_alumno'] ?>"
                                            <?= (isset($editing_dosif) && $editing_dosif['numCuenta_alumno'] == $alumno['numCuenta_alumno'] ? 'selected' : '') ?>>
                                            <?= $alumno['nombre_alumno'] ?> (<?= $alumno['numCuenta_alumno'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="clave_lic" class="form-label">Licenciatura</label>
                                <select class="form-select" id="clave_lic" name="clave_lic" required>
                                    <option value="">Seleccione una licenciatura...</option>
                                    <?php foreach ($licenciaturas as $lic): ?>
                                        <option value="<?= $lic['clave_lic'] ?>"
                                            <?= (isset($editing_dosif) && $editing_dosif['clave_lic'] == $lic['clave_lic'] ? 'selected' : '') ?>>
                                            <?= $lic['nombre_lic'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="turno" class="form-label">Turno</label>
                                <select class="form-select" id="turno" name="turno" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Matutino" <?= (isset($editing_dosif) && $editing_dosif['turno'] == 'Matutino' ? 'selected' : '') ?>>Matutino</option>
                                    <option value="Vespertino" <?= (isset($editing_dosif) && $editing_dosif['turno'] == 'Vespertino' ? 'selected' : '') ?>>Vespertino</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="fecha_atcion" class="form-label">Fecha de Atención</label>
                                <input type="date" class="form-control" id="fecha_atcion" name="fecha_atcion" required
                                    value="<?= isset($editing_dosif) ? $editing_dosif['fecha_atcion'] : '' ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="hora_atcion" class="form-label">Hora de Atención</label>
                                <input type="time" class="form-control" id="hora_atcion" name="hora_atcion" required
                                    value="<?= isset($editing_dosif) ? substr($editing_dosif['hora_atcion'], 0, 5) : '' ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="num_atcion" class="form-label">Número de Atención</label>
                                <input type="number" class="form-control" id="num_atcion" name="num_atcion" required
                                    min="1" value="<?= isset($editing_dosif) ? $editing_dosif['num_atcion'] : '' ?>">
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

                <!-- Tabla de dosificaciones -->
                <div class="form-container">
                    <h3 class="form-title">Dosificaciones Registradas</h3>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Alumno</th>
                                    <th>Licenciatura</th>
                                    <th>Turno</th>
                                    <th>Fecha/Hora</th>
                                    <th>Núm. Atención</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($dosificaciones as $dosif): ?>
                                <tr>
                                    <td><?= $dosif['id_dosificacion'] ?></td>
                                    <td><?= htmlspecialchars($dosif['nombre_alumno']) ?> (<?= $dosif['numCuenta_alumno'] ?>)</td>
                                    <td><?= htmlspecialchars($dosif['nombre_lic']) ?></td>
                                    <td><?= $dosif['turno'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($dosif['fecha_atcion'])) ?> <?= substr($dosif['hora_atcion'], 0, 5) ?></td>
                                    <td><?= $dosif['num_atcion'] ?></td>
                                    <td>
                                        <button class="btn btn-primary btn-action" 
                                                onclick="editDosif('<?= $dosif['id_dosificacion'] ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger btn-action" 
                                                onclick="confirmDelete('<?= $dosif['id_dosificacion'] ?>')">
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
            document.getElementById('formTitle').textContent = 'Nueva Dosificación';
            document.getElementById('formType').value = 'create';
            document.getElementById('dosifFormContainer').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function hideForm() {
            document.getElementById('dosifFormContainer').style.display = 'none';
        }

        // Editar dosificación
        function editDosif(id_dosificacion) {
            window.location.href = `dosificacion.php?edit=${id_dosificacion}`;
        }

        // Eliminar dosificación con confirmación
        function confirmDelete(id_dosificacion) {
            Swal.fire({
                title: '¿Eliminar dosificación?',
                text: "¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `dosificaciones.php?delete=${id_dosificacion}`;
                }
            });
        }

        // Rellenar formulario si estamos editando
        <?php if (isset($editing_dosif)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('formTitle').textContent = 'Editar Dosificación';
                document.getElementById('formType').value = 'edit';
                document.getElementById('formIdDosif').value = '<?= $editing_dosif['id_dosificacion'] ?>';
                
                document.getElementById('dosifFormContainer').style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        <?php endif; ?>

        // Resetear formulario
        function resetForm() {
            const form = document.getElementById('dosifForm');
            form.reset();
            form.classList.remove('was-validated');
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
        }

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