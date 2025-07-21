# Hardware Shop & Warehouse Management System

This is a web-based management system for a hardware shop and warehouse. It is built with PHP and uses a MariaDB database.

## Features

- User authentication with roles (Admin, Cashier, Warehouse)
- Inventory management (add, edit, delete products)
- Barcode scanning
- Purchase module
- Sales module
- Return management
- Invoice printing
- Reports module
- Dashboard with analytical graphs

## Requirements

- A web server with PHP support (e.g., Apache, Nginx)
- MariaDB 10 or higher
- A web browser

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/your-username/your-repo-name.git
   ```

2. **Create a database:**
   - Create a new database in MariaDB.
   - Import the `database.sql` file to create the necessary tables and seed the database with initial data.

3. **Configure the database connection:**
   - Open the `config/database.php` file.
   - Replace the placeholder values for `DB_USER`, `DB_PASS`, and `DB_NAME` with your actual database credentials.

4. **Deploy the application:**
   - Copy the project files to your web server's document root.

5. **Run the application:**
   - Open your web browser and navigate to the project's URL.

## Default Login Credentials

The default login credentials for all user roles are:

- **Username:** admin
- **Password:** password

## Troubleshooting

If you are unable to log in after setting up the project, please check the following:

1. **Database Connection:**
   - Double-check that the database credentials in `config/database.php` are correct.
   - Ensure that the MariaDB server is running and accessible.

2. **Session Handling:**
   - Make sure that the `session.save_path` in your `php.ini` file is set to a writable directory.
   - Check your browser's developer tools to see if a session cookie is being set after you attempt to log in.

3. **Error Logs:**
   - Check your web server's error logs for any PHP errors that might be occurring. The location of the error logs will vary depending on your web server configuration.

4. **Debugging:**
   - If you are still unable to log in, you can uncomment the debugging lines in `templates/login.php` to get more information about what is happening during the login process.
