# Laravel JWT Authentication API

A RESTful API built with Laravel 12 that handles JWT authentication, role-based access control, and user management. I've structured this following clean architecture principles to keep things organized and maintainable.

## 📋 Table of Contents

- [What's Included](#whats-included)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [API Documentation](#api-documentation)
- [Seeders](#seeders)
- [Postman Collection](#postman-collection)
- [Project Structure](#project-structure)
- [Architecture](#architecture)
- [Security](#security)

---

## What's Included

Here's what you get out of the box:

- **JWT Authentication** - Token-based auth that's secure and stateless
- **Token Refresh** - Users can refresh their tokens without logging in again
- **Role-Based Access Control** - Simple admin/user roles for now
- **User Management** - Users can update their profile, change password, and delete their account
- **User Listing** - Admins can list all users with pagination, search, and sorting
- **Strong Password Validation** - Enforces uppercase, lowercase, and numbers
- **API Versioning** - All endpoints are under `/api/v1`
- **Consistent Responses** - Every endpoint returns data in the same format
- **Database Transactions** - Important operations are wrapped in transactions

---

## Tech Stack

- Laravel 12
- PHP 8.2+
- JWT Auth (tymon/jwt-auth)
- MySQL/PostgreSQL/SQLite (your choice)

---

## Getting Started

### Prerequisites

Make sure you have PHP 8.2+, Composer, and a database ready.

### Installation Steps

1. **Clone and install**

```bash
git clone https://github.com/MohamadKheerMustafa/apostrophe-center-test.git
cd apostrophe-center-test
composer install
```

2. **Setup environment**

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

3. **Configure your database**

Edit `.env` and update these lines:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

4. **Run migrations**

```bash
php artisan migrate
```

5. **Seed admin user (optional)**

```bash
php artisan db:seed --class=AdminSeeder
```

This creates an admin user:
- Email: `admin@example.com`
- Password: `Admin@123456`

**Important:** Change this password in production!

6. **Start the server**

```bash
php artisan serve
```

Your API is now running at `http://localhost:8000`

---

## Configuration

### JWT Settings

You can tweak JWT settings in `.env`:

```env
JWT_SECRET=your_jwt_secret_key
JWT_TTL=60                    # Token lifetime in minutes (default: 60 minutes)
JWT_REFRESH_TTL=20160         # How long tokens can be refreshed (default: 2 weeks)
JWT_BLACKLIST_ENABLED=true
```

### App Settings

```env
APP_NAME="Your App Name"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

---

## API Documentation

### Base URL

All endpoints start with `/api/v1`:

```
http://your-domain.com/api/v1
```

### Authentication

This API uses JWT tokens. Include your token in the Authorization header like this:

```
Authorization: Bearer your_token_here
```

When you register or login, you'll get a token back. Use that for protected endpoints.

### Response Format

All responses follow this format:

**Success:**
```json
{
  "status": 200,
  "errorCode": 0,
  "data": { /* your data here */ },
  "message": "Success message"
}
```

**Error:**
```json
{
  "status": 400,
  "errorCode": 1,
  "data": null,
  "message": "Error message"
}
```

**Validation Errors:**
```json
{
  "status": 400,
  "errorCode": 1,
  "data": {
    "errors": {
      "email": ["The email field is required."],
      "password": ["The password must be at least 8 characters."]
    }
  },
  "message": "Validation failed"
}
```

### HTTP Status Codes

- `200` - Everything worked
- `201` - Resource created
- `400` - Bad request (validation errors, etc.)
- `401` - Unauthorized (invalid or expired token)
- `403` - Forbidden (missing permissions)
- `404` - Not found
- `500` - Server error

---

## Endpoints

### Authentication

#### Register User

`POST /api/v1/auth/register`

No authentication needed. Just send:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password123",
  "password_confirmation": "Password123"
}
```

**Password requirements:**
- At least 8 characters
- One uppercase letter
- One lowercase letter
- One number

You'll get back a token along with the user data.

#### Login

`POST /api/v1/auth/login`

```json
{
  "email": "john@example.com",
  "password": "Password123"
}
```

Returns your user data and a JWT token.

#### Refresh Token

`POST /api/v1/auth/refresh`

Send your expired token in the Authorization header. As long as it's within the refresh window (default 2 weeks), you'll get a new token back.

#### Get Current User

`GET /api/v1/auth/me`

Protected endpoint. Returns your user info (without the token).

#### Update Profile

`PUT /api/v1/auth/update-profile`

Update your name, email, or password. All fields are optional - send only what you want to update.

**Update name:**
```json
{
  "name": "John Smith"
}
```

**Update password:**
```json
{
  "old_password": "OldPassword123",
  "password": "NewPassword123",
  "password_confirmation": "NewPassword123"
}
```

The new password must be different from the old one, and you need to provide the current password.

#### Delete Account

`DELETE /api/v1/auth/delete-account`

Permanently deletes your account. Requires password confirmation:

```json
{
  "password": "Password123"
}
```

---

### User Management

#### List Users

`GET /api/v1/users`

**Admin only!** Get a paginated list of all users.

**Query parameters:**
- `page` - Page number (default: 1)
- `per_page` - Items per page, 1-100 (default: 15)
- `search` - Search in name and email
- `order_by` - Sort by: `id`, `name`, `email`, `created_at`, `updated_at`
- `order_direction` - `asc` or `desc`

**Examples:**
```
GET /api/v1/users
GET /api/v1/users?search=john
GET /api/v1/users?order_by=created_at&order_direction=desc&page=2
```

---

### Role Management

#### Assign Admin Role

`POST /api/v1/roles/assign-admin`

**Admin only!** Promote a user to admin.

```json
{
  "user_id": 5
}
```

#### Revoke Admin Role

`POST /api/v1/roles/revoke-admin`

**Admin only!** Demote an admin back to regular user.

```json
{
  "user_id": 5
}
```

**Note:** You can't revoke your own admin role (safety feature).

---

### About Role Management

The current implementation uses a simple role system with just `admin` and `user` roles stored in the users table. This works fine for basic needs, but if you need something more advanced (multiple roles, permissions, role hierarchies), consider integrating:

- **[Spatie Permission](https://spatie.be/docs/laravel-permission)** - Great for complex permission systems
- **[Laratrust](https://laratrust.santigarcor.me/)** - Another solid option for roles and permissions

Both packages are well-maintained and offer way more flexibility if your requirements grow.

---

## Seeders

### Admin Seeder

Create an admin user quickly:

```bash
php artisan db:seed --class=AdminSeeder
```

This creates:
- **Email:** `admin@example.com`
- **Password:** `Admin@123456`
- **Role:** `admin`

**Important:** Make sure to change these credentials in production! The seeder checks if an admin with this email already exists, so it won't create duplicates.

---

## Postman Collection

I've included a complete Postman collection ready to use.

**Location:** `postman/Laravel_JWT_API.postman_collection.json`

### How to Use

1. Open Postman
2. Click Import
3. Select the collection file
4. The `base_url` variable is already set to `http://localhost:8000/api/v1` - change it if needed

Or:
Live Postman Collection : https://documenter.getpostman.com/view/28504650/2sB3dLUX7w

The collection includes all endpoints with example requests. After logging in or registering, the token is automatically saved to the `auth_token` variable, so you don't have to copy-paste it everywhere.

---

## Project Structure

Here's how things are organized:

```
app/
├── Http/
│   ├── Controllers/          # All controllers
│   ├── Middleware/           # Custom middleware (AdminMiddleware)
│   ├── Requests/             # Form request validators
│   │   ├── Auth/            # Auth-related requests
│   │   ├── User/            # User-related requests
│   │   └── Role/            # Role-related requests
│   └── Resources/            # API resource transformers
├── Interfaces/               # Service interfaces
├── Models/                   # Eloquent models
├── Providers/                # Service providers
└── Services/                 # Business logic

routes/
├── api.php                   # Main API routes
└── v1/                       # Version 1 route files
    ├── auth.php
    ├── users.php
    └── roles.php

database/
└── seeders/
    └── AdminSeeder.php       # Admin user seeder
```

---

## Architecture

I've structured this following clean architecture principles. Here's the flow:

**Controller → Interface → Service → Model**

- **Controllers** handle HTTP stuff - requests, responses, validation
- **Interfaces** define contracts for services
- **Services** contain the actual business logic
- **Models** interact with the database

This separation makes things easier to test and maintain. Services are bound to interfaces in `RepositoryServiceProvider`, so you can swap implementations easily if needed.

---

## Security

### Authentication & Authorization

- JWT tokens for stateless auth
- Token refresh mechanism
- Role-based access control
- Middleware protecting routes

### Password Security

- Strong password requirements (min 8 chars, mixed case, numbers)
- Passwords are hashed with bcrypt
- Password confirmation required
- Old password verification when changing password

### Data Protection

- All inputs are validated
- SQL injection prevention (parameterized queries + whitelist validation)
- CSRF protection
- Database transactions for critical operations
- Token blacklisting to prevent reuse

### Best Practices

- API versioning for future changes
- Consistent error responses
- FormRequest classes for validation
- Type hints everywhere
- PSR coding standards

---

## License

MIT License - feel free to use this for your projects.

---

## Author

**Mohamad Kheer Mustafa**

---

**Last Updated:** November 2024
