# secure-web-development-CA
Task Manager Web Application – Secure & Insecure Versions
A Security-Focused Comparative Implementation Project
📌 Overview

This repository contains two complete implementations of a PHP/MySQL-based Task Manager Web Application:

insecure/ — the initial intentionally vulnerable version

secure/ — the fully refactored, security-hardened version following OWASP best practices

The system supports:

User authentication (admin + employee roles)

Task creation, updating, viewing, and deletion

Admin-only user creation

Activity logging

Session handling

The project demonstrates how insecure legacy-style PHP code can be redesigned into an enterprise-ready secure architecture, applying principles from the secure software development lifecycle (SSDLC).

📁 Repository Structure
/insecure
│
├── add_user.php            # Insecure user creation (SQLi + plaintext passwords)
├── auth_check.php          # Weak session validation
├── create_admin.php        # Hardcoded admin credentials
├── dashboard.php           # SQL injection, IDOR, no CSRF
├── db_connection.php       # No error handling, no security flags
├── insecure_session.php    # session_start() only — no security config
├── login.php               # SQL injection, no hashing
├── logout.php              # Basic session destroy
├── security_log.php        # Minimal logging
└── sidebar.php

/secure
│
├── add_user.php            # Hashed passwords, validation, CSRF, RBAC, logging
├── auth_check.php          # Enforced login + session security
├── create_admin.php        # Secure admin creation using hashed password
├── csrf.php                # Token generation + verification
├── dashboard.php           # Prepared statements, RBAC, IDOR protection, CSRF
├── db_connection.php       # Secure PDO with exception mode + UTF-8
├── login.php               # Secure login with password_verify + CSRF
├── logout.php              # Safe cookie destruction + session cleanup
├── secure_session.php      # Hardened session configuration
├── security_log.php        # Log includes timestamp, IP, user agent
└── sidebar.php

database.sql
README.md

✨ Features
User Roles

Admin

Can create users

Can manage ALL tasks

Employee

Can manage ONLY their own tasks

Task Management

Create tasks

Edit tasks

Delete tasks

Assign tasks to users

View tasks in dashboard

Security Logging

Login attempts

CSRF failures

Unauthorized access attempts

Task creation/update/deletion

🔐 Security Improvements (Secure Version)

The secure folder implements full protection against the vulnerabilities present in the insecure version.

1. SQL Injection Protection

✔ All raw SQL replaced with prepared statements ($stmt = $conn->prepare(...))
✔ No user input is concatenated into SQL queries

2. Password Security

✔ password_hash() for storing passwords
✔ password_verify() for login
✔ Strong password rules enforced in add_user.php

3. Session Hardening

✔ Strict mode
✔ SameSite=Strict cookies
✔ HttpOnly enabled
✔ Optional Secure flag for HTTPS
✔ Session fixation protection (session_regenerate_id(true))
✔ Idle timeout + absolute session lifetime

4. CSRF Protection

✔ CSRF token generation (get_csrf_token())
✔ CSRF validation (verify_csrf_token())
✔ Required for all POST actions, including:

login

create task

update task

delete task

add user

5. Role-Based Access Control (RBAC)

✔ Admin-only routes enforced
✔ Employees cannot:

Create users

Edit tasks not assigned to them

Delete tasks not assigned to them

6. IDOR Protection

✔ Authorization checks ensure users cannot access another user’s tasks
✔ Forced ownership validation using prepared statements

7. XSS Protection

✔ All output escaped with htmlspecialchars()

8. Secure Logging

✔ Logs include:

Timestamp

IP address

User agent
✔ Sensitive errors not shown to user

⚠️ Vulnerabilities in the Insecure Version
File	Vulnerability
login.php	SQL Injection, plaintext passwords, no CSRF
add_user.php	SQL Injection, plaintext storage, no validation
dashboard.php	SQL Injection, IDOR, no CSRF, no sanitisation
auth_check.php	Weak session handling
insecure_session.php	No secure flags, fixation allowed
create_admin.php	Hardcoded admin password
db_connection.php	No error hardening or secure config

These insecure behaviours are intentionally preserved to demonstrate common real-world vulnerabilities.

🚀 Installation (Secure Version)
1. Import Database
mysql -u root -p < database.sql

2. Configure DB Connection

Edit:

secure/db_connection.php

3. Run the Application

Using PHP built-in server:

cd secure
php -S localhost:8000


Navigate to:

http://localhost:8000/login.php

🔑 Default Credentials (Secure Version)
Role	Username	Password
Admin	admin	Myname@2010
Employee	Veena	Veena@123 (if seeded)
🧪 Testing Checklist
Functional Tests

Login/logout

Create user (admin only)

Create/update/delete task

Employee vs admin permissions

Security Tests

SQL Injection attempts

CSRF attack simulation

Session hijacking tests

IDOR tests by changing task IDs

XSS test payloads