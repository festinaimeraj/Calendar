# Calendar Leave Management System

This web application is a Calendar Leave Management System developed with Laravel. It provides user roles (Admin and Employee), leave request and management functionalities, and a calendar interface to view and manage leave requests.

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

## Admin Calendar View

The admin can view and edit all employee leaves on a calendar, with different colors representing different leave types (e.g., "Flex" leave in green, "Medical" leave in orange).

![admin_calendar](https://github.com/user-attachments/assets/feade1b8-5ccf-4711-a1a5-c57fcec37b04)


## Edit Leave Request

![edit_request](https://github.com/user-attachments/assets/48288037-00a1-4648-98a3-f41a900e4a9c)

## Manage employees

![employees](https://github.com/user-attachments/assets/4213da09-2ed3-4cb5-b8db-1d2230e1029a)

## Request a leave

![request_leave](https://github.com/user-attachments/assets/b37c0a10-c506-4a4e-adc5-111bf7ab41d3)

## Approve or deny the leave requests

![approve](https://github.com/user-attachments/assets/74bad4af-2272-44ba-857e-05f23205900d)

## Leave report for each employee

![report](https://github.com/user-attachments/assets/9985f399-43c4-4ed9-b1b9-c4fe637eb3d6)

## Leave types

![leave_types](https://github.com/user-attachments/assets/f067587d-cd1a-43e7-b665-0b4ce7af14ed)


## Employee Calendar View

![employee](https://github.com/user-attachments/assets/57b44ad8-6682-4e36-8e84-c89854ecc9ff)

## Request a leave

![request_leave](https://github.com/user-attachments/assets/0231419a-a818-4cc9-9982-28e4476dca5c)

## Employee's leave totals

![leave_totals](https://github.com/user-attachments/assets/7ad71254-0fc3-4662-a198-2086b799e18e)

## Edit my pending requests

![image](https://github.com/user-attachments/assets/94475680-ef04-4c01-ac0b-6a2b19f4a9f2)


**Also, it has a mail functionality:**
- When the employee requests a leave, an email goes to admin.
- When the admin approves/denies a leave request, an email is sent to employee.

![image](https://github.com/user-attachments/assets/e5c6e0a0-05c3-41c7-9875-9cfa15c1c054)

![image](https://github.com/user-attachments/assets/bda04f43-0360-4338-adc5-6861176c00d9)


