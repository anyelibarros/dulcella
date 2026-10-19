<?php
$flash = obtenerMensaje();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($tituloPagina ?? 'Panel') ?> | Dulcella Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,650;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body class="admin">
<header class="admin-top">
  <div class="admin-brand">
    <img src="../img/logo.jpg" alt="Dulcella">
    <span>Panel Dulcella</span>
  </div>
  <input type="checkbox" id="admin-nav-toggle" class="nav-toggle" hidden>
  <label for="admin-nav-toggle" class="nav-burger nav-burger-admin" aria-label="Abrir menú admin"><span></span></label>
  <nav class="admin-nav">
    <a class="<?= claseActiva('index.php') ?>" href="index.php">Panel</a>
    <a class="<?= claseActiva('categorias.php') ?>" href="categorias.php">Categorías</a>
    <a class="<?= claseActiva('productos.php') ?> <?= claseActiva('producto_form.php') ?>" href="productos.php">Productos</a>
    <a class="<?= claseActiva('pedidos.php') ?>" href="pedidos.php">Pedidos</a>
    <a class="salir" href="logout.php">Cerrar sesión</a>
  </nav>
</header>
<?php if ($flash): ?>
  <div class="flash flash-<?= e($flash['tipo']) ?>"><?= e($flash['texto']) ?></div>
<?php endif; ?>
<main class="contenedor admin-main">
