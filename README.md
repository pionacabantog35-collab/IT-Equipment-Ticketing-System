**IT Equipment & Ticketing System
**
A role-based web application for managing IT equipment and support tickets at Arellano University. Built to streamline how equipment issues are reported, assigned, resolved, and tracked across three distinct user roles.

**Overview**

This system replaces manual/ad-hoc equipment issue reporting with a structured ticketing workflow. Staff can report problems with equipment, technical personnel handle repairs and updates, and administrators oversee accounts, equipment inventory, and ticket routing.

**User Roles & Permissions**
🧑‍💼 Staff
Submit tickets to report issues with equipment
View equipment records
🔧 Technical
Add, update, and delete equipment records
Receive assigned tickets
Mark tickets as completed once resolved
View full ticket details
🛡️ Administrator
Create, update, and delete user accounts
Add, update, and delete equipment records
View full ticket details
Assign tickets to the appropriate technical staff
Approve and delete tickets
Features
Role-based authentication and access control
Equipment inventory management
Ticket creation, assignment, and status tracking (open → assigned → completed)
Admin-level oversight across accounts, equipment, and tickets
Tech Stack
Backend: PHP
Database: MySQL
Frontend: HTML, CSS, JavaScript
Server environment: XAMPP (Apache + MySQL)
Getting Started
Prerequisites
XAMPP (or any Apache + MySQL + PHP stack)
A web browser
Installation
Clone the repository into your XAMPP htdocs folder:
bash
   git clone https://github.com/your-username/your-repo-name.git
Start Apache and MySQL from the XAMPP control panel.
Open phpMyAdmin (http://localhost/phpmyadmin) and create a new database.
Import the provided .sql file (if included) to set up the schema.
Update config.php with your database credentials:
php
   define('DB_SERVER','127.0.0.1');
   define('DB_USERNAME','your_username');
   define('DB_PASSWORD', 'your_password');
   define('DB_NAME','your_database_name');
Navigate to the project folder in your browser, e.g.:
   http://localhost/your-project-folder/
Project Status

🚧 In development — built as a final exam project for a systems course at Arellano University.

License

This project is for academic purposes. Feel free to fork and adapt for your own coursework or learning.

Built by Pyo
