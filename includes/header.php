<?php
$flash = obtenerMensaje();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($tituloPagina ?? 'Dulcella') ?> | Dulcella</title>
  <meta name="description" content="Menú digital artesanal Dulcella — postres y bebidas chocolate + rosa.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,500;0,9..144,650;0,9..144,700;1,9..144,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e($cssPath ?? 'css/estilos.css') ?>">
</head>
<body class="publico">
<header class="topbar">
  <a class="brand" href="<?= e($homePath ?? 'index.php') ?>">
    <img src="<?= e($imgPath ?? 'img/logo.jpg') ?>" alt="Dulcella" class="brand-logo">
    <span class="brand-text">
      <strong>Dulcella</strong>
      <em>Dulzura artesanal en cada pedido.</em>
    </span>
  </a>
  <input type="checkbox" id="nav-toggle" class="nav-toggle" hidden>
  <label for="nav-toggle" class="nav-burger" aria-label="Abrir menú"><span></span></label>
  <nav class="nav-principal">
    <a href="<?= e($homePath ?? 'index.php') ?>">Inicio</a>
    <a href="<?= e($catalogoPath ?? 'catalogo.php') ?>">Menú</a>
    <a href="<?= e($pedidoPath ?? 'pedido.php') ?>">Pedir</a>
    <a class="link-admin" href="<?= e($adminPath ?? 'admin/login.php') ?>">Admin</a>
  </nav>
</header>
<?php if ($flash): ?>
  <div class="flash flash-<?= e($flash['tipo']) ?>"><?= e($flash['texto']) ?></div>
<?php endif; ?>
<main class="contenedor">
