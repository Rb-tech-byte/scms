# Short Course Management System (SCMS)

Online Course Registration and Management System for the Institute of Education and Management Technologies (IEMT) at the Open University of Tanzania.

---

## Overview

The Short Course Management System (SCMS) is an academic administration portal developed with the Laravel framework. The application coordinates applicant self-registration, multi-venue course scheduling, branch-level operations, structured tuition billing, multi-installment payment schedules, real-time invoice reconciliation, attendance records, and verifiable completion certification.

---

## System Test Credentials

For user acceptance testing (UAT) and administrative verification, use the following provisioned accounts:

### Your Compliant Credentials

| Account | Email | Compliant Password | Role |
| :--- | :--- | :--- | :--- |
| System Admin | admin@scms.out.ac.tz | Scms@Admin2026!# | System Admin |
| Staff / Tutor | info@wolinet.com | Scms@Admin2026!# | Staff |
| Student | bernadorenatus32@gmail.com | Scms@Admin2026!# | Student |

> **Security Policy Compliance:** Passwords conform to government institutional security standards requiring at least 8 characters, mixed-case lettering, numbers, symbols, and non-compromised credential verification.

---

## Core Modules and Capabilities

- **Applicant Registration and Enrollment:** Online registration portal supporting course selection, venue assignment, student identification, and staff-student verification.
- **Course and Venue Administration:** Scheduling across multiple study centres, pricing structures, intake durations, and tutor assignments.
- **Tuition Billing and Payment Schedules:**
  - Automated invoice generation with discount eligibility verification.
  - Multi-installment payment schedule tracking, receipt uploads, and balance recalculation.
- **Role-Based Access Control (RBAC):** Fine-grained permission model regulating access across System Administrators, Branch Managers, Tutors, and Students.
- **Attendance Verification and Certification:** Student participation logging and verifiable completion certificate generation.
- **Administrative Reporting:** Real-time revenue reporting, enrollment distribution, and instructor workload analytics.

---

## System Requirements

- **PHP:** 8.3, 8.4, or 8.5
  - Required Extensions: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `gd`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo_mysql`, `tokenizer`, `xml`, `zip`
- **Web Server:** Apache (with `mod_rewrite` enabled) or Nginx
- **Database:** MySQL 5.7+ or MySQL 8.0+ / MariaDB 10.3+
- **Dependency Managers:**
  - Composer 2.2+
  - Node.js 18.x+ and npm
- **Framework:** Laravel 13

---

## Installation and Setup Guide

### 1. Clone the Repository
```bash
git clone https://github.com/Rb-tech-byte/scms.git
cd scms
```

### 2. Configure Environment Parameters
Create or update the `.env` configuration file:
```bash
cp .env.example .env
```

Ensure the database parameters align with your local server environment (e.g., standard MAMP configuration):
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

### 3. Install PHP Dependencies and Generate Application Key
```bash
composer install
php artisan key:generate
```

### 4. Database Setup and Migration Execution
Verify that the target database exists in MySQL and apply all database migrations:
```bash
php artisan migrate --force
```

### 5. Compile Front-End Assets and Create Storage Symlink
```bash
npm install
npm run build
php artisan storage:link
```

### 6. Automated Deployment & Baseline Data Seeding
For production rollouts or automated server deployments, execute the included deployment script:
```bash
chmod +x deploy.sh
./deploy.sh
```
The deployment script coordinates:
- Graceful maintenance mode activation (`php artisan down`).
- Repository synchronization (`git reset --hard origin/main`).
- Production dependency installation (`composer install --no-dev --optimize-autoloader`).
- Safe database migration execution (`php artisan migrate --force`).
- **Idempotent baseline prior data seeding** (`php artisan db:seed --class=ProductionInitialSeeder --force`).
- Storage symlink verification (`php artisan storage:link`).
- Cache optimization (`config:cache`, `route:cache`, `view:cache`).
- Application reactivation (`php artisan up`).

### 7. Launch Application
- **MAMP Environment:** Open `http://localhost:8888/scms/public/` in your browser.
- **Built-in CLI Server:** Alternatively, start the local development server:
  ```bash
  php artisan serve
  ```

---

## Continuous Integration & Continuous Deployment (CI/CD)

The repository includes a GitHub Actions pipeline configured in `.github/workflows/ci-cd.yml`:
1. **CI Pipeline Gate:** Automatically builds on PHP 8.3 & MySQL 8.0, tests database migrations, runs the idempotent `ProductionInitialSeeder`, and compiles frontend assets on every commit.
2. **Automated CD Server Deployment:** Automatically runs `deploy.sh` on your production server when the following GitHub repository secrets are set:
   - `SSH_HOST`: Target server hostname or IP address.
   - `SSH_USER`: SSH login user (e.g. `ubuntu` or `deploy`).
   - `SSH_KEY`: Private SSH deployment key.
   - `DEPLOY_PATH`: Absolute path to project directory on server (e.g. `/var/www/scms`).


---

## Security and System Hardening

1. **Upload Execution Prevention (php2shell Mitigation):**
   - Uploaded files are restricted to strict MIME types (`application/pdf`, `image/jpeg`, `image/png`).
   - File extensions are determined by MIME inspection rather than client-supplied strings.
   - Storage directories enforce execution blocks denying direct execution of script files (`.php`, `.phtml`, `.phar`, `.sh`).
2. **Password Complexity:** User accounts enforce comprehensive entropy rules with mandatory resets for non-compliant legacy credentials.
3. **Data Protection:** Database operations involving payments, billing, and enrollment transitions are wrapped in atomic transactions.

---

## User Acceptance Testing (UAT) Guide

1. **System Admin Portal:**
   - Sign in using `admin@scms.out.ac.tz` and `Scms@Admin2026!#`.
   - Access the administrative dashboard at `/dashboard`.
   - Inspect applicant records, course assignments, invoices, and analytics at `/analytics/branches`.
2. **Staff and Tutor Portal:**
   - Sign in using `info@wolinet.com` and `Scms@Admin2026!#`.
   - Verify assigned course rosters, student lists, and session logs.
3. **Student Portal:**
   - Sign in using `bernadorenatus32@gmail.com` and `Scms@Admin2026!#`.
   - Check registered courses, outstanding balances, receipts, and certificate status.

---

## Authorship and Development Credits

- **Author & Lead Developer:** Renatus Bernardo (`bernadorenatus32@gmail.com`)
- **Institution:** Institute of Education and Management Technologies (IEMT), The Open University of Tanzania

---

## License

Institutional software property developed for the Institute of Education and Management Technologies (IEMT) at the Open University of Tanzania.
