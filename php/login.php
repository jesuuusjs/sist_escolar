<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

$numcuenta = $_POST['numcuenta'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE numcuenta = ?");
$stmt->bind_param("s", $numcuenta);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    if (password_verify($password, $usuario['password'])) {
        $_SESSION['numcuenta'] = $usuario['numcuenta'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['nombre'] = $usuario['nombre'] ?? '';
        
        // Respuesta JSON para éxito
        echo json_encode([
            'status' => 'success',
            'message' => '¡Inicio de sesión exitoso!',
            'nombre' => $usuario['nombre'] ?? '',
            'redirect' => ($usuario['rol'] === 'administrador' ? 'admin.php' : 'alumno.php')
        ]);
        exit;
    } else {
        // Respuesta JSON para contraseña incorrecta
        echo json_encode([
            'status' => 'error',
            'message' => 'Contraseña incorrecta',
            'detail' => 'Por favor verifica tu contraseña e intenta nuevamente'
        ]);
        exit;
    }
} else {
    // Respuesta JSON para usuario no encontrado
    echo json_encode([
        'status' => 'error',
        'message' => 'Usuario no encontrado',
        'detail' => 'El número de cuenta no está registrado en el sistema'
    ]);
    exit;
}
?>