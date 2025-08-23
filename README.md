# Laravel REST API

A robust and modern RESTful API built with Laravel 12, demonstrating best practices in API design, authentication, and resource management.

## Overview

This project serves as a foundation for building scalable REST APIs using Laravel. It leverages Laravel's latest features, including streamlined file structure, Eloquent ORM, and secure authentication mechanisms. The API is structured for clarity, maintainability, and extensibility.

## Features

- Follows RESTful API conventions using Laravel 12
- Secure authentication with Laravel Sanctum
- User registration and login endpoints
- Authenticated user profile retrieval
- Example CRUD endpoints for resource management
- Consistent API response formatting
- Integration with essential Laravel packages
- Ready for extension with additional resources

## Getting Started

### Prerequisites

- PHP 8.2.12 or higher
- Composer
- Node.js & npm
- MySQL or compatible database

### Installation

1. **Clone the repository:**
    ```sh
    git clone https://github.com/naseefameer03/laravel-rest-api
    cd laravel-rest-api
    ```

2. **Install PHP and JS dependencies:**
    ```sh
    composer install
    npm install
    ```

3. **Configure environment variables:**
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

4. **Set up your database credentials in `.env`.`

5. **Run database migrations:**
    ```sh
    php artisan migrate
    ```

6. **(Optional) Seed the database:**
    ```sh
    php artisan db:seed
    ```

7. **Start the development server:**
    ```sh
    php artisan serve
    ```

> For frontend changes, run `npm run dev` or `npm run build` as needed.

## Usage

Interact with the API using tools like [Postman](https://www.postman.com/) or [cURL](https://curl.se/). All endpoints are prefixed with `/api`.

## Authentication

Authentication is handled via Laravel Sanctum or Passport. Register a user and log in to receive an access token. Include this token in the `Authorization` header for protected routes:

```
Authorization: Bearer <token>
```

## API Endpoints

| Method | Endpoint      | Description                |
|--------|--------------|----------------------------|
| POST   | /api/register| Register a new user        |
| POST   | /api/login   | User login (returns token) |
| GET    | /api/user    | Get authenticated user     |
| ...    | ...          | Additional CRUD endpoints  |

> Expand this section as you add more resources and endpoints.

## Contributing

Contributions are welcome! Please follow Laravel's code style and submit pull requests for review.

## License

This project is open-source and available under the [MIT license](LICENSE).
