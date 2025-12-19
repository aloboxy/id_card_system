# System Documentation: ID Card Management System

## 1. Introduction
The **ID Card Management System** is a robust web application designed to streamline the process of managing schools, students, staff, and generating professional ID cards. It features a custom drag-and-drop ID card designer, role-based access control (RBAC), and bulk ID card generation capabilities.

## 2. Technology Stack

### Backend
*   **Framework**: Laravel 12.x
*   **Language**: PHP 8.2+
*   **Database**: MySQL
*   **Packages**:
    *   `spatie/laravel-permission`: Role-Based Access Control (RBAC)
    *   `yajra/laravel-datatables`: Server-side processing for complex tables
    *   `simplesoftwareio/simple-qrcode`: QR Code generation for ID cards

### Frontend
*   **Styling**: Tailwind CSS v4.0 (via Vite)
*   **Scripting**: JavaScript (ES6+)
*   **Libraries**:
    *   `fabric.js`: Canvas manipulation for the ID Card Designer
    *   `jquery`: Required for DataTables integration
    *   `fontawesome`: Icons

## 3. Prerequisites
Before installing the application, ensure your server meets the following requirements:
*   PHP >= 8.2
*   Composer
*   Node.js & NPM
*   MySQL Database server

## 4. Installation Guide

### Step 1: Clone the Repository
```bash
git clone <repository-url>
cd id_card_system
```

### Step 2: Install Backend Dependencies
```bash
composer install
```

### Step 3: Configure Environment
Copy the example environment file and configure your database settings:
```bash
cp .env.example .env
nano .env
```
Update the following variables:
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### Step 4: Generate Application Key
```bash
php artisan key:generate
```

### Step 5: Run Migrations and Seeders
Run the database migrations and seed the default roles and permissions:
```bash
php artisan migrate --seed
```
*This command creates the necessary tables and populates them with initial data, including the default `admin` role and permissions.*

### Step 6: Install Frontend Dependencies & Build
```bash
npm install
npm run build
```

### Step 7: Start the Server
```bash
php artisan serve
```
Access the application at `http://localhost:8000`.

## 5. System Modules & Functionality

### 5.1 Authentication & Access Control
The system uses `spatie/laravel-permission` for granular access control.
*   **Roles**: Define groups of permissions (e.g., Admin, User, Editor).
*   **Permissions**: Specific actions users can perform (e.g., `student-create`, `school-list`).
*   **Role Management**: Admins can create custom roles and assign specific permissions via a dynamic UI.
*   **User Management**: Admins can create users and assign them roles.

### 5.2 School Management
*   **Functionality**: Manage multiple schools within the system.
*   **Features**: Add, edit, delete, and list schools. Each student is linked to a school.

### 5.3 Student Management
*   **Functionality**: Comprehensive database of students.
*   **Features**:
    *   Add/Edit student details (Name, ID, Class, etc.).
    *   **Photo Capture**: Capture student photos directly using a webcam or upload from file.
    *   **Fingerprint**: Upload fingerprint images.
    *   **DataTables**: Efficiently search, sort, and filter thousands of records.

### 5.4 ID Card Designer
*   **Functionality**: A powerful, drag-and-drop interface for creating ID card templates.
*   **Features**:
    *   **Canvas**: Built with Fabric.js for pixel-perfect design.
    *   **Tools**: Add text, rectangles, images, and QR codes.
    *   **Placeholders**: Use dynamic placeholders (e.g., `{{ name }}`, `{{ student_id }}`) that are automatically replaced with actual student data during generation.
    *   **Styling**: specific font families, sizes, colors, and alignments.

### 5.5 ID Card Generation
*   **Functionality**: Generate ID cards in bulk.
*   **Features**: Select a template and a batch of students to generate print-ready ID cards. The system automatically populates the templates with student data and QR codes.

### 5.6 Settings
*   **Functionality**: System-wide configurations.
*   **Features**: Update system name, logo, and other global preferences.

## 6. Directory Structure
Key directories to verify during development/maintenance:
*   `app/Models`: Eloquent models (User, Student, School, Role, etc.).
*   `app/Http/Controllers`: Backend logic for each module.
*   `resources/views`: Blade templates for the UI.
*   `routes/web.php`: Route definitions and middleware groups.

## 7. Troubleshooting
*   **"PermissionDoesNotExist" Error**: Ensure the permission seeder has run (`php artisan db:seed --class=PermissionTableSeeder`) and cache is cleared (`php artisan permission:cache-reset`).
*   **Style Issues**: Ensure `npm run build` has been executed to compile Tailwind assets.
*   **Database Connectivity**: Verify `.env` credentials and ensure the MySQL service is running.

---
*Documentation generated on 2025-12-19*
