
# 📝 Nudger List API

This is a simple Laravel-based task management API using Sanctum for authentication.

## Requirements

- PHP >= 8.1
- Composer
- MySQL or SQLite
- Laravel 10.x
- Laravel Sanctum

## 🚀 Setup Instructions

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/nudger-list-api.git
cd nudger-list-api
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit the `.env` file to set your database credentials.

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Serve the Application

```bash
php artisan serve
```

### 6. 🔐 API Authentication with Sanctum

Register or login a user to receive a token:

```bash
POST /api/register
POST /api/login
```

Use the token returned to authenticate requests:

```
Authorization: Bearer {token}
```

## API Endpoints

### Task Routes

| Method | Endpoint                             | Description                                      |
|--------|--------------------------------------|--------------------------------------------------|
| POST   | /api/register                        | Register a new user                              |
| POST   | /api/login                           | Login and receive an access token                |
| POST   | /api/logout                          | Logout the authenticated user                    |
| GET    | /api/user/{id}                       | Get user details by ID                           |
| GET    | /api/tasks                           | List all tasks                                   |
| POST   | /api/tasks                           | Create a new task                                |
| GET    | /api/tasks/{id}                      | Get details of a specific task                   |
| PUT    | /api/tasks/{id}                      | Update a task                                    |
| PATCH  | /api/tasks/{id}/status               | Update only the status of a task                 |
| GET    | /api/tasks/status/{status}           | Filter tasks by status                           |
| DELETE | /api/tasks/{id}                      | Soft delete a task                               |
| GET    | /api/trashed/tasks                   | List all soft-deleted (trashed) tasks            |
| GET    | /api/task/trashed/{id}               | Show a specific soft-deleted task                |
| PATCH  | /api/tasks/{id}/restore              | Restore a soft-deleted task                      |
| DELETE | /api/tasks/{id}/force                | Permanently delete a soft-deleted task           |
| GET    | /api/search/tasks/{query}            | Search tasks by title or description             |


## Testing the API

You can test the API using tools like:

- Postman
- Curl
- Swagger UI

## Swagger API Documentation

Swagger is available to visualize and interact with the API.

### Access the Swagger UI

Run the Laravel server and visit:

```
http://localhost:8000/api/documentation
```

### Example: Search Task

```bash
curl -X GET "http://localhost:8000/api/search/tasks/meeting" -H "accept: application/json" -H "Authorization: Bearer {your_token}"
```

### Example: Create Task

```bash
curl -X POST "http://localhost:8000/api/tasks" -H "accept: application/json" -H "Authorization: Bearer {your_token}" -H "Content-Type: application/json" -d '{"title":"Test task", "description":"Task description"}'
```

### 🛠 Artisan Shortcuts

```bash
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan test
```

### 📄 License

MIT