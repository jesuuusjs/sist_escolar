<?php
session_start();
header('Cache-Control: no-cache, must-revalidate');
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: index.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FESC UNAM - Alumno</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" href="../images/logo-FESC.ico" type="image/webp">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="../css/alumnos.css">
    <link rel="stylesheet" href="../css/actualizardatos.css">
    <link rel="stylesheet" href="../css/inscripcion.css">
    <link rel="stylesheet" href="../css/saturacion.css">
    <link rel="stylesheet" href="../css/inscrito.css">
    
</head>
<body>
    <!-- Botón móvil -->
    <button class="mobile-menu-btn" id="mobileMenuBtn" style="display: none;">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="sidebar-header">
            <img src="../images/logo.webp" alt="FESC UNAM">
            <h4>Panel del Alumno</h4>
        </div>
        
        <ul class="sidebar-menu">
            <li>
                <a href="#" id="datos-link">
                    <i class="fas fa-user-edit"></i> Actualizar Datos
                </a>
            </li>
            <li>
                <a href="#" id="inscripcion-link">
                    <i class="fas fa-clipboard-list"></i> Inscribir Materias
                </a>
            </li>
            <li>
                <a href="#" id="dosificacion-link">
                    <i class="fas fa-calendar-alt"></i> Consultar Dosificación
                </a>
            </li>
            <li>
                <a href="#" id="saturacion-link">
                    <i class="fas fa-chart-bar"></i> Consultar Saturación
                </a>
            </li>
        </ul>
        
        <div class="user-info">
            <img src="../images/user-avatar.png" alt="Usuario">
            <div>
                <div><?php echo $_SESSION['numcuenta'] ?? 'Alumno'; ?></div>
                <small>Estudiante</small>
            </div>
            <button class="btn btn-sm btn-danger ms-auto" onclick="confirmarCierreSesion()">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </div>

    <!-- Contenido principal -->
    <div id="content">

    <!-- Sección de Actualizar Datos -->
    <div id="datos-section" class="content-section">
    <div class="unam-header" style="display: flex; align-items: center; background-color: #002147; padding: 10px;">
    <div class="unam-logo">
        <img src="../images/logo-unam-blanco.png" alt="UNAM" class="escudo-unam">
    </div>
    <div class="unam-text" style="color: white; margin-left: 15px;">
        <h1 style="color: white; margin: 0;">UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO</h1>
        <h2 style="color: white; margin: 0;">Facultad de Estudios Superiores Cuautitlán</h2>
    </div>
</div>

    <div class="section-header">
    <h3><i class="fas fa-user-edit" style="color:gold;"></i> Actualización de Datos del Alumno</h3>
    <p>Actualiza tus datos personales para mantener tu información al día.</p>

    </div>
    
    <div class="section-content">
        <?php
        require_once 'conexion.php'; // Archivo con la conexión a la DB
        // Verificar sesión
        $numCuentaAlumno = $_SESSION['numcuenta'] ?? null;
        if (!$numCuentaAlumno) {
            echo '<script>
            Swal.fire({
                title: "Error de sesión",
                text: "Debes iniciar sesión primero",
                icon: "error"
            }).then(() => {
                window.location.href = "index.html";
            });
            </script>';
            exit;
        }

        // Procesar formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = htmlspecialchars($_POST['nombre'] ?? '');
            $genero = htmlspecialchars($_POST['genero'] ?? '');
            $fecha_nac = htmlspecialchars($_POST['fecha_nacimiento'] ?? '');
            $domicilio = htmlspecialchars($_POST['domicilio'] ?? '');
            $telefono = htmlspecialchars($_POST['telefono'] ?? '');
            $correo = htmlspecialchars($_POST['correo'] ?? '');
            
            // Validación
            $errores = [];
            if (empty($nombre)) $errores[] = "El nombre es obligatorio";
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errores[] = "Correo electrónico inválido";
            if (!preg_match('/^[0-9]{10}$/', $telefono)) $errores[] = "Teléfono debe tener 10 dígitos";

            if (empty($errores)) {
                $stmt = $conn->prepare("UPDATE Alumno SET 
                    nombre_alumno=?, genero=?, fecha_nacimiento=?, 
                    domicilio=?, telefono=?, correo=? 
                    WHERE numCuenta_alumno=?");
                
                if ($stmt->bind_param('sssssss', $nombre, $genero, $fecha_nac, $domicilio, $telefono, $correo, $numCuentaAlumno) && $stmt->execute()) {
                    $_SESSION['nombre_alumno'] = $nombre;
                    echo '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                        title: "¡Éxito!",
                        text: "Datos actualizados correctamente",
                        icon: "success",
                        confirmButtonColor: "#28a745"
                        }).then(() => {
                    window.location.href = "'.$_SERVER['PHP_SELF'].'"; // Recarga limpia
                        });
                    });
                </script>';
                } else {
                    echo '<script>
                    Swal.fire({
                        title: "Error",
                        text: "Error al actualizar: '.addslashes($conn->error).'",
                        icon: "error",
                        confirmButtonColor: "#dc3545"
                    });
                    </script>';
                }
                $stmt->close();
            } else {
                echo '<script>
                Swal.fire({
                    title: "Error de validación",
                    html: "'.implode("<br>", $errores).'",
                    icon: "error",
                    confirmButtonColor: "#dc3545"
                });
                </script>';
            }
        }

        // Obtener datos actuales
        $stmt = $conn->prepare("SELECT * FROM Alumno WHERE numCuenta_alumno=?");
        $stmt->bind_param('s', $numCuentaAlumno);
        $stmt->execute();
        $alumno = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        ?>

        <div class="formulario-datos">
            <form id="form-datos" method="POST">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre"><i class="fas fa-user"></i> Nombre completo</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" 
                            value="<?= htmlspecialchars($alumno['nombre_alumno'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="genero"><i class="fas fa-venus-mars"></i> Género</label>
                        <select class="form-control" id="genero" name="genero" required>
                            <option value="Masculino" <?= ($alumno['genero'] ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Femenino" <?= ($alumno['genero'] ?? '') == 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                            <option value="Otro" <?= empty($alumno['genero']) || !in_array($alumno['genero'], ['Masculino', 'Femenino']) ? 'selected' : '' ?>>Otro</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fecha_nacimiento"><i class="fas fa-birthday-cake"></i> Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                            value="<?= htmlspecialchars($alumno['fecha_nacimiento'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono"
                            value="<?= htmlspecialchars($alumno['telefono'] ?? '') ?>" 
                            pattern="[0-9]{10}" title="10 dígitos numéricos" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="domicilio"><i class="fas fa-home"></i> Domicilio</label>
                    <input type="text" class="form-control" id="domicilio" name="domicilio"
                        value="<?= htmlspecialchars($alumno['domicilio'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="correo"><i class="fas fa-envelope"></i> Correo electrónico</label>
                    <input type="email" class="form-control" id="correo" name="correo"
                        value="<?= htmlspecialchars($alumno['correo'] ?? '') ?>" required>
                </div>
                
                <div class="actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                    
                </div>
            </form>
        </div>
    </div>
    <script>
        const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

// Validación del formulario
document.getElementById('form-datos').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!this.checkValidity()) {
        let errorFields = Array.from(this.querySelectorAll(':invalid'));
        let errorMessage = '<div style="text-align:left;"><b>Corrige estos campos:</b><ul style="margin-top:10px;">';
        
        errorFields.forEach(field => {
            errorMessage += `<li>${field.labels[0].textContent}</li>`;
            field.style.borderColor = '#dc3545';
        });
        
        errorMessage += '</ul></div>';
        
        Swal.fire({
            title: 'Formulario incompleto',
            html: errorMessage,
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
        
        errorFields[0].focus();
        return;
    }
    
    Swal.fire({
        title: '¿Guardar cambios?',
        text: 'Se actualizarán tus datos personales',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        }
    });
});
// Mostrar ayuda al enfocar campos
document.querySelectorAll('.form-control').forEach(field => {
    field.addEventListener('focus', function() {
        const label = this.labels[0].textContent;
        Toast.fire({
            icon: 'info',
            title: `Editando: ${label}`
        });
    });
    
    // Remover borde rojo al corregir
    field.addEventListener('input', function() {
        if (this.checkValidity()) {
            this.style.borderColor = '#ced4da';
        }
    });
});
    </script>
</div>
        <!-- Sección de Inscripción de Materias -->
        <div id="inscripcion-section" class="content-section">
        <?php
            require_once 'conexion.php'; // Archivo con la conexión a la DB
            // Obtener el número de cuenta del alumno desde la sesión
            if (!isset($_SESSION['numcuenta'])) {
            die('<div class="alert alert-danger">No se encontró la cuenta del alumno en la sesión.</div>');
        }
            $numCuenta = $_SESSION['numcuenta'];
            
            $queryDosificacion = "SELECT fecha_atcion, hora_atcion FROM Dosificacion WHERE numCuenta_alumno = ?";
            $stmt = $conn->prepare($queryDosificacion);
            $stmt->bind_param("s", $numCuenta);
            $stmt->execute();
            $result = $stmt->get_result();
            $fecha_atencion = $hora_atencion = "";
            if ($row = $result->fetch_assoc()) {
            $fecha_atencion = $row['fecha_atcion'];
            $hora_atencion = $row['hora_atcion'];
            } else {
                echo '<div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> No tienes una dosificación asignada actualmente.
                </div>';
            }
$stmt->close();

            ?>
            
        <div class="unam-header" style="display: flex; align-items: center; background-color: #002147; padding: 10px;">
    <div class="unam-logo">
        <img src="../images/logo-unam-blanco.png" alt="UNAM" class="escudo-unam">
    </div>
    <div class="unam-text" style="color: white; margin-left: 15px;">
        <h1 style="color: white; margin: 0;">UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO</h1>
        <h2 style="color: white; margin: 0;">Facultad de Estudios Superiores Cuautitlán</h2>
    </div>
</div>

            <div class="section-header">
        <h2><i class="fas fa-clipboard-list"></i> Inscripción de Materias</h2>
        <div class="alert alert-info">
            <i class="fas fa-user-graduate"></i>
            <p><strong>Fecha de atención:</strong> <?= htmlspecialchars($fecha_atencion) ?></p>
            <p><strong>Hora de atención:</strong> <?= htmlspecialchars($hora_atencion) ?></p>
        </div>
            </div>
        <div class="section-content">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Selecciona el grupo al que deseas inscribirte.
            </div>
        </div>
<?php
require_once 'conexion.php';

if (!isset($_SESSION['numcuenta']) || $_SESSION['rol'] !== 'alumno') {
    header("Location: index.html");
    exit;
}

$mensaje = "";
$numCuenta = $_SESSION['numcuenta'];

$asignaturas = $conn->query("SELECT clave_asig, nombre_asignatura FROM Asignatura");
$grupos = $conn->query("
    SELECT g.num_grupo, a.nombre_asignatura, g.clave_asig 
    FROM Grupo g
    INNER JOIN Asignatura a ON g.clave_asig = a.clave_asig
");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clave_asig = $_POST["clave_asig"];
    $grupo = $_POST["grupo"];

    // Verificar si ya está inscrito
    $stmt_verifica = $conn->prepare("SELECT * FROM alumno_asignaturas WHERE numCuenta_alumno = ? AND clave_asig = ? AND grupo = ?");
    $stmt_verifica->bind_param("sss", $numCuenta, $clave_asig, $grupo);
    $stmt_verifica->execute();
    $resultado_verifica = $stmt_verifica->get_result();

    if ($resultado_verifica->num_rows > 0) {
        $mensaje = "⚠️ Ya estás inscrito en esa materia y grupo.";
    } else {
        // Verificar cupo
        $stmt = $conn->prepare("SELECT cupo_maximo, inscritos FROM Grupo WHERE num_grupo = ? AND clave_asig = ?");
        $stmt->bind_param("ss", $grupo, $clave_asig);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();

            if ($fila["inscritos"] < $fila["cupo_maximo"]) {
                // Inscribir
                $stmt_insert = $conn->prepare("INSERT INTO alumno_asignaturas (numCuenta_alumno, clave_asig, grupo) VALUES (?, ?, ?)");
                $stmt_insert->bind_param("sss", $numCuenta, $clave_asig, $grupo);

                if ($stmt_insert->execute()) {
                    $stmt_update = $conn->prepare("UPDATE Grupo SET inscritos = inscritos + 1 WHERE num_grupo = ? AND clave_asig = ?");
                    $stmt_update->bind_param("ss", $grupo, $clave_asig);
                    $stmt_update->execute();
                    $mensaje = "✅ Inscripción exitosa.";
                } else {
                    $mensaje = "❌ Error al inscribir: " . $stmt_insert->error;
                }
            } else {
                $mensaje = "❌ No hay cupo disponible en este grupo.";
            }
        } else {
            $mensaje = "❌ Grupo no encontrado.";
        }
    }
}
?>

<!-- FORMULARIO -->
<div class="container mt-4">
    <form id="formInscripcion" method="POST">
        <div class="mb-3">
            <label for="clave_asig" class="form-label">Asignatura:</label>
            <select name="clave_asig" id="clave_asig" class="form-select" required>
                <option value="">Seleccione una asignatura</option>
                <?php while ($row = $asignaturas->fetch_assoc()): ?>
                    <option value="<?= $row['clave_asig'] ?>"><?= $row['nombre_asignatura'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="grupo" class="form-label">Grupo:</label>
            <select name="grupo" id="grupo" class="form-select" required>
                <option value="">Seleccione un grupo</option>
                <?php while ($row = $grupos->fetch_assoc()): ?>
                    <option value="<?= $row['num_grupo'] ?>"><?= $row['num_grupo'] . " - " . $row['nombre_asignatura'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Inscribirse
            </button>
        </div>
    </form>
</div>

<!-- MENSAJE CON SWEETALERT PERSONALIZADO -->
<?php if ($mensaje): ?>
    <script>
        const mensaje = <?= json_encode(strip_tags($mensaje)) ?>;
        const tipo = <?= str_contains($mensaje, '✅') ? "'success'" : (str_contains($mensaje, '⚠️') ? "'warning'" : "'error'") ?>;

        Swal.fire({
            icon: tipo,
            title: mensaje,
            showConfirmButton: tipo !== 'success',  // Mostrar botón solo si NO es éxito
            timer: tipo === 'success' ? 3000 : undefined,
            timerProgressBar: tipo === 'success'
        });
    </script>
<?php endif; ?>


<!-- CONFIRMACIÓN ANTES DE ENVIAR -->
<script>
document.getElementById('formInscripcion').addEventListener('submit', function (e) {
    e.preventDefault(); // Evita envío inmediato

    Swal.fire({
        title: '¿Confirmar inscripción?',
        text: "¿Estás seguro de inscribirte en esta materia?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, inscribirme',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit(); // Enviar formulario si confirma
        }
    });
});
</script>


<!-- Botón con estilo -->
<button onclick="toggleMaterias()" type="button" style="
    margin-top: 20px;
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s;">
    Mostrar/Ocultar mis materias inscritas
</button>

<!-- Contenedor oculto con estilo de tarjeta -->
<div id="materiasInscritas" style="
    display: none;
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #ccc;
    border-radius: 12px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    max-width: 600px;
">
    <h3 style="margin-top: 0; color: #333;">Materias inscritas:</h3>
    <ul style="list-style-type: none; padding-left: 0;">
        <?php
        $numCuenta = $_SESSION['numcuenta'];
        $stmt_materias = $conn->prepare("
            SELECT a.nombre_asignatura, aa.grupo
            FROM alumno_asignaturas aa
            INNER JOIN Asignatura a ON aa.clave_asig = a.clave_asig
            WHERE aa.numCuenta_alumno = ?
        ");
        $stmt_materias->bind_param("s", $numCuenta);
        $stmt_materias->execute();
        $resultado_materias = $stmt_materias->get_result();

        if ($resultado_materias->num_rows > 0) {
            while ($fila = $resultado_materias->fetch_assoc()) {
                echo "<li style='margin-bottom: 8px; padding: 8px; background-color: #e9ecef; border-radius: 6px;'>
                        <strong>{$fila['nombre_asignatura']}</strong> - Grupo: {$fila['grupo']}
                      </li>";
            }
        } else {
            echo "<li style='color: #777;'>No tienes materias inscritas aún.</li>";
        }
        ?>
    </ul>
</div>

<!-- Script para mostrar/ocultar -->
<script>
function toggleMaterias() {
    const div = document.getElementById("materiasInscritas");
    div.style.display = (div.style.display === "none") ? "block" : "none";
}
</script>


        
        </div>

        <!-- Sección de Consultar Dosificación -->
        <div id="dosificacion-section" class="content-section">
        <div class="unam-header" style="display: flex; align-items: center; background-color: #002147; padding: 10px;">
    <div class="unam-logo">
        <img src="../images/logo-unam-blanco.png" alt="UNAM" class="escudo-unam">
    </div>
    <div class="unam-text" style="color: white; margin-left: 15px;">
        <h1 style="color: white; margin: 0;">UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO</h1>
        <h2 style="color: white; margin: 0;">Facultad de Estudios Superiores Cuautitlán</h2>
    </div>
</div>

    <div class="section-header">
        <h2><i class="fas fa-calendar-alt"></i> Mi Dosificación</h2>
    </div>
    
    <div class="section-content">
        <?php
        require_once 'conexion.php'; // Archivo con la conexión a la DB
        // Obtener el número de cuenta del alumno desde la sesión
        $numCuentaAlumno = $_SESSION['numcuenta']; 
        // Consulta para obtener la dosificación del alumno
        $sql = "SELECT * FROM dosificacion WHERE numCuenta_alumno = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $numCuentaAlumno);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $dosificacion = $result->fetch_assoc();
            ?>
            
            <div class="dosificacion-info">
                <div class="info-card">
                    <h3><i class="fas fa-info-circle"></i> Información de tu Dosificación</h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">ID Dosificación:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['id_dosificacion']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Clave Licenciatura:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['clave_lic']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Número de Cuenta:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['numCuenta_alumno']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Nombre:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['nombre_alumno']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Turno:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['turno']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Fecha de Atención:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['fecha_atcion']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Hora de Atención:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['hora_atcion']) ?></span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Número de Atención:</span>
                            <span class="info-value"><?= htmlspecialchars($dosificacion['num_atcion']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="actions">
                
                </div>
            </div>
            
            <?php
        } else {
            echo '<div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> No tienes una dosificación asignada actualmente.
                </div>';
        }
        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>
        <!-- Sección de Consultar Saturación -->
        <div id="saturacion-section" class="content-section">
        <div class="unam-header" style="display: flex; align-items: center; background-color: #002147; padding: 10px;">
    <div class="unam-logo">
        <img src="../images/logo-unam-blanco.png" alt="UNAM" class="escudo-unam">
    </div>
    <div class="unam-text" style="color: white; margin-left: 15px;">
        <h1 style="color: white; margin: 0;">UNIVERSIDAD NACIONAL AUTÓNOMA DE MÉXICO</h1>
        <h2 style="color: white; margin: 0;">Facultad de Estudios Superiores Cuautitlán</h2>
    </div>
</div>

    <div class="section-header">
        <h2><i class="fas fa-chart-bar"></i> Consultar Saturación de Grupos</h2>
    </div>
    
    <div class="container">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Esta tabla muestra el porcentaje de ocupación de cada grupo.
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Grupo</th>
                        <th>Materia</th>
                        <th>Licenciatura</th>
                        <th>Semestre</th>
                        <th>Cupo</th>
                        <th>Inscritos</th>
                        <th>Saturación</th>
                        <th>Barra de Progreso</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require 'conexion.php'; // Incluir la conexión a la base de datos
                    
                    $query = "SELECT 
                                g.num_grupo,
                                a.nombre_asignatura,
                                l.nombre_lic,
                                a.semestre,
                                g.cupo_maximo,
                                g.inscritos,
                                ROUND((g.inscritos / g.cupo_maximo) * 100) as porcentaje_ocupacion
                            FROM Grupo g
                            JOIN Asignatura a ON g.clave_asig = a.clave_asig
                            JOIN Licenciatura l ON g.clave_lic = l.clave_lic
                            ORDER BY a.semestre, a.nombre_asignatura, g.num_grupo";
                    
                    $result = $conn->query($query);
                    
                    if ($result && $result->num_rows > 0) {
                        while($grupo = $result->fetch_assoc()) {
                            $porcentaje = $grupo['porcentaje_ocupacion'];
                            $clase_progreso = ($porcentaje >= 90) ? 'bg-danger' : 
                                            (($porcentaje >= 70) ? 'bg-warning' : 'bg-success');
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($grupo['num_grupo']) ?></td>
                        <td><?= htmlspecialchars($grupo['nombre_asignatura']) ?></td>
                        <td><?= htmlspecialchars($grupo['nombre_lic']) ?></td>
                        <td><?= htmlspecialchars($grupo['semestre']) ?>°</td>
                        <td><?= htmlspecialchars($grupo['cupo_maximo']) ?></td>
                        <td><?= htmlspecialchars($grupo['inscritos']) ?></td>
                        <td><?= $porcentaje ?>%</td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar <?= $clase_progreso ?>" 
                                    role="progressbar" 
                                    style="width: <?= $porcentaje ?>%;" 
                                    aria-valuenow="<?= $porcentaje ?>" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <?= $porcentaje ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="8" class="text-center">No se encontraron grupos registrados</td></tr>';
                    }
                    // Cerrar conexión
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <div class="alert alert-secondary">
                <strong>Leyenda:</strong>
                <span class="badge bg-success">Disponible (menos del 70%)</span>
                <span class="badge bg-warning">Llenándose (70-89%)</span>
                <span class="badge bg-danger">Casi lleno (90% o más)</span>
            </div>
        </div>
    </div>
</div>
    </div>
    <script src="../js/alumnos.js"></script>
</body>
</html>