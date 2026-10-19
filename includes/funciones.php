<?php
/** WhatsApp oficial de la asesora Dulcella (Colombia, +57). */
const DULCELLA_WHATSAPP = '573017293044';

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

/* ——— Carrito en sesión: [id_producto => cantidad] ——— */

function carritoItems(): array
{
    $carrito = $_SESSION['carrito'] ?? [];
    if (!is_array($carrito)) {
        return [];
    }
    $limpio = [];
    foreach ($carrito as $id => $cantidad) {
        $id = (int) $id;
        $cantidad = (int) $cantidad;
        if ($id > 0 && $cantidad > 0) {
            $limpio[$id] = min($cantidad, 99);
        }
    }
    return $limpio;
}

function carritoCantidadTotal(): int
{
    return array_sum(carritoItems());
}

function carritoAgregar(int $idProducto, int $cantidad = 1): void
{
    if ($idProducto <= 0) {
        return;
    }
    $carrito = carritoItems();
    $carrito[$idProducto] = min(($carrito[$idProducto] ?? 0) + max(1, $cantidad), 99);
    $_SESSION['carrito'] = $carrito;
}

function carritoFijarCantidad(int $idProducto, int $cantidad): void
{
    $carrito = carritoItems();
    if ($cantidad <= 0) {
        unset($carrito[$idProducto]);
    } else {
        $carrito[$idProducto] = min($cantidad, 99);
    }
    $_SESSION['carrito'] = $carrito;
}

function carritoVaciar(): void
{
    unset($_SESSION['carrito']);
}

/**
 * Productos del carrito con datos actuales de la BD.
 * Quita de la sesión los que ya no estén disponibles.
 * Devuelve ['items' => [...cada uno con cantidad y subtotal...], 'total' => float].
 */
function carritoDetalle(mysqli $mysqli): array
{
    $carrito = carritoItems();
    if (!$carrito) {
        return ['items' => [], 'total' => 0.0];
    }

    $ids = array_keys($carrito);
    $marcas = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $mysqli->prepare(
        "SELECT id_producto, nombre, precio, foto_url FROM producto
         WHERE disponible = 1 AND id_producto IN ($marcas)"
    );
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $encontrados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $items = [];
    $total = 0.0;
    $vigentes = [];
    foreach ($encontrados as $p) {
        $id = (int) $p['id_producto'];
        $cantidad = $carrito[$id];
        $subtotal = (float) $p['precio'] * $cantidad;
        $p['cantidad'] = $cantidad;
        $p['subtotal'] = $subtotal;
        $items[] = $p;
        $total += $subtotal;
        $vigentes[$id] = $cantidad;
    }
    $_SESSION['carrito'] = $vigentes;

    return ['items' => $items, 'total' => $total];
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
