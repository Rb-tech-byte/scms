# Short Course Management System (SCMS)

Online Course Registration & Management System for the **Institute of Education and Management Technologies (IEMT) — Open University of Tanzania**.

---

## 🚀 Overview

The **Short Course Management System (SCMS)** is an enterprise academic administration portal built on Laravel. It streamlines applicant self-registration, course management, multi-branch operations, multi-installment tuition payment schedules, financial invoice tracking, attendance tracking, and completion certification.

---

## 🔑 Your Compliant Credentials

Use these test accounts to verify user acceptance across roles:

| Account | Email | Compliant Password | Role |
| :--- | :--- | :--- | :--- |
| **System Admin** | `admin@scms.out.ac.tz` | `Scms@Admin2026!#` | System Admin |
| **Staff / Tutor** | `info@wolinet.com` | `Scms@Admin2026!#` | Staff |
| **Student** | `bernadorenatus32@gmail.com` | `Scms@Admin2026!#` | Student |

> **Note:** The application enforces a strict security policy requiring at least 8 characters, mixed case, numbers, special characters, and non-compromised passwords. The test credentials above are pre-configured to comply with this policy.

---

## 📋 Key Modules & Features

- **Applicant Registration**: Online student enrollment portal with course selection and branch assignment.
- **Course & Venue Management**: Multi-venue scheduling, course durations, pricing tiers, and tutor assignments.
- **Billing & Payment Schedules**: 
  - Dynamic invoice generation with automated discount calculations.
  - Multi-installment payment schedule tracking and receipt generation.
- **Role-Based Access Control (RBAC)**: Fine-grained permissions powered by DaVinci RBAC (System Admin, Branch Admin, Tutor, Student).
- **Attendance & Certifications**: Attendance verification and verifiable completion certificate generation.
- **Reporting & Business Analytics**: Real-time revenue reports, branch-level metrics, and tutor workload analytics.

---

## 🛠️ System Requirements

- **PHP**: `>= 8.2` (PHP 8.3 recommended)
  - Required Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
- **Web Server**: Apache / Nginx (MAMP, XAMPP, or standalone)
- **Database**: MySQL `>= 5.7` or MariaDB `>= 10.3`
- **Dependency Manager**: Composer `>= 2.0`
- **Node.js**: `>= 18.x` & npm

---

## 💻 Local Installation & Setup (MAMP / Standard)

### 1. Clone the Repository
```bash
git clone https://github.com/Rb-tech-byte/scms.git
cd scms
```

### 2. Configure Environment
Copy `.env.example` or create `.env`:
```bash
cp .env.example .env
```
Update your database configuration in `.env`:
```env
APP_NAME=SCMS
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8888/scms/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=scms
DB_USERNAME=root
DB_PASSWORD=root
DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock
```

### 3. Install PHP Dependencies
```bash
composer install
php artisan key:generate
```

### 4. Database Setup & Migrations
Create the database in MySQL and run migrations:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS scms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan migrate --force
```
*(Optional: Import base dataset using `mysql -u root -p scms < scms.sql` if starting from the seeded snapshot).*

### 5. Build Assets & Link Storage
```bash
npm install
npm run build
php artisan storage:link
```

### 6. Run the Application
When using MAMP, access:
- **Local Application URL**: `http://localhost:8888/scms/public/login` (or `http://localhost:8888/scms`)

Alternatively, start the Laravel built-in server:
```bash
php artisan serve
```

---

## 🧪 User Acceptance Testing (UAT) Guide

1. **System Admin Testing**:
   - Log in with `admin@scms.out.ac.tz` / `Scms@Admin2026!#`.
   - Access the Main Dashboard at `/dashboard`.
   - Review `/applicants`, `/courses`, `/branches`, `/invoices`, and `/payments`.
   - Check Branch Analytics at `/analytics/branches`.

2. **Tutor / Staff Testing**:
   - Log in with `info@wolinet.com` / `Scms@Admin2026!#`.
   - Review assigned course modules, student attendance, and course rosters.

3. **Student Testing**:
   - Log in with `bernadorenatus32@gmail.com` / `Scms@Admin2026!#`.
   - View enrolled courses, invoice balances, payment receipts, and certificate status.

---

## 📄 License
Open-sourced software developed for Open University of Tanzania.
