CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(150) NOT NULL,
    level INT DEFAULT 2
);

CREATE TABLE companies (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    cnpj VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    cep VARCHAR(10) NOT NULL,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL,
    category VARCHAR(30) NOT NULL,
    description LONGTEXT NOT NULL,
    image VARCHAR(200),
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(8,2) NOT NULL,
    id_company INT NOT NULL,
    FOREIGN KEY (id_company) REFERENCES companies (id) ON DELETE CASCADE
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    method_payment VARCHAR(50) NOT NULL,
    total DECIMAL(8,2) NOT NULL,
    data_sale TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_user INT NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users (id) ON DELETE CASCADE
);

CREATE TABLE sale_product (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    total DECIMAL(8,2) NOT NULL,
    quantity INT NOT NULL,
    id_sale INT NOT NULL,
    id_product INT NOT NULL,
    FOREIGN KEY (id_sale) REFERENCES sales (id) ON DELETE CASCADE,
    FOREIGN KEY (id_product) REFERENCES products (id) ON DELETE CASCADE
);

DELIMITER $$
CREATE TRIGGER update_quantity_product AFTER INSERT ON sale_product FOR EACH ROW
BEGIN
    DECLARE quantity_current INT;
    DECLARE quantity_new INT;

    SET quantity_current := (SELECT quantity FROM products WHERE id = NEW.id_product);
    SET quantity_new := quantity_current - NEW.quantity;

    UPDATE products SET quantity = quantity_new WHERE id = NEW.id_product;
END
$$