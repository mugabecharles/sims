<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\GradingScheme;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Term;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SchoolProfileController extends Controller
{
    public function __construct(private AuditService $audit) {}

    private function getSchool(Request $request): School
    {
        return $request->user()->schools()->where('is_primary', true)->firstOrFail();
    }

    // ── School Profile ─────────────────────────────────────────────────────

    public function show(Request $request): Response
    {
        $school = $this->getSchool($request);
        return Inertia::render('School/Profile', ['school' => $school]);
    }

    public function update(Request $request): RedirectResponse
    {
        $school = $this->getSchool($request);

        $data = $request->validate([
            'name'             => ['required', 'string', 'max:191'],
            'short_name'       => ['nullable', 'string', 'max:50'],
            'emis_no'          => ['nullable', 'string', 'max:50'],
            'level'            => ['required', 'in:primary,secondary,combined'],
            'school_type'      => ['required', 'in:day,boarding,mixed'],
            'ownership'        => ['required', 'in:government,private,community,religious'],
            'district'         => ['nullable', 'string', 'max:100'],
            'subcounty'        => ['nullable', 'string', 'max:100'],
            'address'          => ['nullable', 'string'],
            'phone'            => ['nullable', 'string', 'max:20'],
            'email'            => ['nullable', 'email', 'max:191'],
            'website'          => ['nullable', 'url', 'max:191'],
            'motto'            => ['nullable', 'string', 'max:191'],
            'head_teacher_name'=> ['nullable', 'string', 'max:191'],
            'proprietor_name'  => ['nullable', 'string', 'max:191'],
            'admission_no_prefix' => ['nullable', 'string', 'max:20'],
            'invoice_no_prefix'   => ['nullable', 'string', 'max:20'],
            'receipt_no_prefix'   => ['nullable', 'string', 'max:20'],
            'sms_sender_id'    => ['nullable', 'string', 'max:20'],
        ]);

        $original = $school->toArray();
        $school->update($data);
        $this->audit->updated($school, $original, 'School');

        return back()->with('success', 'School profile updated successfully.');
    }

    // ── Academic Years ─────────────────────────────────────────────────────

    public function academicYears(Request $request): Response
    {
        $school = $this->getSchool($request);
        return Inertia::render('School/AcademicYears', [
            'academicYears' => $school->academicYears()->with('terms')->orderByDesc('year')->get(),
        ]);
    }

    public function storeAcademicYear(Request $request): RedirectResponse
    {
        $school = $this->getSchool($request);
        $data   = $request->validate([
            'year'       => ['required', 'string', 'max:10'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ]);

        $year = $school->academicYears()->create($data);
        $this->audit->created($year, 'AcademicYear');

        return back()->with('success', "Academic year {$data['year']} created.");
    }

    public function updateAcademicYear(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $data = $request->validate([
            'year'       => ['required', 'string', 'max:10'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'status'     => ['required', 'in:upcoming,active,completed,archived'],
        ]);

        $original = $academicYear->toArray();
        $academicYear->update($data);
        $this->audit->updated($academicYear, $original, 'AcademicYear');

        return back()->with('success', 'Academic year updated.');
    }

    public function setCurrentYear(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $school = $this->getSchool($request);
        $school->academicYears()->update(['is_current' => false]);
        $academicYear->update(['is_current' => true, 'status' => 'active']);
        $this->audit->log('set_current', null, $academicYear, null, null, "Set as current academic year", 'AcademicYear');

        return back()->with('success', "Year {$academicYear->year} set as current.");
    }

    // ── Terms ──────────────────────────────────────────────────────────────

    public function terms(Request $request): Response
    {
        $school = $this->getSchool($request);
        return Inertia::render('School/Terms', [
            'academicYears' => $school->academicYears()->with('terms')->orderByDesc('year')->get(),
        ]);
    }

    public function storeTerm(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name'             => ['required', 'string', 'max:50'],
            'term_no'          => ['required', 'integer', 'min:1', 'max:3'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after:start_date'],
        ]);

        $term = Term::create($data);
        $this->audit->created($term, 'Term');

        return back()->with('success', "{$data['name']} created.");
    }

    public function updateTerm(Request $request, Term $term): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:50'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'status'     => ['required', 'in:upcoming,active,completed'],
        ]);

        $original = $term->toArray();
        $term->update($data);
        $this->audit->updated($term, $original, 'Term');

        return back()->with('success', 'Term updated.');
    }

    // ── Classes ────────────────────────────────────────────────────────────

    public function classes(Request $request): Response
    {
        $school = $this->getSchool($request);
        return Inertia::render('School/Classes', [
            'classes' => $school->classes()->with('streams')->orderBy('sort_order')->get(),
        ]);
    }

    public function storeClass(Request $request): RedirectResponse
    {
        $school = $this->getSchool($request);
        $data   = $request->validate([
            'name'         => ['required', 'string', 'max:20'],
            'display_name' => ['nullable', 'string', 'max:100'],
            'level'        => ['required', 'in:primary,secondary'],
            'section'      => ['required', 'in:o_level,a_level,primary'],
            'sort_order'   => ['nullable', 'integer'],
        ]);

        $class = $school->classes()->create($data);
        $this->audit->created($class, 'SchoolClass');

        return back()->with('success', "{$data['name']} class created.");
    }

    public function updateClass(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:20'],
            'display_name' => ['nullable', 'string', 'max:100'],
            'level'        => ['required', 'in:primary,secondary'],
            'section'      => ['required', 'in:o_level,a_level,primary'],
            'sort_order'   => ['nullable', 'integer'],
            'active'       => ['boolean'],
        ]);

        $original = $schoolClass->toArray();
        $schoolClass->update($data);
        $this->audit->updated($schoolClass, $original, 'SchoolClass');

        return back()->with('success', 'Class updated.');
    }

    // ── Subjects ───────────────────────────────────────────────────────────

    public function subjects(Request $request): Response
    {
        $school = $this->getSchool($request);
        return Inertia::render('School/Subjects', [
            'subjects' => $school->subjects()->orderBy('name')->get(),
        ]);
    }

    public function storeSubject(Request $request): RedirectResponse
    {
        $school = $this->getSchool($request);
        $data   = $request->validate([
            'code'         => ['nullable', 'string', 'max:20'],
            'name'         => ['required', 'string', 'max:100'],
            'level'        => ['required', 'in:primary,secondary,both'],
            'subject_type' => ['required', 'in:compulsory,optional,elective'],
            'department'   => ['nullable', 'string', 'max:100'],
        ]);

        $subject = $school->subjects()->create($data);
        $this->audit->created($subject, 'Subject');

        return back()->with('success', "{$data['name']} subject created.");
    }

    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $data = $request->validate([
            'code'         => ['nullable', 'string', 'max:20'],
            'name'         => ['required', 'string', 'max:100'],
            'level'        => ['required', 'in:primary,secondary,both'],
            'subject_type' => ['required', 'in:compulsory,optional,elective'],
            'department'   => ['nullable', 'string', 'max:100'],
            'active'       => ['boolean'],
        ]);

        $original = $subject->toArray();
        $subject->update($data);
        $this->audit->updated($subject, $original, 'Subject');

        return back()->with('success', 'Subject updated.');
    }

    // ── Grading Schemes ────────────────────────────────────────────────────

    public function grading(Request $request): Response
    {
        $school = $this->getSchool($request);
        return Inertia::render('School/Grading', [
            'schemes'       => $school->gradingSchemes ?? GradingScheme::where('school_id', $school->id)->get(),
            'academicYears' => $school->academicYears()->orderByDesc('year')->get(),
        ]);
    }

    public function storeGrading(Request $request): RedirectResponse
    {
        $school = $this->getSchool($request);
        $data   = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'level'            => ['required', 'in:primary,secondary,both'],
            'rules'            => ['required', 'array', 'min:1'],
            'rules.*.min'      => ['required', 'numeric', 'min:0'],
            'rules.*.max'      => ['required', 'numeric', 'max:100'],
            'rules.*.grade'    => ['required', 'string'],
            'rules.*.points'   => ['nullable', 'integer'],
            'rules.*.remark'   => ['nullable', 'string'],
            'is_default'       => ['boolean'],
        ]);
        $data['school_id'] = $school->id;

        $scheme = GradingScheme::create($data);
        $this->audit->created($scheme, 'GradingScheme');

        return back()->with('success', "Grading scheme '{$data['name']}' created.");
    }

    public function updateGrading(Request $request, GradingScheme $scheme): RedirectResponse
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:100'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'level'            => ['required', 'in:primary,secondary,both'],
            'rules'            => ['required', 'array', 'min:1'],
            'rules.*.min'      => ['required', 'numeric', 'min:0'],
            'rules.*.max'      => ['required', 'numeric', 'max:100'],
            'rules.*.grade'    => ['required', 'string'],
            'rules.*.points'   => ['nullable', 'integer'],
            'rules.*.remark'   => ['nullable', 'string'],
            'is_default'       => ['boolean'],
        ]);

        $original = $scheme->toArray();
        $scheme->update($data);
        $this->audit->updated($scheme, $original, 'GradingScheme');

        return back()->with('success', 'Grading scheme updated.');
    }
}
