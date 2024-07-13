# Market Store API

Market Store API is a backend application similar to the Polish Allegro, built using Laravel. It provides functionalities to manage orders, authentication, users, and products.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Contributing](#contributing)
- [Contact](#contact)

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/your-username/market-store-api.git
    cd market-store-api
    ```

2. Install dependencies:
    ```sh
    composer install
    npm install
    ```

3. Copy `.env.example` to `.env` and configure your environment variables:
    ```sh
    cp .env.example .env
    ```

4. Generate an application key:
    ```sh
    php artisan key:generate
    ```

5. Run the migrations (with seeders if you have any):
    ```sh
    php artisan migrate --seed
    ```

## Usage

1. Start the local development server:
    ```sh
    php artisan serve
    ```

2. Access the application at `http://localhost:8000`.

## API Endpoints

The following controllers handle the various API endpoints:

- **AuthController**: Manages user authentication.
- **UserController**: Manages user data and profiles.
- **ProductController**: Handles product-related operations.
- **OrderController**: Manages orders.

Swagger documentation is available to provide detailed information about each endpoint. Refer to the screenshots provided for the Swagger UI.

## Contributing

This is a private project, and I am not currently accepting contributions.

## Contact

For any inquiries or questions, please DM me directly.
