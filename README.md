# Hostel Management System (HMS)

A centralized, web-based **Hostel Management System** developed as part of the **Database Management Systems (CSBB 204)** course at **National Institute of Technology Delhi**.  
The system digitizes and automates hostel administration tasks, ensuring efficiency, transparency, and role-based access control.

---

## Project Overview

Traditional hostel management relies heavily on manual records, which often leads to delays, redundancy, and data inconsistency.  
The Hostel Management System (HMS) replaces these processes with a **single, centralized digital platform** that supports multiple user roles:

- **Admin** – global hostel oversight and reporting  
- **Warden** – hostel-level management  
- **Student** – self-service access to personal and hostel information  

The system supports student records, room allocation, attendance, complaints, notices, fee tracking, and outing management.

---

## Key Features

### Role-Based Access
- Secure login for **Admin, Warden, and Student**
- Session-based authentication using PHP

### Database-Driven Modules
- Student & warden management  
- Hostel and room allocation  
- Attendance tracking  
- Complaint management and resolution  
- Fee records and payment status  
- Notices and announcements  
- Outing (home entry/exit) records  

### Advanced DBMS Concepts
- Normalized relational schema  
- Primary & foreign key constraints  
- Triggers for:
  - Automatic age calculation
  - Room occupancy updates on student insert/update/delete  
- Complex SQL queries using `JOIN`, `GROUP BY`, subqueries, and aggregation  

---

## Technology Stack

| Layer | Technology |
|------|------------|
| Frontend | HTML, CSS, JavaScript |
| Backend | PHP (Core PHP, no framework) |
| Database | MySQL |
| Server | WAMP (Apache) |
| Tools | VS Code, MySQL Workbench |
| Version Control | Git & GitHub |

---

## Project Structure

<pre>
Hostel-Management-System/
│
├── *.php # Core application logic
├── css/ # Stylesheets
├── js/ # Client-side scripts
├── database/
│ └── hostel_management.sql
├── connect.sample.php
├── README.md
└── .gitignore
</pre>

<p> A flat PHP file structure is used to keep routing simple in a non-framework environment.</p>

---

## Database Design

- Designed using **ER modeling** and mapped to a **relational schema**
- Includes entities such as:
  - Admin, Student, Warden
  - Hostel, Room
  - Attendance, Complaint, Fee
  - Notice, Home_Entry
- Enforces data integrity using foreign keys and triggers

The complete schema and sample data are available in:
database/hostel_management.sql

---

## Installation & Setup

### Prerequisites
- Windows OS
- WAMP Server
- PHP 7.4+
- MySQL
- Git

### Steps

1. Clone the repository:
```bash
git clone https://github.com/MishtiGupta-10/Hostel-Management-System.git
```

2. Move the project to
C:\wamp64\www\

3. Database setup:
- Open phpMyAdmin
- Create database HMS
- Import database/hostel_management.sql

4. Configure database connection:
- Rename connect.sample.php to connect.php
- Update MySql credentials

5. Run the application:
http://localhost/Hostel-Management-System

---

## Security

- Sensitive credentials excluded using .gitignore
- Passwords stored using hashing
- Role-based authorization enforced throughout the system

---

## Project Team 

- **Mishti Gupta** - Backend development, database design, Github integeration
- **Krrish Kumar, Maurya Pratham** - Module integeration, testing, frotend development, ui styling and documentation.

---

## Learning Outcomes 

Through this project, we gained hands-on experience with: 
- ER modeling and relational database design
- Writing optimized SQL queries
- Implementing triggers and constraints
- PHP–MySQL integration
- Building a complete database-driven web application

---

## References 

- Fundamentals of Database Systems – Elmasri & Navathe
- Database System Concepts – Korth
- phpMyAdmin Documentation
- W3Schools (PHP & SQL)

---

## Project Significance 

This project demonstrates the practical application of DBMS theory to solve real administrative problems and reflects strong fundamentals in **database design, backend development, and team collaboration**.








