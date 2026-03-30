# ADBU Academic Calendar

A web-based Academic Calendar Management System built with PHP and MySQL. Students can view upcoming academic events, holidays, and examinations, while administrators can manage the calendar through a secure admin panel.

---

## Features

- Students can browse events organized by month with a visual calendar
- Events are color-coded by type: Academic, Exam, and Holiday
- Secure login-protected admin panel to manage all events
- Admins can Add, Edit, and Delete calendar events
- Search events by title from the admin dashboard
- Auto-logout after 15 minutes of inactivity

---

## Video Demo

Watch the full installation walkthrough and project demo here:
YOUR_YOUTUBE_LINK_HERE

---

## Prerequisites

Make sure the following software is installed before proceeding:

- XAMPP 8.x or later (includes Apache, PHP, and MySQL) — WAMPP also works
- PHP 8.0 or later (bundled with XAMPP/WAMPP)
- MySQL 8.0 or later (bundled with XAMPP/WAMPP)
- Any modern web browser (Chrome, Firefox, Edge)
- Git (for cloning the repository)

---

## Installation Steps

### Step 1 - Clone the Repository

Open a terminal and run:

```
git clone https://github.com/Tonie-K/Academic_Calendar
```

### Step 2 - Move the Project to Your Server Root

XAMPP users: Copy the cloned `academic` folder into:
```
C:/xampp/htdocs/
```

WAMPP users: Copy the cloned `academic` folder into:
```
C:/wamp64/www/
```

Your final path should look like: `htdocs/academic/` or `www/academic/`

### Step 3 - Start Apache and MySQL

1. Open the XAMPP Control Panel (or WAMPP manager)
2. Click Start next to Apache
3. Click Start next to MySQL

### Step 4 - Set Up the Database

1. Open your browser and go to: http://localhost/phpmyadmin
2. Click New in the left sidebar to create a new database
3. Name it `academic_calendar` and click Create
4. Select the `academic_calendar` database
5. Click the Import tab
6. Click Choose File and select `academic_calendar.sql` from the root of the project folder
7. Click Import — the tables and sample data will be loaded automatically

### Step 5 - Configure the Database Connection

Open `academic/config.php` and verify the credentials match your local setup:

```php
$host = "localhost";
$user = "root";
$pass = "";   // Leave blank if you have not set a MySQL root password
$db   = "academic_calendar";
```

If you have set a MySQL password, replace the empty string with your password.

### Step 6 - Run the Project

Open your browser and navigate to:

```
http://localhost/academic/index.php
```

You should now see the public-facing Academic Calendar.

---

## Admin Panel Access

- URL: http://localhost/academic/login.php
- Username: admin
- Password: admin123

Note: Change the default credentials after your first login.

From the admin dashboard you can:

- View all events with upcoming and past counts
- Search events by title
- Add new events (title, type, date, description)
- Edit existing events
- Delete events

---

## Project Structure

```
academic/
├── config.php              - Database connection and session config
├── index.php               - Public calendar view (homepage)
├── login.php               - Admin login page
├── logout.php              - Session destroy and redirect
├── css/
│   └── style.css           - Global styles
├── admin/
│   ├── dashboard.php       - Admin event management dashboard
│   ├── add_event.php       - Add new event form
│   ├── edit_event.php      - Edit existing event form
│   └── delete_event.php    - Delete event handler
└── database/
    └── database.sql        - Minimal schema

academic_calendar.sql       - Full database dump with sample data (use this for import)
```

---

## Database Schema

### admins table

| Column   | Type         | Description            |
|----------|--------------|------------------------|
| id       | INT          | Auto-increment ID      |
| username | VARCHAR(50)  | Admin username         |
| password | VARCHAR(255) | Bcrypt-hashed password |

### events table

| Column      | Type         | Description                          |
|-------------|--------------|--------------------------------------|
| id          | INT          | Auto-increment ID                    |
| title       | VARCHAR(200) | Event title                          |
| type        | VARCHAR(50)  | Category: Academic / Exam / Holiday  |
| event_date  | DATE         | Date of the event                    |
| description | TEXT         | Optional event description           |

---

## Built With

- PHP 8
- MySQL 8
- Apache (via XAMPP/WAMPP)
- HTML5 / CSS3
- JavaScript

---

## License

This project was created for academic submission purposes.
