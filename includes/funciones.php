<?php
/** WhatsApp de contacto Dulcella (Colombia). */
const DULCELLA_WHATSAPP = '573001112233';

function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function adminLogueado(): bool
{
    return isset($_SESSION['admin_id']);
}

function requiereAdmin(): void
{
    if (!adminLogueado()) {
        header('Location: login.php');
        exit;
    }
}

function guardarMensaje(string $texto, string $tipo = 'ok'): void
{
    $_SESSION['flash'] = ['texto' => $texto, 'tipo' => $tipo];
}

function obtenerMensaje(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $msg = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $msg;
}

function formatearPrecio($precio): string
{
    return '$' . number_format((float) $precio, 0, ',', '.');
}

function claseActiva(string $archivo): string
{
    return basename($_SERVER['PHP_SELF']) === $archivo ? 'activo' : '';
}

function enlaceWhatsApp(string $mensaje): string
{
    return 'https://wa.me/' . DULCELLA_WHATSAPP . '?text=' . rawurlencode($mensaje);
}

/** Resuelve foto local (img/...) o URL absoluta. $prefijo = '../' desde admin. */
function urlFoto(?string $foto, string $prefijo = ''): string
{
    $fallback = $prefijo . 'img/logo.jpg';
    if ($foto === null || trim($foto) === '') {
        return $fallback;
    }
    $foto = trim($foto);
    if (preg_match('#^https?://#i', $foto)) {
        return $foto;
    }
    return $prefijo . ltrim($foto, '/');
}

/** Imagen representativa de categoría por nombre. */
function imagenCategoria(string $nombre, string $prefijo = ''): string
{
    $mapa = [
        'postres dietéticos' => 'img/cat-dieteticos.jpg',
        'postres de chocolate' => 'img/cat-chocolate.jpg',
        'postres con helado' => 'img/cat-helado.jpg',
        'postres con frutas' => 'img/cat-frutas.jpg',
        'bebidas' => 'img/cat-bebidas.jpg',
    ];
    $clave = mb_strtolower(trim($nombre));
    $ruta = $mapa[$clave] ?? 'img/hero-postre.jpg';
    return $prefijo . $ruta;
}
