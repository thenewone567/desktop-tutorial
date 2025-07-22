CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE warehouse_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(255) NOT NULL,
    barcode VARCHAR(255) NOT NULL,
    category_id INT NOT NULL,
    subcategory_id INT,
    purchase_rate DECIMAL(10, 2) NOT NULL,
    selling_rate DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    min_stock INT,
    max_stock INT,
    warehouse_location_id INT,
    batch_number VARCHAR(255),
    expiry_date DATE,
    image VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id),
    FOREIGN KEY (warehouse_location_id) REFERENCES warehouse_locations(id)
);

CREATE TABLE stock_adjustments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    adjustment INT NOT NULL,
    reason VARCHAR(255),
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(255),
    email VARCHAR(255),
    address TEXT,
    credit_limit DECIMAL(10, 2)
);

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    sale_date DATE NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    discount DECIMAL(10, 2) NOT NULL,
    tax DECIMAL(10, 2) NOT NULL,
    grand_total DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(255) NOT NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
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

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    gstin VARCHAR(255),
    phone VARCHAR(255),
    email VARCHAR(255),
    address TEXT
);

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    purchase_date DATE NOT NULL,
    supplier_invoice VARCHAR(255),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
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

CREATE TABLE sales_returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    return_date DATE NOT NULL,
    reason VARCHAR(255),
    refund_amount DECIMAL(10, 2),
    refund_method VARCHAR(255),
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
    reason VARCHAR(255),
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
