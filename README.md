# Secure Task Manager Web Application  
A Security-Focused Redesign of an Insecure PHP Task Management System

---

## 📑 Table of Contents
1. [Project Overview](#project-overview)  
2. [Features and Security Objectives](#features-and-security-objectives)  
3. [Project Structure](#project-structure)  
4. [Setup & Installation Instructions](#setup--installation-instructions)  
5. [Usage Guidelines](#usage-guidelines)  
6. [Security Improvements](#security-improvements)  
7. [Testing Process](#testing-process)  
8. [Contributions & References](#contributions--references)

---

## 📌 Project Overview

The **Secure Task Manager Web Application** is a full PHP-based system that demonstrates how an insecure application can be transformed into a secure, industry-standard web application.

The project was developed in **two phases**:

### 🔴 **Insecure Implementation**
Includes deliberate vulnerabilities:
- Plaintext passwords  
- SQL injection  
- Broken authentication  
- No CSRF protection  
- No session hardening  
- IDOR vulnerabilities  
- Missing validation and sanitisation  

### 🟢 **Secure Implementation**
Refactored using OWASP-aligned best practices:
- Password hashing (bcrypt)  
- Prepared SQL statements  
- Secure session configuration (HttpOnly, SameSite, regeneration)  
- Role-Based Access Control  
- CSRF protection  
- Input validation  
- XSS protection  
- Detailed security logging  

The web application supports two roles:

- **Admin** – Add users, edit/delete/view all tasks  
- **Employee** – Create, update, and delete only their own tasks  

This repository serves as a **teaching and demonstration tool** showcasing secure vs insecure software engineering.

---

## ✨ Features and Security Objectives

### Functional Features
- User login and authentication  
- Admin user creation  
- Create, update, and delete tasks  
- Assign tasks to other users (admin only)  
- Employee access to their own tasks  
- Activity logging  

### Security Objectives
- Prevent SQL Injection through parameterised queries  
- Store passwords securely using bcrypt  
- Mitigate CSRF attacks using unique tokens  
- Enforce strict session security policy  
- Prevent IDOR by checking ownership  
- Prevent XSS using `htmlspecialchars()`  
- Apply principle of least privilege (RBAC)  
- Maintain audit trail for accountability  

---

## 📁 Project Structure

```text
task-manager/
|
+-- insecure/                      # Intentionally vulnerable implementation
|   +-- add_user.php               # SQLi, plaintext passwords
|   +-- auth_check.php             # Weak session check
|   +-- create_admin.php           # Hardcoded admin
|   +-- dashboard.php              # SQLi, IDOR, no CSRF
|   +-- db_connection.php          # Weak PDO config
|   +-- insecure_session.php       # No security flags
|   +-- login.php                  # SQLi login, plaintext password check
|   +-- logout.php                 # Basic session destroy
|   +-- security_log.php           # Minimal log entries
|   +-- sidebar.php
|
+-- secure/                        # Hardened, secure implementation
|   +-- add_user.php               # CSRF, hashing, validation, RBAC
|   +-- auth_check.php             # Secure session validation
|   +-- create_admin.php           # Hashed admin password
|   +-- csrf.php                   # CSRF token logic
|   +-- dashboard.php              # Prepared statements + IDOR protection
|   +-- db_connection.php          # Secure PDO config
|   +-- login.php                  # Hash verify, log failures, regenerate session
|   +-- logout.php                 # Secure session destruction
|   +-- secure_session.php         # Strict session settings
|   +-- security_log.php           # Detailed logs: IP, UA, timestamp
|   +-- sidebar.php
|
+-- database.sql                   # Database schema
+-- README.md                      # Documentation
