# ADSSU Farmers Extension Services Management System

## Overview
The ADSSU Farmers’ Extension Services Portal is an online hub and robust management system used by the Ateneo de Davao University Senior High School to provide details, monetize, and update local farmers about new farming methods, agricultural techniques, training schedules, and technology updates.

It features a public-facing portal for prospective farmers to learn about the services and sign up, and a secure internal system tailored for three distinct user roles:
- **Admin**: Oversees the entire platform, manages extension workers and farmers, coordinates agricultural programs, schedules trainings, and generates statistical reports.
- **Extension Worker**: Manages assigned farmers, logs on-site field visits (with GPS tracking), and records the distribution of agricultural assistance.
- **Farmer**: A dedicated portal to view their service history, upcoming and attended trainings, and track any agricultural assistance they have received.

## Features
- **Public Landing Page**: Information hub for agricultural services and news.
- **Role-Based Dashboards**: Customized UI/UX for Admins, Extension Workers, and Farmers.
- **Modern UI/UX**: Premium SaaS-inspired design featuring glassmorphism elements, fully responsive layout, and smooth animations powered by Bootstrap 5 and custom CSS.
- **AJAX-Driven Interactions**: Seamless, asynchronous CRUD operations utilizing jQuery and PDO for optimized performance and security.
- **Real-Time Analytics**: Chart.js and dynamic data tables for visual reports.
- **Secure Authentication**: Includes password hashing (`password_hash`), CSRF protection, and role-based access control.

## Installation & Setup

1. **Prerequisites**
   - XAMPP/WAMP or any local server running PHP 8.0+ and MySQL.
   - A modern web browser.

2. **Database Configuration**
   - Start your Apache and MySQL servers.
   - Import the database schema located at `database/setup.sql` into a MySQL database named `adssu_farmers_db`.
     *(Note: This file will automatically create the database and required tables).*

3. **Application Configuration**
   - Ensure the project directory is placed in your local server's web root (e.g., `C:\xampp\htdocs\ADSSU Farmers Extension Services`).
   - The database connection settings are located in `config/database.php`. Modify them if your local MySQL uses a different username/password (default is `root` with no password).

4. **Testing the Application**
   - Navigate to the landing page to view the public site:
     [http://localhost/ADSSU Farmers Extension Services/](http://localhost/ADSSU%20Farmers%20Extension%20Services/)
   - **Login Link**: [http://localhost/ADSSU Farmers Extension Services/login.php](http://localhost/ADSSU%20Farmers%20Extension%20Services/login.php)
   - **Sign Up Link**: [http://localhost/ADSSU Farmers Extension Services/signup.php](http://localhost/ADSSU%20Farmers%20Extension%20Services/signup.php)

## Default Credentials
To access the system, use the following default credentials (injected via `setup.sql`):

- **Admin Dashboard**
  - **Username:** `admin`
  - **Email:** `admin@adssu.edu.ph`
  - **Password:** `admin123`

- **Extension Worker Portal**
  - **Username:** `worker`
  - **Email:** `worker@adssu.edu.ph`
  - **Password:** `admin123`

- **Farmer Portal**
  - **Username:** `farmer`
  - **Email:** `farmer@adssu.edu.ph`
  - **Password:** `admin123`

## Tech Stack
- **Backend:** PHP (PDO)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript, jQuery
- **UI Framework:** Bootstrap 5
- **Libraries:** SweetAlert2, Chart.js, FontAwesome

## Project Structure
- `/admin` - Admin dashboard and management pages
- `/extension-worker` - Extension worker modules and field visit logs
- `/farmer` - Farmer service history and training dashboard
- `/ajax` - Backend endpoints for asynchronous requests
- `/assets` - CSS, JS, and Images
- `/config` - Database configuration files
- `/database` - SQL schema and setup scripts
- `/includes` - Reusable layout components (Sidebar, Header, Footer) and Auth logic
