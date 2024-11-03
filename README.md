# Calendar Leave Management System

This web application is a Calendar Leave Management System developed with Laravel and FullCalendar. It provides user roles (Admin and Employee), leave request and management functionalities, and a calendar interface to view and manage leave requests.

## Features

### Admin Role
- **Login & Authentication**: Admins can log in securely using their email and password.
- **Dashboard**: Admins have a calendar view showing all employee leave requests.
- **Manage Leave Requests**: Admins can approve or deny leave requests, specifying a reason if denying.
- **Employee Management**: Admins can view all employees and add, edit, or delete employee information.
- **Leave Types Management**: Admins can manage leave types that employees can choose from when requesting leave.
- **Reports**: Admins can view reports detailing leave usage per employee, filtering by name and leave type.
- **Calendar View**: Admins can see the leave requests displayed on a calendar or edit them.

### Employee Role
- **Login & Authentication**: Employees log in with email and password to access their personal dashboard.
- **Request Leave**: Employees can request leave by specifying dates, leave type, and reason.
- **View Leave Status**: Employees can view the status of their leave requests.
- **Calendar View**: Employees see their own approved leave requests displayed on a calendar.
- **Notifications**: Employees receive notifications when their leave requests are approved or denied.

## Setup Instructions

### Prerequisites
- **PHP**: ^7.4|^8.0
- **Laravel**: ^8.0 or higher
- **Composer**: Latest version
- **MySQL**: For database storage
- **Node.js & NPM**: For handling frontend assets and dependencies

### Installation

1. **Clone the Repository**
2. **Install Dependencies**

   composer install    
   npm install
4. **Environment Setup**

   Copy the .env.example file to .env
- **Set up the database connection details in .env:**
  
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password
 - **Generate application key:**

    php artisan key:generate
 - **Database Migration & Seeding**

    php artisan migrate --seed
 - **Run the Server**
   
    php artisan serve
    npm run dev
- **Usage**
- Access the Application: Open http://127.0.0.1:8000 in your browser.
- Login as Admin or Employee: Use the credentials set during seeding or registration.





