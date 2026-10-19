# Dulcella

Menú digital artesanal de postres y bebidas (chocolate + rosa) + carrito con varios productos por pedido + panel admin. Confirmación por WhatsApp al número oficial de la asesora.

## Instalación

1. Clona este repositorio dentro de la carpeta `htdocs` de XAMPP (queda la carpeta `dulcella`).
2. Enciende Apache y MySQL en XAMPP.
3. Importa la base de datos (ver abajo).

## URL local

- Público: http://localhost/dulcella/
- Menú: http://localhost/dulcella/menu.php (alias de `catalogo.php`)
- Carrito: http://localhost/dulcella/carrito.php
- Pedido: http://localhost/dulcella/pedido.php
- Admin: http://localhost/dulcella/admin/login.php

## Credenciales demo

- Correo: `admin@demo.com`
- Contraseña: `Admin123!`

## Importar base de datos (XAMPP)

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root < /Applications/XAMPP/xamppfiles/htdocs/dulcella/bd/dulcella.sql
```

O en phpMyAdmin: Importar → `bd/dulcella.sql`.

## Flujo UX

1. **Inicio** → elige categoría
2. **Menú** → «Agregar al carrito» en los postres que quieras (o «Pedir ya» para ir directo al carrito)
3. **Carrito** → ajusta cantidades, agrega otros postres o quita ítems; ves el total
4. **Pedido** → confirma el resumen (varios productos) + datos de entrega → botón de WhatsApp a la asesora
5. **Admin** → bandeja de pedidos con el detalle por ítem y el total de cada orden

## Stack

PHP + MySQL (`mysqli`) + CSS propio + sesiones PHP. Fotos locales en `img/` por categoría.
