<?php
session_start();
require_once __DIR__ . '/includes/funciones.php';

$wa = $_SESSION['wa_redirect'] ?? '';
unset($_SESSION['wa_redirect']);

$tituloPagina = 'Pedido enviado';
require __DIR__ . '/includes/header.php';
?>

<div class="panel-form anim-rise" style="text-align:center;">
  <div class="gracias-icon" aria-hidden="true">✓</div>
  <h1>¡Pedido registrado!</h1>
  <p class="seccion-sub" style="margin-left:auto;margin-right:auto;">
    El equipo de Dulcella ya lo ve en la bandeja. Confirma por WhatsApp si quieres.
  </p>
  <?php if ($wa !== ''): ?>
    <a class="btn btn-wa" href="<?= e($wa) ?>" target="_blank" rel="noopener">Abrir WhatsApp</a>
  <?php endif; ?>
  <p style="margin-top:1.1rem;">
    <a class="btn btn-outline" href="index.php">Volver al inicio</a>
  </p>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
