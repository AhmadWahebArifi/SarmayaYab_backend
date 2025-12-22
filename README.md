# SarmayaYab Backend API

A Laravel-based REST API for the SarmayaYab investment management application.

## Features

- **User Authentication**: Registration, login, and logout with JWT tokens
- **Investment Management**: Create, read, update, and delete investments
- **Transaction Tracking**: Record deposits, withdrawals, and investment returns
- **Balance Management**: Track user balances and investment performance
- **API Rate Limiting**: Built-in protection against abuse
- **Request Validation**: Comprehensive input validation for all endpoints

## Tech Stack

- **PHP 8.1+**
- **Laravel 10.x**
- **MySQL Database**
- **Laravel Sanctum** for API authentication
- **JSON API responses**

## Installation

1. Clone the repository
2. Install dependencies:

   ```bash
   composer install
   ```

3. Create environment file:

   ```bash
   cp .env.example .env
   ```

4. Generate application key:

   ```bash
   php artisan key:generate
   ```

5. Configure your database in `.env` file

6. Run migrations:

   ```bash
   php artisan migrate
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication

- `POST /api/register` - Register new user
- `POST /api/login` - User login
- `POST /api/logout` - User logout (requires authentication)

### User Management

- `GET /api/user` - Get current user info
- `PUT /api/user` - Update user profile
- `GET /api/user/balance` - Get user balance

### Investments

- `GET /api/investments` - List user investments
- `POST /api/investments` - Create new investment
- `GET /api/investments/{id}` - Get specific investment
- `PUT /api/investments/{id}` - Update investment
- `DELETE /api/investments/{id}` - Delete investment
- `GET /api/investments/summary` - Get investment summary

### Transactions

- `GET /api/transactions` - List user transactions
- `POST /api/transactions` - Create new transaction
- `GET /api/transactions/{id}` - Get specific transaction
- `GET /api/transactions/summary` - Get transaction summary

## Database Schema

### Users

- Basic user information with balance tracking
- Account status management (active, inactive, suspended)

### Investments

- Investment records with type classification
- Expected vs actual returns tracking
- Status management (active, completed, cancelled)

### Transactions

- Financial transaction history
- Multiple transaction types (deposit, withdrawal, investment, return)
- Status tracking (pending, completed, failed)

## Authentication

The API uses Laravel Sanctum for token-based authentication. Include the token in the `Authorization` header:

```
Authorization: Bearer {your_token}
```

## Validation

All endpoints include comprehensive request validation. Invalid requests will receive detailed error messages with 422 status codes.

## Rate Limiting

- General API: 60 requests per minute per user/IP
- Authentication endpoints: 5 requests per minute per IP

## Error Responses

All errors return JSON responses with appropriate HTTP status codes:

- `400` - Bad Request (validation errors, insufficient balance, etc.)
- `401` - Unauthorized (invalid credentials, missing token)
- `403` - Forbidden (account inactive, unauthorized access)
- `404` - Not Found (resource doesn't exist)
- `422` - Unprocessable Entity (validation failures)
- `500` - Internal Server Error

## Development

### Running Tests

```bash
php artisan test
```

### Code Formatting

```bash
php artisan pint
```

### Clear Cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Security Features

- Password hashing with bcrypt
- Token-based authentication
- Input sanitization and validation
- SQL injection protection via Eloquent ORM
- CORS configuration
- Request rate limiting

## Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Update documentation for API changes
4. Use meaningful commit messages
