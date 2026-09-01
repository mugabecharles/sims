<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\GradingScheme;
use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Permissions ───────────────────────────────────────────────────
        $permissionsData = [
            // Users & Roles
            ['code' => 'users.view',        'display_name' => 'View Users',             'module' => 'admin'],
            ['code' => 'users.create',      'display_name' => 'Create Users',           'module' => 'admin'],
            ['code' => 'users.edit',        'display_name' => 'Edit Users',             'module' => 'admin'],
            ['code' => 'users.delete',      'display_name' => 'Delete Users',           'module' => 'admin'],
            ['code' => 'roles.manage',      'display_name' => 'Manage Roles',           'module' => 'admin'],
            // School
            ['code' => 'school.view',       'display_name' => 'View School Profile',    'module' => 'school'],
            ['code' => 'school.edit',       'display_name' => 'Edit School Profile',    'module' => 'school'],
            ['code' => 'school.configure',  'display_name' => 'Configure School',       'module' => 'school'],
            // Learners
            ['code' => 'learners.view',     'display_name' => 'View Learners',          'module' => 'learners'],
            ['code' => 'learners.create',   'display_name' => 'Admit Learners',         'module' => 'learners'],
            ['code' => 'learners.edit',     'display_name' => 'Edit Learner Records',   'module' => 'learners'],
            ['code' => 'learners.delete',   'display_name' => 'Archive Learners',       'module' => 'learners'],
            ['code' => 'learners.export',   'display_name' => 'Export Learner Data',    'module' => 'learners'],
            // Admissions
            ['code' => 'admissions.view',   'display_name' => 'View Applications',      'module' => 'admissions'],
            ['code' => 'admissions.create', 'display_name' => 'Create Applications',    'module' => 'admissions'],
            ['code' => 'admissions.approve','display_name' => 'Approve Applications',   'module' => 'admissions'],
            // Attendance
            ['code' => 'attendance.view',   'display_name' => 'View Attendance',        'module' => 'attendance'],
            ['code' => 'attendance.create', 'display_name' => 'Record Attendance',      'module' => 'attendance'],
            ['code' => 'attendance.correct','display_name' => 'Correct Attendance',     'module' => 'attendance'],
            ['code' => 'attendance.approve','display_name' => 'Approve Attendance',     'module' => 'attendance'],
            // Assessments
            ['code' => 'assessments.view',  'display_name' => 'View Assessments',       'module' => 'assessments'],
            ['code' => 'assessments.create','display_name' => 'Create Assessments',     'module' => 'assessments'],
            ['code' => 'assessments.edit',  'display_name' => 'Edit/Enter Marks',       'module' => 'assessments'],
            ['code' => 'assessments.approve','display_name' => 'Approve Marks',         'module' => 'assessments'],
            ['code' => 'assessments.publish','display_name' => 'Publish Results',       'module' => 'assessments'],
            // UNEB
            ['code' => 'uneb.view',         'display_name' => 'View UNEB Records',      'module' => 'uneb'],
            ['code' => 'uneb.manage',       'display_name' => 'Manage UNEB Candidates', 'module' => 'uneb'],
            // Finance
            ['code' => 'finance.view',      'display_name' => 'View Finance',           'module' => 'finance'],
            ['code' => 'finance.invoice',   'display_name' => 'Create Invoices',        'module' => 'finance'],
            ['code' => 'finance.receive',   'display_name' => 'Receive Payments',       'module' => 'finance'],
            ['code' => 'finance.approve',   'display_name' => 'Approve Financials',     'module' => 'finance'],
            ['code' => 'finance.export',    'display_name' => 'Export Finance Reports', 'module' => 'finance'],
            ['code' => 'finance.reverse',   'display_name' => 'Reverse Transactions',   'module' => 'finance'],
            // Communication
            ['code' => 'sms.view',          'display_name' => 'View SMS Logs',          'module' => 'communication'],
            ['code' => 'sms.send',          'display_name' => 'Send SMS',               'module' => 'communication'],
            ['code' => 'sms.bulk',          'display_name' => 'Send Bulk SMS',          'module' => 'communication'],
            // Discipline
            ['code' => 'discipline.view',   'display_name' => 'View Discipline Cases',  'module' => 'discipline'],
            ['code' => 'discipline.create', 'display_name' => 'Record Discipline',      'module' => 'discipline'],
            ['code' => 'discipline.edit',   'display_name' => 'Edit Discipline Cases',  'module' => 'discipline'],
            // Health
            ['code' => 'health.view',       'display_name' => 'View Health Records',    'module' => 'health'],
            ['code' => 'health.manage',     'display_name' => 'Manage Health Records',  'module' => 'health'],
            // Boarding
            ['code' => 'boarding.view',     'display_name' => 'View Boarding',          'module' => 'boarding'],
            ['code' => 'boarding.manage',   'display_name' => 'Manage Boarding',        'module' => 'boarding'],
            // Library
            ['code' => 'library.view',      'display_name' => 'View Library',           'module' => 'library'],
            ['code' => 'library.manage',    'display_name' => 'Manage Library',         'module' => 'library'],
            // Inventory
            ['code' => 'inventory.view',    'display_name' => 'View Inventory',         'module' => 'inventory'],
            ['code' => 'inventory.manage',  'display_name' => 'Manage Inventory',       'module' => 'inventory'],
            // Transport
            ['code' => 'transport.view',    'display_name' => 'View Transport',         'module' => 'transport'],
            ['code' => 'transport.manage',  'display_name' => 'Manage Transport',       'module' => 'transport'],
            // Reports
            ['code' => 'reports.view',      'display_name' => 'View Reports',           'module' => 'reports'],
            ['code' => 'reports.export',    'display_name' => 'Export Reports',         'module' => 'reports'],
            ['code' => 'reports.management','display_name' => 'Management Reports',     'module' => 'reports'],
            // Audit
            ['code' => 'audit.view',        'display_name' => 'View Audit Logs',        'module' => 'audit'],
            // Staff
            ['code' => 'staff.view',        'display_name' => 'View Staff',             'module' => 'staff'],
            ['code' => 'staff.create',      'display_name' => 'Add Staff',              'module' => 'staff'],
            ['code' => 'staff.edit',        'display_name' => 'Edit Staff',             'module' => 'staff'],
        ];

        $permissions = [];
        foreach ($permissionsData as $p) {
            $permissions[$p['code']] = Permission::firstOrCreate(['code' => $p['code']], $p);
        }

        // ── Roles ─────────────────────────────────────────────────────────
        $allCodes  = array_keys($permissions);
        $rolesData = [
            [
                'name'         => 'system_admin',
                'display_name' => 'System Administrator',
                'description'  => 'Full system access',
                'is_system'    => true,
                'permissions'  => $allCodes,
            ],
            [
                'name'         => 'director',
                'display_name' => 'Director / Proprietor',
                'description'  => 'Strategic oversight',
                'is_system'    => true,
                'permissions'  => ['school.view','learners.view','learners.export','staff.view','finance.view','finance.export','reports.view','reports.export','reports.management','assessments.view','attendance.view','audit.view'],
            ],
            [
                'name'         => 'head_teacher',
                'display_name' => 'Head Teacher / Principal',
                'description'  => 'Academic and administrative leadership',
                'is_system'    => true,
                'permissions'  => ['school.view','school.edit','learners.view','learners.create','learners.edit','learners.export','admissions.view','admissions.approve','attendance.view','attendance.approve','assessments.view','assessments.approve','assessments.publish','uneb.view','uneb.manage','finance.view','finance.approve','sms.send','sms.bulk','discipline.view','discipline.edit','health.view','boarding.view','boarding.manage','reports.view','reports.export','reports.management','staff.view','staff.create','staff.edit','audit.view'],
            ],
            [
                'name'         => 'bursar',
                'display_name' => 'Bursar / Accountant',
                'description'  => 'Financial management',
                'is_system'    => true,
                'permissions'  => ['finance.view','finance.invoice','finance.receive','finance.approve','finance.export','finance.reverse','learners.view','sms.send','reports.view','reports.export'],
            ],
            [
                'name'         => 'exam_officer',
                'display_name' => 'Academic Registrar / Exam Officer',
                'description'  => 'Academic and examinations management',
                'is_system'    => true,
                'permissions'  => ['learners.view','learners.create','learners.edit','admissions.view','assessments.view','assessments.create','assessments.edit','assessments.approve','assessments.publish','uneb.view','uneb.manage','reports.view','reports.export'],
            ],
            [
                'name'         => 'teacher',
                'display_name' => 'Teacher',
                'description'  => 'Teaching, attendance and marks entry for assigned classes',
                'is_system'    => true,
                'permissions'  => ['learners.view','attendance.view','attendance.create','assessments.view','assessments.edit','sms.send','discipline.create','reports.view'],
            ],
            [
                'name'         => 'boarding_master',
                'display_name' => 'Boarding / House Master',
                'description'  => 'Hostel management',
                'is_system'    => true,
                'permissions'  => ['boarding.view','boarding.manage','learners.view','attendance.view','attendance.create'],
            ],
            [
                'name'         => 'librarian',
                'display_name' => 'Librarian',
                'description'  => 'Library management',
                'is_system'    => true,
                'permissions'  => ['library.view','library.manage','learners.view'],
            ],
            [
                'name'         => 'store_officer',
                'display_name' => 'Store / Inventory Officer',
                'description'  => 'Inventory management',
                'is_system'    => true,
                'permissions'  => ['inventory.view','inventory.manage'],
            ],
            [
                'name'         => 'nurse',
                'display_name' => 'Nurse / Welfare Officer',
                'description'  => 'Health records — restricted access',
                'is_system'    => true,
                'permissions'  => ['health.view','health.manage','learners.view'],
            ],
            [
                'name'         => 'admissions_officer',
                'display_name' => 'Reception / Admissions Officer',
                'description'  => 'Admissions and learner records',
                'is_system'    => true,
                'permissions'  => ['admissions.view','admissions.create','learners.view','learners.create','learners.edit'],
            ],
            [
                'name'         => 'parent',
                'display_name' => 'Parent / Guardian',
                'description'  => 'View own children information',
                'is_system'    => true,
                'permissions'  => ['learners.view','attendance.view','assessments.view','finance.view'],
            ],
            [
                'name'         => 'student',
                'display_name' => 'Student / Learner',
                'description'  => 'View own profile and results',
                'is_system'    => true,
                'permissions'  => ['assessments.view','attendance.view'],
            ],
        ];

        foreach ($rolesData as $rd) {
            $roleCodes = $rd['permissions'];
            unset($rd['permissions']);

            $role = Role::firstOrCreate(['name' => $rd['name']], $rd);

            $permissionIds = collect($roleCodes)
                ->map(fn($code) => $permissions[$code]?->id)
                ->filter()
                ->toArray();

            $role->permissions()->sync($permissionIds);
        }

        // ── System Admin User ─────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@sims.school'],
            [
                'name'     => 'System Administrator',
                'username' => 'admin',
                'password' => Hash::make('Admin1234'),
                'status'   => 'active',
            ]
        );
        $adminRole = Role::where('name', 'system_admin')->first();
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // ── Sample School ─────────────────────────────────────────────────
        $school = School::firstOrCreate(
            ['name' => 'St. Mary\'s College'],
            [
                'short_name'       => 'SMC',
                'level'            => 'combined',
                'ownership'        => 'private',
                'school_type'      => 'mixed',
                'district'         => 'Kampala',
                'subcounty'        => 'Central Division',
                'phone'            => '+256700000000',
                'email'            => 'info@smc.ac.ug',
                'currency'         => 'UGX',
                'timezone'         => 'Africa/Kampala',
                'admission_no_prefix' => 'SMC',
                'invoice_no_prefix'   => 'INV',
                'receipt_no_prefix'   => 'RCP',
                'learner_id_prefix'   => 'STU',
                'status'           => 'active',
            ]
        );

        // Link admin to school
        $school->users()->syncWithoutDetaching([$admin->id => ['is_primary' => true]]);

        // ── Academic Year & Terms ─────────────────────────────────────────
        $year = AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'year' => '2026'],
            [
                'start_date' => '2026-01-06',
                'end_date'   => '2026-11-30',
                'status'     => 'active',
                'is_current' => true,
            ]
        );

        $termsData = [
            ['name' => 'Term 1', 'term_no' => 1, 'start_date' => '2026-01-06', 'end_date' => '2026-04-11', 'status' => 'completed'],
            ['name' => 'Term 2', 'term_no' => 2, 'start_date' => '2026-05-04', 'end_date' => '2026-08-07', 'status' => 'active', 'is_current' => true],
            ['name' => 'Term 3', 'term_no' => 3, 'start_date' => '2026-09-07', 'end_date' => '2026-11-28', 'status' => 'upcoming'],
        ];
        foreach ($termsData as $td) {
            Term::firstOrCreate(['academic_year_id' => $year->id, 'term_no' => $td['term_no']], $td);
        }

        // ── Primary Classes ───────────────────────────────────────────────
        $primaryClasses = ['P1','P2','P3','P4','P5','P6','P7'];
        foreach ($primaryClasses as $i => $cls) {
            $num   = $i + 1;
            $class = SchoolClass::firstOrCreate(
                ['school_id' => $school->id, 'name' => $cls],
                ['display_name' => "Primary {$num}", 'level' => 'primary', 'section' => 'primary', 'sort_order' => $num]
            );
            Stream::firstOrCreate(['class_id' => $class->id, 'name' => 'A'], ['capacity' => 45]);
            Stream::firstOrCreate(['class_id' => $class->id, 'name' => 'B'], ['capacity' => 45]);
        }

        // ── Secondary Classes ─────────────────────────────────────────────
        $oLevelClasses = ['S1','S2','S3','S4'];
        foreach ($oLevelClasses as $i => $cls) {
            $num   = $i + 1;
            $class = SchoolClass::firstOrCreate(
                ['school_id' => $school->id, 'name' => $cls],
                ['display_name' => "Senior {$num}", 'level' => 'secondary', 'section' => 'o_level', 'sort_order' => 10 + $i]
            );
            Stream::firstOrCreate(['class_id' => $class->id, 'name' => 'A'], ['capacity' => 45]);
            Stream::firstOrCreate(['class_id' => $class->id, 'name' => 'B'], ['capacity' => 45]);
        }

        $aLevelClasses = ['S5','S6'];
        foreach ($aLevelClasses as $i => $cls) {
            $num   = $i + 5;
            $class = SchoolClass::firstOrCreate(
                ['school_id' => $school->id, 'name' => $cls],
                ['display_name' => "Senior {$num}", 'level' => 'secondary', 'section' => 'a_level', 'sort_order' => 15 + $i]
            );
            Stream::firstOrCreate(['class_id' => $class->id, 'name' => 'Arts'], ['capacity' => 40]);
            Stream::firstOrCreate(['class_id' => $class->id, 'name' => 'Sciences'], ['capacity' => 40]);
        }

        // ── Subjects ──────────────────────────────────────────────────────
        $subjectData = [
            ['code' => 'ENG', 'name' => 'English Language',     'level' => 'both',      'subject_type' => 'compulsory'],
            ['code' => 'MAT', 'name' => 'Mathematics',          'level' => 'both',      'subject_type' => 'compulsory'],
            ['code' => 'SCI', 'name' => 'Science',              'level' => 'primary',   'subject_type' => 'compulsory'],
            ['code' => 'SST', 'name' => 'Social Studies',       'level' => 'primary',   'subject_type' => 'compulsory'],
            ['code' => 'CRE', 'name' => 'Christian RE',         'level' => 'both',      'subject_type' => 'optional'],
            ['code' => 'IRE', 'name' => 'Islamic RE',           'level' => 'both',      'subject_type' => 'optional'],
            ['code' => 'BIO', 'name' => 'Biology',              'level' => 'secondary', 'subject_type' => 'compulsory'],
            ['code' => 'CHE', 'name' => 'Chemistry',            'level' => 'secondary', 'subject_type' => 'compulsory'],
            ['code' => 'PHY', 'name' => 'Physics',              'level' => 'secondary', 'subject_type' => 'compulsory'],
            ['code' => 'HIS', 'name' => 'History',              'level' => 'secondary', 'subject_type' => 'optional'],
            ['code' => 'GEO', 'name' => 'Geography',            'level' => 'secondary', 'subject_type' => 'optional'],
            ['code' => 'LIT', 'name' => 'Literature in English','level' => 'secondary', 'subject_type' => 'optional'],
            ['code' => 'ECO', 'name' => 'Economics',            'level' => 'secondary', 'subject_type' => 'optional'],
            ['code' => 'COM', 'name' => 'Commerce',             'level' => 'secondary', 'subject_type' => 'optional'],
            ['code' => 'ACC', 'name' => 'Accounting',           'level' => 'secondary', 'subject_type' => 'optional'],
            ['code' => 'ICT', 'name' => 'Information Technology','level' => 'both',     'subject_type' => 'optional'],
            ['code' => 'AGR', 'name' => 'Agriculture',          'level' => 'both',      'subject_type' => 'optional'],
        ];
        foreach ($subjectData as $sd) {
            Subject::firstOrCreate(['school_id' => $school->id, 'name' => $sd['name']], $sd);
        }

        // ── Default UCE Grading Scheme ─────────────────────────────────────
        GradingScheme::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'UCE Grading (Standard)'],
            [
                'academic_year_id' => $year->id,
                'level'            => 'secondary',
                'is_default'       => true,
                'rules'            => [
                    ['min' => 75, 'max' => 100, 'grade' => 'D1', 'points' => 1,  'remark' => 'Distinction 1'],
                    ['min' => 70, 'max' => 74,  'grade' => 'D2', 'points' => 2,  'remark' => 'Distinction 2'],
                    ['min' => 65, 'max' => 69,  'grade' => 'C3', 'points' => 3,  'remark' => 'Credit 3'],
                    ['min' => 60, 'max' => 64,  'grade' => 'C4', 'points' => 4,  'remark' => 'Credit 4'],
                    ['min' => 55, 'max' => 59,  'grade' => 'C5', 'points' => 5,  'remark' => 'Credit 5'],
                    ['min' => 50, 'max' => 54,  'grade' => 'C6', 'points' => 6,  'remark' => 'Credit 6'],
                    ['min' => 45, 'max' => 49,  'grade' => 'P7', 'points' => 7,  'remark' => 'Pass 7'],
                    ['min' => 40, 'max' => 44,  'grade' => 'P8', 'points' => 8,  'remark' => 'Pass 8'],
                    ['min' => 0,  'max' => 39,  'grade' => 'F9', 'points' => 9,  'remark' => 'Fail 9'],
                ],
            ]
        );

        // ── Default PLE Grading Scheme ─────────────────────────────────────
        GradingScheme::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'PLE Grading (Standard)'],
            [
                'academic_year_id' => $year->id,
                'level'            => 'primary',
                'is_default'       => true,
                'rules'            => [
                    ['min' => 80, 'max' => 100, 'grade' => '1', 'points' => 1, 'remark' => 'Distinction'],
                    ['min' => 60, 'max' => 79,  'grade' => '2', 'points' => 2, 'remark' => 'Credit'],
                    ['min' => 40, 'max' => 59,  'grade' => '3', 'points' => 3, 'remark' => 'Pass'],
                    ['min' => 0,  'max' => 39,  'grade' => '4', 'points' => 4, 'remark' => 'Fail'],
                ],
            ]
        );

        $this->command->info('✅ SIMS seed complete. Admin login: admin@sims.school / Admin@1234');
    }
}
