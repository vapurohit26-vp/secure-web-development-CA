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
├── insecure/                      # Intentionally vulnerable implementation
│   ├── add_user.php               # Plaintext passwords, SQL injection
│   ├── auth_check.php             # Weak session validation
│   ├── create_admin.php           # Hardcoded admin credentials
│   ├── dashboard.php              # SQLi, IDOR, no CSRF, no sanitization
│   ├── db_connection.php          # Weak PDO config
│   ├── insecure_session.php       # No secure session flags
│   ├── login.php                  # SQLi, plaintext passwords
│   ├── logout.php                 # Basic session destroy
│   ├── security_log.php           # Minimal logging
│   └── sidebar.php
│
├── secure/                        # Security-hardened implementation
│   ├── add_user.php               # CSRF, hashing, validation, RBAC
│   ├── auth_check.php             # Secure session enforcement
│   ├── create_admin.php           # Hashed admin password
│   ├── csrf.php                   # Token generation + validation
│   ├── dashboard.php              # Prepared queries, IDOR protection
│   ├── db_connection.php          # Secure PDO + UTF-8 handling
│   ├── login.php                  # Hash verify, CSRF, session hardening
│   ├── logout.php                 # Secure session destruction
│   ├── secure_session.php         # Strict secure session configuration
│   ├── security_log.php           # Logs with timestamp, IP, user agent
│   └── sidebar.php
│
├── database.sql
└── README.md

