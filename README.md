# Event Booking API

This API allows users to manage events, attendees, and bookings with proper validation and constraints.

---

## Features

- Manage Events: Create, update, delete, and list events.
- Manage Attendees: Register and manage attendee information.
- Booking System: Book attendees for events with overbooking and duplicate booking prevention.
- Authentication & Authorization (outlined below).

---

## API Endpoints Overview

| Resource   | HTTP Method | Endpoint              | Auth Required | Description                    |
|------------|-------------|---------------------  |---------------|--------------------------------|
| Events     | GET         | `/api/events`         | Yes           | List all events                |
| Events     | POST        | `/api/events`         | Yes           | Create a new event             |
| Events     | GET         | `/api/events/{id}`    | Yes           | Get event details              |
| Events     | PUT         | `/api/events/{id}`    | Yes           | Update an event                |
| Events     | DELETE      | `/api/events/{id}`    | Yes           | Delete an event                |
| Attendees  | POST        | `/api/attendees`      | No            | Register a new attendee        |
| Attendees  | GET         | `/api/attendees`      | Yes           | Get attendee details           |
| Attendees  | PUT         | `/api/attendees`      | Yes           | Update attendee information    |
| Attendees  | DELETE      | `/api/attendees`      | Yes           | Delete an attendee             |
| Bookings   | POST        | `/api/bookings`       | Yes           | Book an attendee for an event  |

---

## 🔐 Authentication & Authorization

### Event Management
- All event management endpoints require authenticated API consumers.
- Authentication is done using Laravel Sanctum token-based authentication.
- Authenticated users can create, update, delete, and view events.

### Attendee Registration
- Attendee registration (`POST /api/attendees`) is **public** and does **not** require authentication.
- Other attendee management routes require authentication.

### Booking System
- Booking endpoints require authentication.
- The system prevents overbooking (cannot book if event capacity is full).
- Duplicate bookings (same attendee for same event) are not allowed.

### Implementation Notes
- Laravel Sanctum is used to manage API tokens and guard routes with `auth:sanctum` middleware.
- Roles and policies can be added for more granular authorization if needed.

---

## Requirements

- PHP >= 8.1  
- Composer  
- Laravel 10.x  
- MySQL or other supported database  
- Docker & Docker Compose (optional, for containerized setup)

---

## Installation & Setup (Without Docker)

1. Clone the repository `git clone <repo-url> && cd <repo-folder>`
2. Run `composer install`  
3. Copy `.env.example` to `.env` and set your database and app settings  
4. Generate app key `php artisan key:generate`
5. Run migrations: `php artisan migrate`  
6. (Optional) Install and configure Sanctum for authentication  
7. Start the server: `php artisan serve`  
---

## Installation & Setup (With Docker)

#### Prerequisites

- Docker
- Docker Compose

#### Steps

1. Clone the repository `git clone <repo-url> && cd <repo-folder>`
2. Copy `.env.example` to `.env` and set your database and app settings 
3. Start containers `docker-compose up -d --build`
4. Run setup commands inside container 
    `docker exec -it laravel-app bash`
    `php artisan key:generate`
    `php artisan migrate`
    `exit`
5. Access the app : Visit http://localhost:8000

## Testing the API

Use Postman, Insomnia, or any API client to test the endpoints.  
Remember to add your Sanctum token in the `Authorization` header as `Bearer {token}` for protected routes.

---

## API Documentation

### 📘 Postman
You can view the full documentation in Postman: (https://documenter.getpostman.com/view/45732318/2sB2x3oDRh)

## License

MIT License

---


