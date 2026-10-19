<?php
session_start();
require_once __DIR__ . '/includes/conexion.php';
require_once __DIR__ . '/includes/funciones.php';

$tituloPagina = 'Inicio';
$categorias = $mysqli->query(
    'SELECT c.id_categoria, c.nombre, COUNT(p.id_producto) AS total
     FROM categoria c
     LEFT JOIN producto p ON p.id_categoria = c.id_categoria AND p.disponible = 1
     GROUP BY c.id_categoria, c.nombre
     ORDER BY c.id_categoria'
)->fetch_all(MYSQLI_ASSOC);

require __DIR__ . '/includes/header.php';
?>

<section class="hero" style="--hero-img: url('img/hero-postre.jpg')">
  <div class="hero-bg" aria-hidden="true"></div>
  <div class="hero-glow" aria-hidden="true"></div>
  <div class="hero-inner anim-rise">
    <img class="brand-mark" src="img/logo.jpg" alt="Logo Dulcella">
    <h1 class="hero-brand">Dulcella</h1>
    <p class="slogan">"Dulzura artesanal en cada pedido."</p>
    <p class="lead">Postres y bebidas en cacao y rosa. Elige categoría, elige un dulce, pide.</p>
    <div class="hero-cta">
      <a class="btn btn-rosa" href="#categorias">Ver categorías</a>
      <a class="btn btn-ghost" href="catalogo.php">Menú completo</a>
    </div>
  </div>
</section>

<section id="categorias" class="anim-rise-2">
  <h2 class="seccion-titulo">Elige tu antojo</h2>
  <p class="seccion-sub">Categoría → producto → un pedido. Así de simple.</p>

  <?php if (!$categorias): ?>
    <div class="vacio">Aún no hay categorías. El administrador debe registrarlas.</div>
  <?php else: ?>
    <div class="grid-cats">
      <?php foreach ($categorias as $i => $cat): ?>
        <a class="cat-card"
           href="catalogo.php?categoria=<?= (int) $cat['id_categoria'] ?>">
          <img src="<?= e(imagenCategoria($cat['nombre'])) ?>" alt="<?= e($cat['nombre']) ?>" loading="lazy">
          <div class="cat-body">
            <strong><?= e($cat['nombre']) ?></strong>
            <span><?= (int) $cat['total'] ?> disponible<?= (int) $cat['total'] === 1 ? '' : 's' ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
