<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: index.html");
    exit;
}

require_once('conexion.php');

// Operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Guardar materia
    if ($action === 'save_materia') {
        $clave_asig = $_POST['clave_asig'] ?? '';
        $nombre_asignatura = $_POST['nombre_asignatura'] ?? '';
        $creditos_asig = $_POST['creditos_asig'] ?? 0;
        $semestre = $_POST['semestre'] ?? 1;
        $clave_lic = $_POST['clave_lic'] ?? '';
        
        $errors = [];
        
        // Validaciones
        if (empty($clave_asig)) $errors[] = "La clave de asignatura es requerida";
        if (empty($nombre_asignatura)) $errors[] = "El nombre de la asignatura es requerido";
        if ($creditos_asig <= 0) $errors[] = "Los créditos deben ser mayores a 0";
        if ($semestre < 1 || $semestre > 12) $errors[] = "El semestre debe estar entre 1 y 12";
        if (empty($clave_lic)) $errors[] = "Se debe seleccionar una licenciatura";
        
        if (empty($errors)) {
            try {
                // Verificar si la materia ya existe en la misma licenciatura
                $stmt = $conn->prepare("SELECT COUNT(*) FROM Asignatura WHERE clave_asig = ? AND clave_lic = ?");
                $stmt->bind_param("ss", $clave_asig, $clave_lic);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_row()[0];
                
                if ($count > 0 && $_POST['form_type'] === 'create') {
                    $_SESSION['swal'] = [
                        'title' => 'Error',
                        'text' => 'Esta materia ya existe en la licenciatura seleccionada',
                        'icon' => 'error'
                    ];
                } else {
                    if ($_POST['form_type'] === 'create') {
                        $stmt = $conn->prepare("INSERT INTO Asignatura (clave_asig, nombre_asignatura, creditos_asig, semestre, clave_lic) 
                                             VALUES (?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssiis", $clave_asig, $nombre_asignatura, $creditos_asig, $semestre, $clave_lic);
                    } else {
                        $stmt = $conn->prepare("UPDATE Asignatura SET nombre_asignatura = ?, creditos_asig = ?, semestre = ?, clave_lic = ? 
                                             WHERE clave_asig = ?");
                        $stmt->bind_param("siiss", $nombre_asignatura, $creditos_asig, $semestre, $clave_lic, $clave_asig);
                    }
                    $stmt->execute();
                    
                    $_SESSION['swal'] = [
                        'title' => '¡Éxito!',
                        'text' => 'Asignatura ' . ($_POST['form_type'] === 'create' ? 'creada' : 'actualizada') . ' correctamente',
                        'icon' => 'success'
                    ];
                }
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
    // Guardar grupo
    elseif ($action === 'save_grupo') {
        $num_grupo = $_POST['num_grupo'] ?? '';
        $cupo_maximo = $_POST['cupo_maximo'] ?? 0;
        $clave_asig = $_POST['clave_asig'] ?? '';
        $clave_lic = $_POST['clave_lic'] ?? '';
        $id_aula = $_POST['id_aula'] ?? '';
        $clave_prof = $_POST['clave_prof'] ?? '';
        
        $errors = [];
        
        // Validaciones
        if (empty($num_grupo)) $errors[] = "El número de grupo es requerido";
        if ($cupo_maximo <= 0) $errors[] = "El cupo máximo debe ser mayor a 0";
        if (empty($clave_asig)) $errors[] = "Se debe seleccionar una asignatura";
        if (empty($clave_lic)) $errors[] = "Se debe seleccionar una licenciatura";
        if (empty($id_aula)) $errors[] = "Se debe seleccionar un aula";
        if (empty($clave_prof)) $errors[] = "Se debe seleccionar un profesor";
        
        if (empty($errors)) {
            try {
                // Verificar si el grupo ya existe
                $stmt = $conn->prepare("SELECT COUNT(*) FROM Grupo WHERE num_grupo = ? AND clave_asig = ?");
                $stmt->bind_param("ss", $num_grupo, $clave_asig);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_row()[0];
                
                if ($count > 0 && $_POST['form_type'] === 'create') {
                    $_SESSION['swal'] = [
                        'title' => 'Error',
                        'text' => 'Este grupo ya existe para esta asignatura',
                        'icon' => 'error'
                    ];
                } else {
                    if ($_POST['form_type'] === 'create') {
                        $stmt = $conn->prepare("INSERT INTO Grupo (num_grupo, cupo_maximo, inscritos, clave_lic, clave_asig, id_aula, clave_prof) 
                                            VALUES (?, ?, 0, ?, ?, ?, ?)");
                        $stmt->bind_param("sissis", $num_grupo, $cupo_maximo, $clave_lic, $clave_asig, $id_aula, $clave_prof);
                    } else {
                        $stmt = $conn->prepare("UPDATE Grupo SET cupo_maximo = ?, clave_lic = ?, clave_asig = ?, id_aula = ?, clave_prof = ? 
                                            WHERE num_grupo = ?");
                        $stmt->bind_param("ississ", $cupo_maximo, $clave_lic, $clave_asig, $id_aula, $clave_prof, $num_grupo);
                    }
                    $stmt->execute();
                    
                    $_SESSION['swal'] = [
                        'title' => '¡Éxito!',
                        'text' => 'Grupo ' . ($_POST['form_type'] === 'create' ? 'creado' : 'actualizado') . ' correctamente',
                        'icon' => 'success'
                    ];
                }
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
} 
// Eliminar materia
elseif (isset($_GET['delete_materia'])) {
    $clave_asig = $_GET['delete_materia'];
    try {
        // Verificar si hay grupos asociados
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Grupo WHERE clave_asig = ?");
        $stmt->bind_param("s", $clave_asig);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_row()[0];
        
        if ($count > 0) {
            $_SESSION['swal'] = [
                'title' => 'Error',
                'text' => 'No se puede eliminar la asignatura porque tiene grupos asociados',
                'icon' => 'error'
            ];
        } else {
            $stmt = $conn->prepare("DELETE FROM Asignatura WHERE clave_asig = ?");
            $stmt->bind_param("s", $clave_asig);
            $stmt->execute();
            
            $_SESSION['swal'] = [
                'title' => '¡Eliminado!',
                'text' => 'Asignatura eliminada correctamente',
                'icon' => 'success'
            ];
        }
    } catch (Exception $e) {
        $_SESSION['swal'] = [
            'title' => 'Error',
            'text' => 'No se pudo eliminar la asignatura: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }
} 
// Eliminar grupo
elseif (isset($_GET['delete_grupo'])) {
    $num_grupo = $_GET['delete_grupo'];
    try {
        $stmt = $conn->prepare("DELETE FROM Grupo WHERE num_grupo = ?");
        $stmt->bind_param("s", $num_grupo);
        $stmt->execute();
        
        $_SESSION['swal'] = [
            'title' => '¡Eliminado!',
            'text' => 'Grupo eliminado correctamente',
            'icon' => 'success'
        ];
    } catch (Exception $e) {
        $_SESSION['swal'] = [
            'title' => 'Error',
            'text' => 'No se pudo eliminar el grupo: ' . $e->getMessage(),
            'icon' => 'error'
        ];
    }
}

// Obtener datos
$asignaturas = $conn->query("
    SELECT a.*, l.nombre_lic 
    FROM Asignatura a
    JOIN Licenciatura l ON a.clave_lic = l.clave_lic
    ORDER BY a.semestre, a.nombre_asignatura
")->fetch_all(MYSQLI_ASSOC);

$grupos = $conn->query("
    SELECT g.*, a.nombre_asignatura, p.nombre_prof, au.nombre_aula, l.nombre_lic
    FROM Grupo g
    JOIN Asignatura a ON g.clave_asig = a.clave_asig
    JOIN Licenciatura l ON g.clave_lic = l.clave_lic
    LEFT JOIN Profesor p ON g.clave_prof = p.clave_prof
    LEFT JOIN Aula au ON g.id_aula = au.id_aula
    ORDER BY g.clave_asig, g.num_grupo
")->fetch_all(MYSQLI_ASSOC);

$licenciaturas = $conn->query("SELECT clave_lic, nombre_lic FROM Licenciatura ORDER BY nombre_lic")->fetch_all(MYSQLI_ASSOC);
$profesores = $conn->query("SELECT clave_prof, nombre_prof FROM Profesor ORDER BY nombre_prof")->fetch_all(MYSQLI_ASSOC);
$aulas = $conn->query("SELECT id_aula, nombre_aula FROM Aula ORDER BY nombre_aula")->fetch_all(MYSQLI_ASSOC);

// Obtener datos para edición
$editing_materia = null;
if (isset($_GET['edit_materia'])) {
    $stmt = $conn->prepare("SELECT * FROM Asignatura WHERE clave_asig = ?");
    $stmt->bind_param("s", $_GET['edit_materia']);
    $stmt->execute();
    $editing_materia = $stmt->get_result()->fetch_assoc();
}

$editing_grupo = null;
if (isset($_GET['edit_grupo'])) {
    $stmt = $conn->prepare("SELECT * FROM Grupo WHERE num_grupo = ?");
    $stmt->bind_param("s", $_GET['edit_grupo']);
    $stmt->execute();
    $editing_grupo = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Académica</title>
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
            padding: 20px;
            margin-bottom: 20px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .tab-content {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 0 0 10px 10px;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
    <header class="fesc-header py-3">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="../images/logo.webp" alt="FESC UNAM" class="fesc-logo">
                    </div>
                    <div class="col">
                        <h1 class="fesc-title">Facultad de Estudios Superiores Cuautitlán</h1>
                        <h2 class="fesc-subtitle">Gestion de Asignaturas</h2>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-danger" onclick="confirmarcierreSesion()">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </button>
                    </div>
                </div>
            </div>
        </header>
        <main class="fesc-main">
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
                            <h2><i class="fas fa-book me-2"></i> Gestión de Materias</h2>
                            <p class="mb-0">Asigna Materias a Carreras de la Facultad</p>
                        </div>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="materias-tab" data-bs-toggle="tab" data-bs-target="#materias" type="button" role="tab">Materias</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="grupos-tab" data-bs-toggle="tab" data-bs-target="#grupos" type="button" role="tab">Grupos</button>
            </li>
        </ul>
                        <button class="btn btn-secondary" onclick="location.href='admin.php'">
                            <i class="fas fa-arrow-left me-2"></i>Regresar
                        </button>
                    </div>
                    <div class="tab-content" id="myTabContent">
            <!-- Pestaña de Materias -->
            <div class="tab-pane fade show active" id="materias" role="tabpanel">
                <div class="form-container">
                    <h4><?= isset($editing_materia) ? 'Editar Materia' : 'Nueva Materia' ?></h4>
                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="action" value="save_materia">
                        <input type="hidden" name="form_type" value="<?= isset($editing_materia) ? 'edit' : 'create' ?>">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="clave_asig" class="form-label">Clave de Materia</label>
                                <input type="text" class="form-control" id="clave_asig" name="clave_asig" 
                                    value="<?= $editing_materia['clave_asig'] ?? '' ?>" required>
                                <div class="invalid-feedback">Campo requerido</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nombre_asignatura" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre_asignatura" name="nombre_asignatura" 
                                    value="<?= $editing_materia['nombre_asignatura'] ?? '' ?>" required>
                                <div class="invalid-feedback">Campo requerido</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="creditos_asig" class="form-label">Créditos</label>
                                <input type="number" class="form-control" id="creditos_asig" name="creditos_asig" 
                                    value="<?= $editing_materia['creditos_asig'] ?? '' ?>" min="1" required>
                                <div class="invalid-feedback">Mínimo 1</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="semestre" class="form-label">Semestre</label>
                                <input type="number" class="form-control" id="semestre" name="semestre" 
                                    value="<?= $editing_materia['semestre'] ?? '' ?>" min="1" max="12" required>
                                <div class="invalid-feedback">Entre 1-12</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="clave_lic" class="form-label">Licenciatura</label>
                                <select class="form-select" id="clave_lic" name="clave_lic" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($licenciaturas as $lic): ?>
                                        <option value="<?= $lic['clave_lic'] ?>" 
                                            <?= (isset($editing_materia) && $editing_materia['clave_lic'] == $lic['clave_lic']) ? 'selected' : '' ?>>
                                            <?= $lic['nombre_lic'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione una licenciatura</div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end mb-3">
                                <div class="w-100">
                                    <?php if (isset($editing_materia)): ?>
                                        <a href="modificar_materias.php" class="btn btn-secondary me-2">Cancelar</a>
                                    <?php endif; ?>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Clave</th>
                                <th>Asignatura</th>
                                <th>Créditos</th>
                                <th>Semestre</th>
                                <th>Licenciatura</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asignaturas as $materia): ?>
                            <tr>
                                <td><?= htmlspecialchars($materia['clave_asig']) ?></td>
                                <td><?= htmlspecialchars($materia['nombre_asignatura']) ?></td>
                                <td><?= htmlspecialchars($materia['creditos_asig']) ?></td>
                                <td><?= htmlspecialchars($materia['semestre']) ?></td>
                                <td><?= htmlspecialchars($materia['nombre_lic']) ?></td>
                                <td>
                                    <a href="modificar_materias.php?edit_materia=<?= $materia['clave_asig'] ?>#materias" class="btn btn-sm btn-warning btn-action">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="modificar_materias.php?delete_materia=<?= $materia['clave_asig'] ?>" 
                                       class="btn btn-sm btn-danger btn-action" onclick="confirmarEliminarMateria(event, '<?= $materia['nombre_asignatura'] ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pestaña de Grupos -->
            <div class="tab-pane fade" id="grupos" role="tabpanel">
                <div class="form-container">
                    <h4><?= isset($editing_grupo) ? 'Editar Grupo' : 'Nuevo Grupo' ?></h4>
                    <form method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="action" value="save_grupo">
                        <input type="hidden" name="form_type" value="<?= isset($editing_grupo) ? 'edit' : 'create' ?>">
                        <?php if (isset($editing_grupo)): ?>
                            <input type="hidden" name="num_grupo" value="<?= $editing_grupo['num_grupo'] ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="num_grupo" class="form-label">Número de Grupo</label>
                                <input type="text" class="form-control" id="num_grupo" name="num_grupo" 
                                    value="<?= $editing_grupo['num_grupo'] ?? '' ?>" <?= isset($editing_grupo) ? 'readonly' : '' ?> required>
                                <div class="invalid-feedback">Campo requerido</div>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="cupo_maximo" class="form-label">Cupo Máximo</label>
                                <input type="number" class="form-control" id="cupo_maximo" name="cupo_maximo" 
                                    value="<?= $editing_grupo['cupo_maximo'] ?? '' ?>" min="1" required>
                                <div class="invalid-feedback">Mínimo 1</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="clave_asig" class="form-label">Materia</label>
                                <select class="form-select" id="clave_asig" name="clave_asig" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($asignaturas as $materia): ?>
                                        <option value="<?= $materia['clave_asig'] ?>" 
                                            <?= (isset($editing_grupo) && $editing_grupo['clave_asig'] == $materia['clave_asig']) ? 'selected' : '' ?>>
                                            <?= $materia['nombre_asignatura'] ?> (<?= $materia['clave_asig'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione una materia</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="clave_lic" class="form-label">Licenciatura</label>
                                <select class="form-select" id="clave_lic" name="clave_lic" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($licenciaturas as $lic): ?>
                                        <option value="<?= $lic['clave_lic'] ?>" 
                                            <?= (isset($editing_grupo) && $editing_grupo['clave_lic'] == $lic['clave_lic']) ? 'selected' : '' ?>>
                                            <?= $lic['nombre_lic'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione una licenciatura</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_aula" class="form-label">Aula</label>
                                <select class="form-select" id="id_aula" name="id_aula" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($aulas as $aula): ?>
                                        <option value="<?= $aula['id_aula'] ?>" 
                                            <?= (isset($editing_grupo) && $editing_grupo['id_aula'] == $aula['id_aula']) ? 'selected' : '' ?>>
                                            <?= $aula['nombre_aula'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione un aula</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="clave_prof" class="form-label">Profesor</label>
                                <select class="form-select" id="clave_prof" name="clave_prof" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($profesores as $prof): ?>
                                        <option value="<?= $prof['clave_prof'] ?>" 
                                            <?= (isset($editing_grupo) && $editing_grupo['clave_prof'] == $prof['clave_prof']) ? 'selected' : '' ?>>
                                            <?= $prof['nombre_prof'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Seleccione un profesor</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-end">
                                <?php if (isset($editing_grupo)): ?>
                                    <a href="modificar_materias.php#grupos" class="btn btn-secondary me-2">Cancelar</a>
                                <?php endif; ?>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Grupo</th>
                                <th>Materia</th>
                                <th>Licenciatura</th>
                                <th>Cupo</th>
                                <th>Inscritos</th>
                                <th>Aula</th>
                                <th>Profesor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grupos as $grupo): ?>
                            <tr>
                                <td><?= htmlspecialchars($grupo['num_grupo']) ?></td>
                                <td><?= htmlspecialchars($grupo['nombre_asignatura']) ?></td>
                                <td><?= htmlspecialchars($grupo['nombre_lic']) ?></td>
                                <td><?= htmlspecialchars($grupo['cupo_maximo']) ?></td>
                                <td><?= htmlspecialchars($grupo['inscritos']) ?></td>
                                <td><?= htmlspecialchars($grupo['nombre_aula'] ?? 'Sin asignar') ?></td>
                                <td><?= htmlspecialchars($grupo['nombre_prof'] ?? 'Sin asignar') ?></td>
                                <td>
                                    <a href="modificar_materias.php?edit_grupo=<?= $grupo['num_grupo'] ?>#grupos" class="btn btn-sm btn-warning btn-action">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="modificar_materias.php?delete_grupo=<?= $grupo['num_grupo'] ?>" 
                                       class="btn btn-sm btn-danger btn-action" onclick="confirmarEliminarGrupo(event, '<?= $grupo['num_grupo'] ?>', '<?= $grupo['nombre_asignatura'] ?>')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
        // Validación de formularios
        (function() {
            'use strict';
            
            // Validar formulario de materias
            const formMateria = document.querySelector('#materias .needs-validation');
            if (formMateria) {
                formMateria.addEventListener('submit', function(event) {
                    if (!formMateria.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    formMateria.classList.add('was-validated');
                }, false);
            }
            
            // Validar formulario de grupos
            const formGrupo = document.querySelector('#grupos .needs-validation');
            if (formGrupo) {
                formGrupo.addEventListener('submit', function(event) {
                    if (!formGrupo.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    formGrupo.classList.add('was-validated');
                }, false);
            }
            
            // Mostrar pestaña activa al recargar
            const hash = window.location.hash;
            if (hash === '#grupos') {
                const tab = new bootstrap.Tab(document.querySelector('#grupos-tab'));
                tab.show();
            }
        })();
        
        // Función para confirmar cierre de sesión
        function confirmarcierreSesion() {
            Swal.fire({
                title: '¿Estás seguro de cerrar sesión?',
                text: "Se cerrará la sesión actual.",
                icon: 'warning',
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
        
        // Función para confirmar eliminación de materia
        function confirmarEliminarMateria(event, nombreMateria) {
            event.preventDefault();
            const url = event.currentTarget.getAttribute('href');
            
            Swal.fire({
                title: '¿Eliminar materia?',
                html: `¿Estás seguro de que deseas eliminar la materia <b>${nombreMateria}</b>?<br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
        
        // Función para confirmar eliminación de grupo
        function confirmarEliminarGrupo(event, numGrupo, nombreMateria) {
            event.preventDefault();
            const url = event.currentTarget.getAttribute('href');
            
            Swal.fire({
                title: '¿Eliminar grupo?',
                html: `¿Estás seguro de que deseas eliminar el grupo <b>${numGrupo}</b> de la materia <b>${nombreMateria}</b>?<br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        }
        
        // Mostrar mensaje de éxito al guardar
        <?php if (isset($_GET['success'])): ?>
            Swal.fire({
                title: '¡Éxito!',
                text: 'Los cambios se han guardado correctamente.',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                // Eliminar el parámetro success de la URL
                const url = new URL(window.location.href);
                url.searchParams.delete('success');
                window.history.replaceState({}, document.title, url.toString());
            });
        <?php endif; ?>
        
        // Mostrar mensaje de error si hay problemas
        <?php if (isset($_GET['error'])): ?>
            Swal.fire({
                title: 'Error',
                text: 'Ha ocurrido un error al procesar la solicitud.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            }).then(() => {
                // Eliminar el parámetro error de la URL
                const url = new URL(window.location.href);
                url.searchParams.delete('error');
                window.history.replaceState({}, document.title, url.toString());
            });
        <?php endif; ?>
    </script>
</body>
</html>