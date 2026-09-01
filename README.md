# SIMS — School Information Management System

A comprehensive, role-based school management platform built for Ugandan primary and secondary schools.

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.3+)
- **Frontend**: React 19 + Inertia.js v2 + TypeScript + Tailwind CSS 4
- **Database**: MySQL / MariaDB
- **Build**: Vite 7

## Features (Phase 0 + 1 Complete)

- ✅ Authentication (login, logout, password reset, rate limiting, account lockout)
- ✅ Role-Based Access Control — 13 system roles, 55 permissions
- ✅ Audit Logging — immutable audit trail for all sensitive actions
- ✅ School Profile & Configuration
- ✅ Academic Years, Terms, Classes, Streams
- ✅ Subject Catalogue with Grading Schemes (UCE + PLE defaults)
- ✅ Responsive sidebar layout with permission-filtered navigation
- ✅ Full database schema: 57+ tables covering all SRS modules

## Upcoming Modules

- Phase 2: Admissions & Learner Management
- Phase 3: Staff & Teacher Assignments
- Phase 4: Attendance
- Phase 5: Assessment, Marks & Report Cards
- Phase 6: Finance (Fees, Invoices, Payments, Receipts)
- Phase 7: SMS & Parent Portal
- Phase 8: Boarding, Library, Inventory, Transport
- Phase 9: Reports & Dashboards
- Phase 10: UNEB / PLE / UCE / UACE

## Local Setup (WAMP)

```bash
# 1. Clone the repo
git clone https://github.com/mugabecharles/sims.git
cd sims

# 2. Install PHP dependencies
php composer.phar install

# 3. Install JS dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate app key
php artisan key:generate

# 6. Create database (MySQL)
mysql -u root -e "CREATE DATABASE sims CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 7. Run migrations and seed
php artisan migrate --seed

# 8. Build frontend
npm run build
```

## Default Login

| Field | Value |
|-------|-------|
| Email | admin@sims.school |
| Password | Admin1234 |

## Architecture

```
app/
├── Http/Controllers/
│   ├── Auth/          # Login, Password Reset
│   ├── Admin/         # Users, Roles
│   ├── School/        # School Profile & Config
│   └── DashboardController.php
├── Models/            # 54 Eloquent models
├── Services/
│   └── AuditService.php
└── Http/Middleware/
    ├── HandleInertiaRequests.php
    ├── CheckPermission.php
    └── CheckRole.php
```

## License

Private — All rights reserved. School Information Management System for Ugandan schools.
