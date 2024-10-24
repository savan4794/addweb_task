# Project Setup Guide

This guide will walk you through setting up the project on your local environment.

## Prerequisites

Ensure that you have the following installed on your machine:

- **PHP 8.x** or higher
- **Composer**
- **Node.js** and **npm**
- **MySQL** or any supported database
- **Laravel 10.x** (or the version required for the project)

## Steps to Set Up the Project

1. **Clone the repository**

    ```bash
    git clone https://github.com/savan4794/addweb_task.git
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies and compile assets**

    ```bash
    npm install && npm run dev
    ```

4. **Set up environment variables**

    Copy the example `.env` file and update it with your database and email configurations:

    ```bash
    cp .env.example .env
    ```

5. **Run database migrations**

    ```bash
    php artisan migrate
    ```

6. **Run seeders**

    ```bash
    php artisan db:seed
    ```

7. **Start the server**

    ```bash
    php artisan serve
    ```

Visit `http://localhost:8000` to access the application.
