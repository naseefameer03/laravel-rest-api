
# Laravel REST API

## Project Purpose

This project demonstrates a working Laravel REST API, integrating essential Laravel packages and basic authentication. It is designed to showcase proper RESTful API structure, usage, and best practices for modern Laravel applications.

## Features

- RESTful API structure following Laravel conventions
- Basic authentication (Laravel Sanctum or Passport)
- Integration of commonly used Laravel packages
- User registration and login endpoints
- Example CRUD endpoints
- Proper API response formatting

## Installation

1. Clone the repository:
	```sh
	git clone <repository-url>
	cd laravel-rest-api
	```
2. Install dependencies:
	```sh
	composer install
	npm install
	```
3. Copy the example environment file and set your configuration:
	```sh
	cp .env.example .env
	php artisan key:generate
	```
4. Run migrations:
	```sh
	php artisan migrate
	```
5. (Optional) Seed the database:
	```sh
	php artisan db:seed
	```
6. Start the development server:
	```sh
	php artisan serve
	```

## Usage

Use an API client (like Postman) to interact with the endpoints. Refer to the API Endpoints section below for available routes.

## Authentication

This project uses basic authentication for API access. Register a user and log in to receive an access token, which must be included in the `Authorization` header for protected routes.

Example:
```
Authorization: Bearer <token>
```

## API Endpoints

| Method | Endpoint           | Description                |
|--------|--------------------|----------------------------|
| POST   | /api/register      | Register a new user        |
| POST   | /api/login         | User login                 |
| GET    | /api/user          | Get authenticated user     |
| ...    | ...                | Additional CRUD endpoints  |

> Expand this section with more endpoints as your API grows.

## License

This project is open-source and available under the [MIT license](LICENSE).
