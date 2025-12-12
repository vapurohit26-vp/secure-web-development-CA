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




## 3. Functional Features

### User Management
- Admin: Add users, manage all tasks
- Employee: Manage own tasks only

### Task Operations
- Create tasks  
- Edit tasks  
- Delete tasks  
- Assign tasks  
- View dashboard list  

### Logging
- Login attempts  
- Task modifications  
- Security events  

---

## 4. Security Comparison

### 4.1 Password Security
| Insecure Version | Secure Version |
|------------------|----------------|
| Plaintext passwords | password_hash() used |
| Manual comparison | password_verify() |

---

### 4.2 SQL Injection
| Insecure | Secure |
|---------|--------|
| SQL queries use string interpolation | All queries use prepared statements |

---

### 4.3 Session Security
| Insecure | Secure |
|---------|--------|
| session_start() only | HttpOnly, SameSite, Secure flags |
| No timeout | Idle + absolute timeout |
| Vulnerable to fixation | session_regenerate_id(true) |

---

### 4.4 CSRF Protection
| Insecure | Secure |
|---------|--------|
| No CSRF protection | CSRF tokens implemented |

---

### 4.5 IDOR & RBAC
| Insecure | Secure |
|---------|--------|
| Employees can modify any task | Ownership checks enforced |
| Weak role handling | Proper RBAC middleware |

---

### 4.6 XSS Protection
| Insecure | Secure |
|---------|--------|
| Raw output rendered | htmlspecialchars() everywhere |

---

### 4.7 Logging
| Insecure | Secure |
|---------|--------|
| Minimal logging | Includes timestamp, IP, user agent |
| Basic events only | Full security audit logging |

---

## 5. Installation Instructions

### Step 1: Import Database


