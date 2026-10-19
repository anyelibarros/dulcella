CREATE DATABASE IF NOT EXISTS dulcella
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE dulcella;

DROP TABLE IF EXISTS pedido_detalle;
DROP TABLE IF EXISTS pedido;
DROP TABLE IF EXISTS producto;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS admin;

CREATE TABLE admin (
  id_admin INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE categoria (
  id_categoria INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE producto (
  id_producto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  descripcion TEXT NULL,
  disponible TINYINT(1) NOT NULL DEFAULT 1,
  foto_url VARCHAR(255) NULL,
  id_categoria INT UNSIGNED NOT NULL,
  FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)
) ENGINE=InnoDB;

CREATE TABLE pedido (
  id_pedido INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre_cliente VARCHAR(100) NOT NULL,
  telefono_cliente VARCHAR(20) NOT NULL,
  direccion_entrega VARCHAR(255) NOT NULL,
  fecha DATE NOT NULL,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB;

CREATE TABLE pedido_detalle (
  id_detalle INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pedido INT UNSIGNED NOT NULL,
  id_producto INT UNSIGNED NOT NULL,
  cantidad SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido) ON DELETE CASCADE,
  FOREIGN KEY (id_producto) REFERENCES producto(id_producto),
  UNIQUE KEY uk_pedido_producto (id_pedido, id_producto)
) ENGINE=InnoDB;

-- Demo: admin@demo.com / Admin123!
INSERT INTO admin (nombre, correo, password_hash) VALUES
('Admin Dulcella', 'admin@demo.com', '$2y$10$jQfdbq6bqKffND7UnG1/kOj99B.mFio1VrmzeHWun.9MpyQWtHDGG');

INSERT INTO categoria (nombre) VALUES
('Postres dietéticos'),
('Postres de chocolate'),
('Postres con helado'),
('Postres con frutas'),
('Bebidas');

INSERT INTO producto (nombre, precio, descripcion, disponible, foto_url, id_categoria) VALUES
-- 1 Dietéticos
('Brownie sin azúcar', 12000.00, 'Brownie húmedo endulzado con stevia y cacao 70%.', 1, 'img/dieteticos/brownie-stevia.jpg', 1),
('Cheesecake light', 15000.00, 'Base integral y crema light con frutos rojos.', 1, 'img/dieteticos/cheesecake-light.jpg', 1),
('Mousse de cacao light', 13500.00, 'Aireado, sin azúcar refinada, toque de vainilla.', 1, 'img/dieteticos/mousse-cacao.jpg', 1),
('Galletas de avena y cacao', 9500.00, 'Crujientes, con chips de chocolate amargo.', 1, 'img/dieteticos/galletas-avena.jpg', 1),

-- 2 Chocolate
('Torta de chocolate artesanal', 28000.00, 'Capas de bizcocho de cacao belga con ganache semiamargo.', 1, 'img/chocolate/torta-artesanal.jpg', 2),
('Trufas de cacao', 18000.00, 'Caja de 6 trufas rellenas de ganache y cacao en polvo.', 1, 'img/chocolate/trufas.jpg', 2),
('Coulant de chocolate', 14000.00, 'Volcán de chocolate con centro líquido, porción individual.', 1, 'img/chocolate/coulant.jpg', 2),
('Brownie triple cacao', 13000.00, 'Densidad chocolateada con nueces tostadas.', 1, 'img/chocolate/brownie-choco.jpg', 2),
('Macarons de cacao rosa', 16000.00, 'Caja de 4: cacao, frambuesa y ganache rosa.', 1, 'img/chocolate/macarons-cacao.jpg', 2),

-- 3 Helado
('Sundae de vainilla y frutos rojos', 16000.00, 'Helado de vainilla, salsa de frutos rojos y crunch de almendras.', 1, 'img/helado/sundae-frutos.jpg', 3),
('Banana split Dulcella', 19000.00, 'Plátano, tres bolas, chantilly y chips de chocolate.', 1, 'img/helado/banana-split.jpg', 3),
('Affogato artesanal', 12500.00, 'Helado de vainilla bañado en espresso caliente.', 1, 'img/helado/affogato.jpg', 3),
('Parfait rosa de frutas', 15500.00, 'Capas de helado, crema y coulis de fresa.', 1, 'img/helado/parfait-rosa.jpg', 3),

-- 4 Frutas
('Ensalada de frutas con yogurt', 13000.00, 'Frutas de temporada, yogurt natural y granola.', 1, 'img/frutas/ensalada-yogurt.jpg', 4),
('Tarta de fresa', 17000.00, 'Crema pastelera, fresas frescas y glaseado brillante.', 1, 'img/frutas/tarta-fresa.jpg', 4),
('Pavlova de frutos rojos', 18500.00, 'Merengue crujiente, crema y berries.', 1, 'img/frutas/pavlova.jpg', 4),
('Copa mango y coco', 14500.00, 'Mango confitado, crema de coco y crumble.', 1, 'img/frutas/copa-mango.jpg', 4),

-- 5 Bebidas
('Malteada de chocolate', 11000.00, 'Leche, helado de chocolate y crema batida.', 1, 'img/bebidas/malteada-choco.jpg', 5),
('Limonada de coco', 9000.00, 'Limón fresco, agua de coco y un toque de menta.', 1, 'img/bebidas/limonada-coco.jpg', 5),
('Café helado artesanal', 10000.00, 'Espresso, leche vaporizada y hielo.', 1, 'img/bebidas/cafe-helado.jpg', 5),
('Smoothie de fresa', 10500.00, 'Fresas, yogurt y un toque de miel.', 1, 'img/bebidas/smoothie-fresa.jpg', 5),
('Chocolate caliente rosa', 11500.00, 'Cacao belga, leche cremosa y espuma rosa.', 0, 'img/bebidas/chocolate-caliente.jpg', 5);

INSERT INTO pedido (nombre_cliente, telefono_cliente, direccion_entrega, fecha, total) VALUES
('Ana Pérez', '3001112233', 'Calle 10 # 5-20, Chapinero', CURDATE(), 37000.00),
('Carlos Gómez', '3012223344', 'Carrera 7 # 45-12', CURDATE(), 16000.00),
('Laura Ruiz', '3023334455', 'Av. Suba # 100-30', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 34000.00),
('María Fernanda López', '3104445566', 'Calle 85 # 12-40', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 14000.00),
('Andrés Castillo', '3115556677', 'Cra. 15 # 93-20', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 11000.00),
('Valentina Mora', '3126667788', 'Calle 116 # 7-50', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 19000.00),
('Diego Herrera', '3137778899', 'Av. El Dorado # 68-90', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 18000.00),
('Camila Soto', '3148889900', 'Calle 53 # 10-15', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 17000.00),
('Julián Pardo', '3159990011', 'Carrera 11 # 70-22', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 9000.00),
('Sofía Méndez', '3201112233', 'Calle 26 # 4-18', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 13500.00),
('Ricardo Núñez', '3212223344', 'Cra. 50 # 80-10', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 12500.00),
('Elena Vargas', '3223334455', 'Calle 72 # 9-33', DATE_SUB(CURDATE(), INTERVAL 6 DAY), 13000.00);

INSERT INTO pedido_detalle (id_pedido, id_producto, cantidad, precio_unitario) VALUES
(1, 5, 1, 28000.00), (1, 19, 1, 9000.00),
(2, 10, 1, 16000.00),
(3, 14, 1, 13000.00), (3, 21, 2, 10500.00),
(4, 7, 1, 14000.00),
(5, 18, 1, 11000.00),
(6, 11, 1, 19000.00),
(7, 6, 1, 18000.00),
(8, 15, 1, 17000.00),
(9, 19, 1, 9000.00),
(10, 3, 1, 13500.00),
(11, 12, 1, 12500.00),
(12, 8, 1, 13000.00);
