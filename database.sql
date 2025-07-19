CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Cashier', 'Warehouse') NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(255) NOT NULL,
    barcode VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    purchase_rate DECIMAL(10, 2) NOT NULL,
    selling_rate DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    aisle VARCHAR(255) NOT NULL,
    rack VARCHAR(255) NOT NULL,
    bin VARCHAR(255) NOT NULL
);

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier VARCHAR(255) NOT NULL,
    purchase_date DATE NOT NULL
);

CREATE TABLE purchase_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    rate DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer VARCHAR(255),
    sale_date DATE NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) NOT NULL,
    grand_total DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(255) NOT NULL
);

CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    rate DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE sales_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    return_date DATE NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id)
);

CREATE TABLE sales_return_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sales_return_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (sales_return_id) REFERENCES sales_returns(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE purchase_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    return_date DATE NOT NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id)
);

CREATE TABLE purchase_return_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_return_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (purchase_return_id) REFERENCES purchase_returns(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

INSERT INTO users (username, password, role) VALUES
('admin', 'password', 'Admin'),
('cashier', 'password', 'Cashier'),
('warehouse', 'password', 'Warehouse');
