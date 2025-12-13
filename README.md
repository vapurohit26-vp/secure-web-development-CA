# Secure Task Manager Web Application  


## Project Overview

This project is a PHP-based Task Manager web application implemented in **two versions**:  
- an **insecure** version demonstrating common web vulnerabilities, and  
- a **secure** version showing how to fix them using modern best practices.

Both versions provide basic task management (users, authentication, task CRUD), but the primary purpose of the repository is to **teach and demonstrate secure coding in PHP** (SQL injection prevention, secure sessions, CSRF protection, XSS mitigation, and proper access control).

---

## Features and Security Objectives

### Core Application Features

- User authentication (login/logout)
- Admin user creation and user management
- Task creation, editing, assignment, and deletion
- Role-based access:
  - **Admin:** manage users and all tasks
  - **Employee:** manage only own tasks
- Security and activity logging for important actions

### Security Objectives

- Prevent **SQL Injection** by replacing string-concatenated queries with prepared statements.
- Protect **passwords** using hashing (`password_hash`, `password_verify`), avoiding plaintext storage.
- Enforce **CSRF protection** using server-side tokens for all state-changing POST requests.
- Harden **sessions** with secure cookie flags and idle/session lifetime limits.
- Mitigate **XSS** via consistent output escaping (`htmlspecialchars`).
- Apply **least privilege** and **role-based authorization** to sensitive operations (user management, task ownership).

---

## Project Structure

At the top level, the repository contains two main folders:
project-root/
├── insecure/ # Original, vulnerable implementation
└── secure/ # Refactored, hardened implementation


### `insecure/` folder

Contains the original vulnerable codebase:

- `add_user.php`  
  - Adds users with **plaintext passwords** and **SQL queries built via string concatenation** (vulnerable to SQL injection).
- `auth_check.php`  
  - Simple session check using `insecure_session.php`, no session hardening.
- `create_admin.php`  
  - Inserts an admin with a hard-coded plaintext password.
- `dashboard.php`  
  - Performs task CRUD with unsanitized GET/POST parameters directly in SQL.
- `db_connection.php`  
  - Basic PDO connection, no further security.
- `insecure_session.php`  
  - Only `session_start()`, no cookie flags or timeout handling.
- `login.php`  
  - Authenticates with plaintext password comparison and vulnerable SQL.
- `logout.php`  
  - Destroys session but without secure cookie cleanup.
- `security_log.php`  
  - Logs basic messages to `logs/security.log` (less contextual data).
- `sidebar.php`  
  - Echoes session values without HTML escaping (XSS risk).

This folder is useful for learning how common mistakes lead to SQL injection, CSRF, XSS, and weak session handling.

### `secure/` folder

Contains the refactored, secure implementation:

- `add_user.php`  
  - Admin-only user creation.  
  - Validates role input, enforces password complexity, hashes passwords, uses prepared statements, includes CSRF checks, and logs user creation events.
- `auth_check.php`  
  - Uses `secure_session.php` and enforces authenticated access with clean redirects.
- `create_admin.php`  
  - Creates a default admin using `password_hash()` and prepared statements.
- `csrf.php`  
  - Generates strong random CSRF tokens with `random_bytes` and verifies them against session data.
- `dashboard.php`  
  - Uses prepared statements for all task operations.  
  - Verifies CSRF token for every POST.  
  - Enforces per-role rules (employees can only manage their own tasks).  
  - Escapes all dynamic output to prevent XSS.
- `db_connection.php`  
  - PDO connection with error mode set to exceptions and UTF‑8 charset.
- `login.php`  
  - Verifies CSRF token, uses prepared statements, and checks hashed passwords using `password_verify`.  
  - Regenerates the session ID on successful login to avoid fixation.
- `logout.php`  
  - Properly clears `$_SESSION`, deletes the session cookie with secure parameters, and calls `session_destroy()`.
- `secure_section.php` (referred to as `secure_session.php` in includes)  
  - Configures session cookie attributes (`HttpOnly`, `SameSite`, `Secure` when HTTPS) before `session_start()`.  
  - Implements inactivity timeout and maximum session lifetime, forcing re-login when exceeded.
- `security_log.php`  
  - Logs security events with timestamp, IP address, and User-Agent for auditing.[web:6]
- `sidebar.php`  
  - Prints user data using `htmlspecialchars()` to prevent XSS.

#### Logs

Both versions write logs under:

logs/
└── security.log # Security-related events (more detailed in secure version)


---

## Setup and Installation

The steps are the same for both versions, but **only the `secure/` version should be used in real environments**.

### 1. Requirements

- PHP 8.x (recommended)
- MySQL/MariaDB
- Web server (Apache, Nginx, etc.)
- PHP extensions: `pdo_mysql`, `openssl` (for secure random CSRF tokens)

### 2. Database Creation

CREATE DATABASE task_management_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

### 3. Tables

Use the same schema for both `insecure` and `secure` apps:

CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
full_name VARCHAR(100) NOT NULL,
username VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
role ENUM('admin','employee') DEFAULT 'employee',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
description TEXT,
assigned_to INT,
due_date DATE,
status ENUM('pending','in_progress','completed') DEFAULT 'pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);



### 4. Configure Database Credentials

Edit `db_connection.php` in each folder :

$host = "localhost";
$db_name = "task_management_db";
$username = "root";
$password = "";


### 5. Install and Run

- **Insecure version (for study only):**

  Place the `insecure/` folder in your web root and open:

http://localhost/insecure/create_admin.php
http://localhost/insecure/login.php


- **Secure version :**

Place the `secure/` folder in your web root and open:

http://localhost/secure/create_admin.php
http://localhost/secure/login.php


The admin user is created by `create_admin.php` (same username/password in both versions, but stored differently: plaintext vs hash).

---

## Usage Guidelines

### Insecure Version

- Login at `/insecure/login.php` using the created admin credentials.
- Use the dashboard to create, update, and delete tasks.
- Use `add_user.php` to add new users.
- This version is intentionally vulnerable (no CSRF, no hashing, direct SQL from inputs).

Use only on a local, isolated environment for demonstration.

### Secure Version

- Login at `/secure/login.php`.
- Admins can:
- Add new users via `add_user.php` (password rules enforced, roles restricted).
- Manage all tasks on `dashboard.php`.
- Employees can:
- Create and manage only their own tasks.
- All POST forms include a CSRF hidden field; a missing or invalid token results in a 400 response and is logged.
- Sessions expire after inactivity or lifetime limits, requiring re-authentication.

---

## Security Improvements (Insecure → Secure)

| Aspect             | Insecure Folder                                      | Secure Folder                                                  |
|--------------------|------------------------------------------------------|----------------------------------------------------------------|
| SQL Queries        | String concatenation with user input (injectable)    | Prepared statements with bound parameters                      |
| Password Storage   | Plaintext in DB                                      | Hashed passwords (`password_hash`, `password_verify`)          |
| CSRF Protection    | None                                                 | CSRF tokens for all POST actions (`csrf.php`)                  |
| Session Management | Basic `session_start()` only                         | HttpOnly, SameSite, optional Secure flag, timeouts, regeneration[web:1][web:7][web:10] |
| XSS Mitigation     | Raw echo of user data                                | `htmlspecialchars` on all user-controlled output               |
| Authorization      | Weak role checks, insecure task ownership handling   | Enforced role checks and ownership verification                |
| Logging            | Simple textual logs                                  | Detailed logs (IP, UA, timestamp, action)                      |
| Input Validation   | Minimal or none                                      | Trimming, validation, password complexity rules                |

These changes follow OWASP secure coding guidance: parameterized queries, output encoding, CSRF tokens, and hardened session management.[web:3][web:6][web:8]

---

## Testing Process

### Insecure Version (Baseline)

- Manually attempted:
- SQL injection via login and task parameters (succeeds, illustrating the vulnerability).
- XSS via task titles and descriptions (renders unescaped).
- CSRF by submitting forged forms from another site (actions succeed).

### Secure Version

- **Manual testing:**
- Same SQL injection payloads fail because queries use prepared statements.
- XSS payloads display as text due to `htmlspecialchars`.
- Cross-site POSTs without a valid CSRF token are rejected with 400 responses.
- Employees cannot edit or delete tasks they do not own.

- **Tool-assisted checks (suggested workflow):**
- Use **OWASP ZAP** or similar scanners for injection and XSS tests.
- Confirm cookie flags (`HttpOnly`, `Secure`, `SameSite`) with browser dev tools.
- Optionally run static analysis tools (e.g., PHPStan, Psalm) for code issues.

Key findings: the secure version addresses the main OWASP Top 10 issues that were present in the insecure version (injection, broken auth/session, XSS, CSRF).

---

## Contributions and References

### Original Source and Intent

- The **`insecure/`** codebase represents a typical beginner-level PHP application with common security mistakes.
- The **`secure/`** codebase is a refactor intended for **security education and comparison**, not as a production-ready framework.


**Recommended Tools:**
- OWASP ZAP / Burp Suite for automated scanning
- Browser DevTools for cookie flag verification
- PHPStan for static analysis

---

## 🤝 Contributions and References

### Key Security References

**Core Standards:**
- OWASP (2021) *OWASP Top Ten Web Application Security Risks*
- OWASP (2020a) *Cross-Site Request Forgery Prevention Cheat Sheet*
- OWASP (2020b) *Session Management Cheat Sheet*

**Technical Foundation:**
- Barth, A. (2011) *HTTP State Management Mechanism* (RFC 6265)
- Barth, A., Jackson, C. & Mitchell, J.C. (2008) *Robust defenses for CSRF*
- PHP Group (2023) *password_hash() Documentation*

**Academic Research:**
- Bonneau, J. (2012) *Password guessing analysis* (70M passwords)
- Halfond, W.G. et al. (2006) *SQL injection classification*
- Ferraiolo, D. et al. (2001) *NIST RBAC standard*
- Gharib, M. & Jebril, N. (2014) *Secure session management*

**Additional Reading:**
- Dahm, M. (2020) *PHP Security Best Practices*
- Howard, M. & Lipner, S. (2006) *Security Development Lifecycle*
- McGraw, G. (2006) *Software security principles*

### License
Educational use only.

---





---
