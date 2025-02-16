<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Office;
use App\Models\Realtor;
use App\Models\OfficeVisit;
use Illuminate\Http\Request;
use App\Models\RealtorSequence;

class DashboardController extends Controller
{
    public function index()
    {
        $uncompletedTaskCount = Task::whereNotIn('status', ['completed', 'cancelled'])->count();
        $realtorCount = Realtor::all()->count();
        $officeCount = Office::all()->count();
        $visitCount = OfficeVisit::all()->count();

        $activeSequences = RealtorSequence::whereNotIn('status', ['completed', 'cancelled'])->with('realtor')->get();
        $recentRealtors = Realtor::latest()->take(5)->get();

        $totalRealtorValue = Realtor::all()->sum(fn($realtor) => $realtor->value);

        return inertia(
            'Dashboard',
            [
                'uncompleted_task_count' => $uncompletedTaskCount,
                'realtor_count' => $realtorCount,
                'recent_realtors' => $recentRealtors,
                'office_count' => $officeCount,
                'visit_count' => $visitCount,
                'active_sequences' => $activeSequences,
                'total_revenue' => '$' . number_format($totalRealtorValue, 2),
            ]
        );
    }
}
