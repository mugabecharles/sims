# SIMS – Setup & Developer Reference

## Phase 0 + 1 Complete ✅

### Stack
- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: React 19 + Inertia.js v2 + TypeScript + Tailwind CSS 4
- **Database**: MySQL/MariaDB (WAMP)
- **Build**: Vite 7

---

## To Run the App

1. **Start WAMP** from the taskbar tray — ensure the icon turns green (Apache + MySQL running).

2. Open your browser and go to:
   ```
   http://localhost/sims/public
   ```

3. Log in with:
   - **Email**: `admin@sims.school`
   - **Password**: `Admin1234`

---

## Development Commands

All commands must be run from `C:\wamp64\www\sims`.

Use the PHP binary:
```
C:\wamp64\bin\php\php8.2.26\php.exe
```

Use composer via:
```
C:\wamp64\bin\php\php8.2.26\php.exe C:\wamp64\bin\composer.phar
```

### Common Artisan Commands
```powershell
# Run migrations (fresh wipe + re-seed)
& "C:\wamp64\bin\php\php8.2.26\php.exe" artisan migrate:fresh --seed --force

# Run seeder only
& "C:\wamp64\bin\php\php8.2.26\php.exe" artisan db:seed --force

# Clear all caches
& "C:\wamp64\bin\php\php8.2.26\php.exe" artisan config:clear
& "C:\wamp64\bin\php\php8.2.26\php.exe" artisan route:clear
& "C:\wamp64\bin\php\php8.2.26\php.exe" artisan view:clear

# Create a migration
& "C:\wamp64\bin\php\php8.2.26\php.exe" artisan make:migration create_xxx_table

# Create a model+controller
& "C:\wamp64\bin\php\php8.2.26\php.exe" artisan make:model Xxx -c
```

### Frontend (npm)
```powershell
# Production build
npm run build

# Development server (hot reload) — run manually in a terminal
npm run dev
```

---

## Project Structure

```
sims/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/          LoginController, PasswordResetController
│   │   │   ├── Admin/         UserController, RoleController
│   │   │   ├── School/        SchoolProfileController
│   │   │   └── DashboardController
│   │   └── Middleware/
│   │       ├── HandleInertiaRequests.php   (shares auth/flash/school to React)
│   │       ├── CheckPermission.php         (usage: middleware('permission:code'))
│   │       └── CheckRole.php               (usage: middleware('role:name'))
│   ├── Models/                54 Eloquent models
│   ├── Services/
│   │   └── AuditService.php   (log(), created(), updated(), deleted())
│   └── Providers/
│       └── AppServiceProvider.php
├── database/
│   ├── migrations/            16 migrations covering all SRS tables
│   └── seeders/
│       └── DatabaseSeeder.php (roles, permissions, sample school, classes, subjects, grading)
├── resources/
│   ├── css/app.css
│   └── js/
│       ├── app.tsx            (Inertia entry point)
│       ├── bootstrap.js
│       ├── types/index.d.ts   (TypeScript interfaces: User, School, PageProps)
│       ├── Layouts/
│       │   └── AppLayout.tsx  (sidebar nav, flash messages, logout)
│       └── Pages/
│           ├── Dashboard.tsx
│           ├── Auth/          Login, ForgotPassword, ResetPassword
│           ├── School/        Profile, AcademicYears, Terms, Classes, Subjects, Grading
│           └── Admin/         Users (Index, Create, Edit), Roles (Index)
├── routes/
│   ├── web.php                (auth, dashboard, school setup, admin)
│   └── api.php                (v1 placeholder)
├── public/build/              (compiled Vite assets)
└── .env                       (DB, app key, timezone: Africa/Kampala)
```

---

## Default Roles (from seeder)

| Role | Description |
|---|---|
| system_admin | Full system access |
| director | Strategic oversight, all reports |
| head_teacher | Academic + administrative leadership |
| bursar | Finance management |
| exam_officer | Academic registrar / UNEB records |
| teacher | Attendance + marks for assigned classes |
| boarding_master | Hostel management |
| librarian | Library management |
| store_officer | Inventory management |
| nurse | Health records (restricted) |
| admissions_officer | Admissions + learner records |
| parent | View own children only |
| student | View own profile only |

---

## Permission Pattern

```php
// In routes
Route::get('/invoices', ...)->middleware('permission:finance.view');
Route::post('/payments', ...)->middleware('permission:finance.receive');

// In controllers (programmatic)
if (!auth()->user()->hasPermission('assessments.approve')) {
    abort(403);
}

// Multiple permissions (any match grants access)
Route::post('/sms', ...)->middleware('permission:sms.send,sms.bulk');
```

---

## Audit Logging Pattern

```php
use App\Services\AuditService;

// Inject in controller
public function __construct(private AuditService $audit) {}

// Log creation
$this->audit->created($model, 'ModuleName');

// Log update (pass original before update)
$original = $model->toArray();
$model->update($data);
$this->audit->updated($model, $original, 'ModuleName');

// Log deletion
$this->audit->deleted($model, 'Reason for deletion', 'ModuleName');

// Custom action
$this->audit->log('approved', null, $invoice, null, ['amount' => 150000]);
```

---

## Next Modules to Build

| Phase | Module | Status |
|---|---|---|
| Phase 2 | Admissions & Learner Management | 🔜 Next |
| Phase 3 | Staff & Teacher Assignments | 🔜 |
| Phase 4 | Attendance | 🔜 |
| Phase 5 | Assessment, Marks & Report Cards | 🔜 |
| Phase 6 | Finance (Fees, Invoices, Payments) | 🔜 |
| Phase 7 | SMS & Parent Portal | 🔜 |
| Phase 8 | Boarding, Library, Inventory, Transport | 🔜 |
| Phase 9 | Reports & Dashboards | 🔜 |
| Phase 10 | UNEB / PLE / UCE / UACE | 🔜 |
| Phase 11 | Hardening, Security, Backups | 🔜 |
