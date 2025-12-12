# Task Manager Web Application  
Secure and Insecure Implementations 

## Table of Contents
1. Overview  
2. Repository Structure  
3. Functional Features  
4. Security Comparison  
   - 4.1 Password Security  
   - 4.2 SQL Injection  
   - 4.3 Session Security  
   - 4.4 CSRF Protection  
   - 4.5 IDOR & RBAC  
   - 4.6 XSS Protection  
   - 4.7 Logging  
5. Installation Instructions  
6. Default Credentials  
7. Testing Instructions  
8. Educational Purpose  
9. License  

---

## 1. Overview
This repository contains two complete versions of a PHP/MySQL-based Task Manager Web Application, created to demonstrate common security vulnerabilities found in older PHP systems and how these issues can be fixed through secure coding practices.

Two versions are included:

- **insecure/** — intentionally vulnerable  
- **secure/** — professionally refactored and fully secured  

Both systems support:

- Admin users (full control)
- Employee users (restricted task management)

---

## 2. Repository Structure

task-manager/
│
├── insecure/
│ ├── add_user.php
│ ├── auth_check.php
│ ├── create_admin.php
│ ├── dashboard.php
│ ├── db_connection.php
│ ├── insecure_session.php
│ ├── login.php
│ ├── logout.php
│ ├── security_log.php
│ └── sidebar.php
│
├── secure/
│ ├── add_user.php
│ ├── auth_check.php
│ ├── create_admin.php
│ ├── csrf.php
│ ├── dashboard.php
│ ├── db_connection.php
│ ├── login.php
│ ├── logout.php
│ ├── secure_session.php
│ ├── security_log.php
│ └── sidebar.php
│
├── database.sql
└── README.md
