# Simple HRIS API

**Simple HRIS** is a lightweight implementation of a Human Resource Information System (HRIS) built with **Laravel**.  
This project focuses on the backend side (API only) and was created as practice to refresh Laravel fundamentals.

---

## ✨ Features

-   **CRUD**:
    -   Employees
    -   Leaves
    -   Attendances
    -   Users
-   **Authentication & Authorization**
    -   Login
    -   Role & Permission Management
    -   Default roles: `HR`, `Manager`, and `Employee`

---

## 🛠️ Tech Stack

-   [Laravel](https://laravel.com/) (Backend & API)
-   MySQL (Database)
-   [spatie/laravel-permission](https://spatie.be/docs/laravel-permission) (Roles & Permissions)

---

## 🚀 Instalasi

1. **Clone the repository**
    ```bash
    git clone https://github.com/username/simple-hris-api.git
    cd simple-hris-api
    ```
2. **Install dependencies via Composer:**
    ```bash
    composer install
    ```
3. **Copy the .env.example file and configure your environment:**
    ```bash
    cp .env.example .env
    ```
4. **Generate application key:**
    ```bash
    php artisan key:generate
    ```
5. **Run migrations and seeders:**
    ```bash
    php artisan migrate --seed
    ```
6. **Start the development server:**
    ```bash
    php artisan serve
    ```
