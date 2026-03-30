[README.md](https://github.com/user-attachments/files/26358742/README.md)
# ADBU Academic Calendar
A web-based Academic Calendar Management System built with PHP and MySQL. It allows students to view upcoming academic events, holidays, and examinations, while administrators can manage the calendar through a secure admin panel.

# Features

Public Calendar View — Students can browse events organized by month with a visual calendar
Event Categories — Events are color-coded by type: Academic, Exam, and Holiday
Admin Dashboard — Secure login-protected panel to manage all events
CRUD Operations — Admins can Add, Edit, and Delete calendar events
Search — Search events by title from the admin dashboard
Session Management — Auto-logout after 15 minutes of inactivity


# Video Demo

Watch the full installation walkthrough and project demo here:
Show Image

(Replace the link above with your actual YouTube video URL)

# Prerequisites
Make sure the following software is installed on your machine before proceeding:
ToolVersionNotesXAMPP8.x or laterIncludes Apache, PHP, and MySQL (WAMPP also works)PHP8.0 or laterBundled with XAMPP/WAMPPMySQL8.0 or laterBundled with XAMPP/WAMPPWeb BrowserAny modern browserChrome, Firefox, Edge, etc.GitAny recent versionFor cloning the repository

Note: This project uses XAMPP/WAMPP as the local server environment. If you're using WAMPP, your www root directory will be used instead of htdocs.


# Installation Steps
Follow these steps to get the project running on your local machine.
Step 1 — Clone the Repository
Open a terminal and run: git clone https://github.com/Tonie-K/Academic_Calendar

Step 2 — Move the Project to Your Server Root
XAMPP users: Copy the cloned academic folder into:

  C:/xampp/htdocs/

WAMPP users: Copy the cloned academic folder into:

  C:/wamp64/www/
Your final path should look like: htdocs/academic/ or www/academic/

Step 3 — Start Apache and MySQL
Open the XAMPP Control Panel (or WAMPP manager)
Click Start next to Apache
Click Start next to MySQL

Step 4 — Set Up the Database
Open your browser and go to: http://localhost/phpmyadmin
Click New in the left sidebar to create a new database
Name it academic_calendar and click Create
Select the academic_calendar database
Click the Import tab
Click Choose File and select the SQL file from the project:

   academic_calendar.sql
(This file is located in the root of the project folder)
Click Import — the tables and sample data will be loaded automatically

Step 5 — Configure the Database Connection
Open academic/config.php and verify the credentials match your local setup:
php$host = "localhost";
$user = "root";
$pass = "";           // Leave blank if you haven't set a MySQL root password
$db   = "academic_calendar";

If you have set a MySQL password, replace the empty string with your password.

Step 6 — Run the Project
Open your browser and navigate to:
http://localhost/academic/
You should see the public-facing Academic Calendar. 🎉

# Admin Panel Access
To manage events, log in to the admin panel:

URL: http://localhost/academic/login.php
Username: admin
Password: admin123


# Important: Change the default admin credentials after your first login, especially before deploying to a live server.

From the admin dashboard you can:

View all events with upcoming/past counts
Search events by title
Add new events (title, type, date, description)
Edit existing events
Delete events


# Project Structure
academic/
├── config.php              # Database connection & session config
├── index.php               # Public calendar view (homepage)
├── login.php               # Admin login page
├── logout.php              # Session destroy & redirect
├── css/
│   └── style.css           # Global styles
├── admin/
│   ├── dashboard.php       # Admin event management dashboard
│   ├── add_event.php       # Add new event form
│   ├── edit_event.php      # Edit existing event form
│   └── delete_event.php    # Delete event handler
└── database/
    └── database.sql        # Minimal schema (use academic_calendar.sql for full import)

academic_calendar.sql       # Full database dump with sample data (use this for import)

# Database Schema
Table: admins
ColumnTypeDescriptionidINT (PK)Auto-increment IDusernameVARCHAR(50)Admin usernamepasswordVARCHAR(255)Bcrypt-hashed password
Table: events
ColumnTypeDescriptionidINT (PK)Auto-increment IDtitleVARCHAR(200)Event titletypeVARCHAR(50)Category: Academic / Exam / Holidayevent_dateDATEDate of the eventdescriptionTEXTOptional event description

# Built With

PHP 8 — Server-side scripting
MySQL 8 — Relational database
Apache — Local web server (via XAMPP/WAMPP)
HTML5 / CSS3 — Frontend structure and styling
JavaScript — Calendar interactivity on the public view


# Notes

Password hashing uses PHP's password_hash() / password_verify() — the default admin password is stored as a bcrypt hash.
Sessions expire after 15 minutes of inactivity for security.
The .gitignore in this repository excludes common unnecessary files. Ensure you do not commit any .env files or sensitive credentials.


# License
This project was created for academic submission purposes.
