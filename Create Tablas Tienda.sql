-- Crear la tabla 'Productos' para almacenar información de teléfonos celulares
CREATE TABLE Productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50),
    modelo VARCHAR(100),
    precio DECIMAL(10, 2),
    descripcion TEXT,
    imagen VARCHAR(255) 
);

-- Insertar ejemplos de productos de las marcas iPhone, Samsung y Xiaomi con orden específico
INSERT INTO Productos (id, marca, modelo, precio, descripcion, imagen)
VALUES
    (1, 'iPhone', 'iPhone 13 Pro Max', 4499.99, '256GB Color Gris.', 'https://m.media-amazon.com/images/I/61i8Vjb17SL.jpg'),
    (2, 'iPhone', 'iPhone 13', 3059.99, '256GB Color Rosa.', 'https://store.storeimages.cdn-apple.com/4668/as-images.apple.com/is/refurb-iphone-13-pro-max-gold-2023?wid=1144&hei=1144&fmt=jpeg&qlt=90&.v=1679072988850'),
    (3, 'iPhone', 'iPhone 12 Pro', 3399.99, '256GB Color Azul.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1F1QTXCQMxx41XaeeaE4cRJV9FwUS1anUwI2yuxmZdcILWGOzrb-hoUojM2F4QoFLpyw&usqp=CAU'),
    (4, 'iPhone', 'iPhone SE', 1998.99, '64GB Color Rojo Blanco Negro.', 'https://telesun.nl/wp-content/uploads/2022/09/SE-2022-red.png'),
    (5, 'iPhone', 'iPhone 14 Pro', 5380.99, '128GB Color Morado 5G.', 'https://exitocol.vtexassets.com/arquivos/ids/15551177/celular-iphone-14-pro-128gb-5g-morado.jpg?v=638043199660970000'),
    (6, 'Samsung', 'Samsung Galaxy S21', 2599.99, '256GB Color Gris.', 'https://olimpica.vtexassets.com/arquivos/ids/1140588/image-1d02c2da7d454be88148624046ca26a3.jpg?v=638276212978300000'),
    (7, 'Samsung', 'Samsung Galaxy S22', 3599.99, '256GB Color Verde 5G.', 'https://exitocol.vtexassets.com/arquivos/ids/19706186/Celular-SAMSUNG-Galaxy-S22-5G-256-GB-Verde-Buds-2-Smart-Tag-Plus-3180663_a.jpg?v=638304127094000000'),
    (8, 'Samsung', 'Samsung Galaxy A52', 1499.99, '128GB Color Negro.', 'https://cdn1.totalcommerce.cloud/mercacentro/product-zoom/es/celular-samsung-galaxy-a52-128g-negro-2.webp'),
    (9, 'Xiaomi', 'Xiaomi Mi 11', 1999.99, '128GB Color Negro.', 'https://m.media-amazon.com/images/I/51MyBbilJfS.jpg'),
    (10, 'Xiaomi', 'Xiaomi Redmi Note 10', 665.99, '128GB Color Gris.', 'https://i5.walmartimages.com.mx/mg/gm/3pp/asr/7c47e900-0eee-4ddc-8fad-b28d25e15906.650e3c47b3a6374f5dc38b7f1dd5ff5e.jpeg?odnHeight=2000&odnWidth=2000&odnBg=ffffff')

-- Crear la tabla 'Usuarios' con el campo 'rol' para gestionar información de usuarios y roles
CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        salt VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        rol VARCHAR(50) NOT NULL,
        UNIQUE (username),
        UNIQUE (email)
    )