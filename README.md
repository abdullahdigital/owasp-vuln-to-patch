# Dentist Appointment Booking System

This project is a full-stack application for managing dentist appointments, built with Laravel for the backend API and Svelte for the frontend user interface.

## Features

- User authentication (admin and regular users)
- Appointment scheduling
- Service management
- Admin dashboard for managing appointments, users, and logs
- Contact form

## Project Structure

The project is organized into the following main directories:

- `backend/`: Contains the PHP backend application (insecure and secure versions).
- `frontend/`: Contains the Svelte frontend application.
- `laravel/`: Contains the Laravel API backend.
- `database/`: Contains SQLite database file.
- `test/`: Contains various test scripts and files.
- `vendor/`: Contains PHP dependencies managed by Composer.

## Setup Instructions

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js >= 16
- npm or Yarn

### Backend Setup (Laravel)

1.  Navigate to the `laravel` directory:
    ```bash
    cd laravel
    ```
2.  Install Composer dependencies:
    ```bash
    composer install
    ```
3.  Copy the `.env.example` file to `.env`:
    ```bash
    cp .env.example .env
    ```
4.  Generate an application key:
    ```bash
    php artisan key:generate
    ```
5.  Run database migrations and seeders:
    ```bash
    php artisan migrate:fresh --seed
    ```
6.  Start the Laravel development server:
    ```bash
    php artisan serve --port=8000
    ```

### Frontend Setup (Svelte)

1.  Navigate to the `frontend` directory:
    ```bash
    cd frontend
    ```
2.  Install Node.js dependencies:
    ```bash
    npm install
    ```
3.  Start the Svelte development server:
    ```bash
    npm run dev
    ```

## Usage

- The frontend application will be accessible at `http://localhost:5173` (or a similar port).
- The backend API will be running on `http://localhost:8000`.

### Admin Login

- **URL**: `http://localhost:5173/#/adminlogin`
- **Username**: `admin@example.com`
- **Password**: `password`

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.