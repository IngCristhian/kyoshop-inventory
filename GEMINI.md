# GEMINI.md - KyoShop Inventory System

## Project Overview

This project is a web-based inventory management system for a clothing store, named **KyoShop Inventory System**. It is built with **pure PHP** following a simple **Model-View-Controller (MVC)** pattern.

- **Backend:** PHP 8.0+
- **Database:** MySQL 8.0+
- **Frontend:** Bootstrap 5.3, Vanilla JavaScript (ES6+), and custom CSS.
- **Deployment:** The application is specifically designed for easy deployment on a **cPanel** shared hosting environment.

The system provides the following key features:
-   Full CRUD (Create, Read, Update, Delete) functionality for products.
-   Image uploads for products.
-   Automatic generation of unique product codes.
-   Product categorization, stock management with low stock alerts.
-   A dashboard with real-time inventory statistics.
-   Advanced search and filtering capabilities.
-   User authentication and authorization (Admin/User roles).

## Building and Running

### Local Development

1.  **Clone the repository.**
2.  **Database Setup:**
    -   Import the database schema from `sql/database_development.sql` into your local MySQL server.
    -   The development database is named `kyosankk_inventory_dev`.
3.  **Configuration:**
    -   The application uses environment variables for configuration. You'll need to set the following in your local environment, or modify `config/database.php` and `config/config.php` to hardcode them (not recommended).
        -   `APP_URL`: The base URL of the application (e.g., `http://localhost:8000`).
        -   `DB_HOST`: Database host (e.g., `localhost`).
        -   `DB_NAME`: Database name (e.g., `kyosankk_inventory_dev`).
        -   `DB_USER`: Database username.
        -   `DB_PASSWORD`: Database password.
4.  **Running the application:**
    -   Use PHP's built-in web server:
        ```bash
        php -S localhost:8000
        ```
    -   Access the application at `http://localhost:8000`.

### Production (cPanel)

-   Detailed deployment instructions are available in `DEPLOY.md`.

## Development Conventions

-   **MVC Architecture:**
    -   **Models:** Located in `models/`, they handle data interaction with the database. `models/Producto.php` is a primary example.
    -   **Views:** Located in `views/`, they are responsible for the presentation layer. The main layout is `views/layouts/master.php`.
    -   **Controllers:** Located in `controllers/`, they contain the business logic, handling user input and interacting with models and views. `controllers/ProductoController.php` is the main controller for product management.
-   **Routing:** A simple front-controller pattern is used in `index.php`, which maps URL paths to controller actions. It is not a full-featured router, but rather a switch statement.
-   **Database Access:** A singleton `Database` class in `config/database.php` provides a PDO connection. It includes helper methods for common database operations like `fetchAll`, `fetch`, `insert`, etc.
-   **Configuration:** Application and database configurations are in `config/config.php` and `config/database.php` respectively. The application relies on environment variables for sensitive information.
-   **Frontend:**
    -   JavaScript is primarily located in `assets/js/app.js`, and it handles tasks like live search, form validation, and image previews.
    -   CSS styles are in `assets/css/`, with `style.css` containing custom styles that override or extend Bootstrap.
-   **Security:**
    -   The application implements basic security measures such as CSRF protection, prepared statements to prevent SQL injection, and input sanitization.
-   **Dependencies:** The project uses CDN links for Bootstrap, so there is no `package.json` or other dependency manager for frontend assets.
