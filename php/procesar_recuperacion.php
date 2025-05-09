<?php
// Incluir conexión a la base de datos
require 'conexion.php';

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $numcuenta = $_POST['accountNumber'] ?? '';
    $nuevaPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Validaciones básicas
    $errores = [];
    
    if (strlen($numcuenta) !== 9 || !ctype_digit($numcuenta)) {
        $errores[] = "El número de cuenta debe tener exactamente 9 dígitos";
    }
    
    if (strlen($nuevaPassword) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres";
    }
    
    if ($nuevaPassword !== $confirmPassword) {
        $errores[] = "Las contraseñas no coinciden";
    }
    
    // Si hay errores, mostrarlos
    if (!empty($errores)) {
        echo "<script>
            Swal.fire({
                title: 'Error',
                html: '".implode('<br>', $errores)."',
                icon: 'error',
                confirmButtonColor: '#0a3d6d',
                confirmButtonText: 'Entendido'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }
    
    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT numcuenta FROM usuarios WHERE numcuenta = ?");
    $stmt->bind_param("s", $numcuenta);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'El número de cuenta no está registrado',
                icon: 'error',
                confirmButtonColor: '#0a3d6d',
                confirmButtonText: 'Entendido'
            }).then(() => {
                window.history.back();
            });
        </script>";
        exit;
    }
    
    // Hashear la nueva contraseña
    $hashedPassword = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    
    // Actualizar la contraseña en la base de datos
    $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE numcuenta = ?");
    $stmt->bind_param("ss", $hashedPassword, $numcuenta);
    
    if ($stmt->execute()) {
        // Éxito - mostrar mensaje y redirigir
        echo "<!DOCTYPE html>
        
        <html>
        <head>
            <title>Procesando...</title>
            <link rel='icon' href='../images/logo-FESC.ico' type='image/x-icon'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: '¡Contraseña actualizada!',
                    text: 'Tu contraseña ha sido restablecida correctamente',
                    icon: 'success',
                    confirmButtonColor: '#d4af37',
                    confirmButtonText: 'Iniciar sesión',
                    allowOutsideClick: false
                }).then(() => {
                    window.location.href = 'index.html';
                });
            </script>
        </body>
        </html>";
        exit;
    } else {
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Error</title>
            <link rel='icon' href='../images/logo-FESC.ico' type='image/x-icon'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al actualizar la contraseña',
                    icon: 'error',
                    confirmButtonColor: '#0a3d6d',
                    confirmButtonText: 'Entendido'
                }).then(() => {
                    window.history.back();
                });
            </script>
        </body>
        </html>";
        exit;
    }
} else {
    // Si no es POST, redirigir
    header("Location: recuperar_contraseña.html");
    exit;
}
?>