CREATE TABLE company (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    cnpj VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(200) NOT NULL,
    cep VARCHAR(10) NOT NULL
);

CREATE TABLE product (
    id INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    name VARCHAR(50) NOT NULL,
    category VARCHAR(30) NOT NULL,
    description LONGTEXT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    price DECIMAL(8,2) NOT NULL,
    id_company INT,
    CONSTRAINT fk_id_comapny FOREIGN KEY (id_company) REFERENCES company(id)
);