# Dulcella

Menú digital artesanal de postres y bebidas (chocolate + rosa) + pedido de un producto + panel admin.

## Instalación

1. Clona este repositorio dentro de la carpeta `htdocs` de XAMPP (queda la carpeta `dulcella`).
2. Enciende Apache y MySQL en XAMPP.
3. Importa la base de datos (ver abajo).

## URL local

- Público: http://localhost/dulcella/
- Menú: http://localhost/dulcella/menu.php (alias de `catalogo.php`)
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
2. **Menú** → elige producto  
3. **Pedido** → un solo ítem + datos de entrega  
4. **Admin** → bandeja de pedidos

## Stack

PHP + MySQL (`mysqli`) + CSS propio + sesiones PHP. Fotos locales en `img/` por categoría.
