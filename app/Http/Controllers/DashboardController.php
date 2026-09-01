<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user   = $request->user();
        $school = $user->schools()->where('is_primary', true)->first();

        $stats = [];

        if ($school) {
            $stats = [
                'total_learners'     => Learner::where('school_id', $school->id)->where('status', 'enrolled')->count(),
                'total_outstanding'  => Invoice::where('school_id', $school->id)
                    ->whereIn('status', ['issued', 'partially_paid', 'overdue'])
                    ->sum('balance'),
                'payments_today'     => Payment::where('school_id', $school->id)
                    ->whereDate('received_at', today())
                    ->where('status', 'confirmed')
                    ->sum('amount'),
            ];
        }

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
