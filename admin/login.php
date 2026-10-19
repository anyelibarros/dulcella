<?php
session_start();
require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/funciones.php';

if (adminLogueado()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if ($correo === '' || $clave === '') {
        $error = 'Correo y contraseña son obligatorios.';
    } else {
        $stmt = $mysqli->prepare('SELECT id_admin, nombre, correo, password_hash FROM admin WHERE correo = ? LIMIT 1');
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if (!$admin || !password_verify($clave, $admin['password_hash'])) {
            $error = 'Correo o contraseña incorrectos.';
        } else {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id_admin'];
            $_SESSION['admin_nombre'] = $admin['nombre'];
            guardarMensaje('Bienvenida/o, ' . $admin['nombre']);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Dulcella Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,650;9..144,700&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <img src="../img/logo.jpg" alt="Dulcella">
    <h1>Panel de Dulcella</h1>
    <p class="sub">Acceso solo para administradores</p>
    <?php if ($error): ?>
      <div class="error-box"><?= e($error) ?></div>
    <?php endif; ?>
    <form class="formulario" method="post">
      <label for="correo">Correo</label>
      <input id="correo" type="email" name="correo" required value="<?= e($_POST['correo'] ?? '') ?>">
      <label for="clave">Contraseña</label>
      <input id="clave" type="password" name="clave" required minlength="6">
      <button class="btn btn-choco" type="submit">Ingresar</button>
    </form>
    <p class="hint">Demo: <strong>admin@demo.com</strong> / <strong>Admin123!</strong></p>
    <p class="hint"><a href="../index.php">← Volver al sitio</a></p>
  </div>
</div>
</body>
</html>
