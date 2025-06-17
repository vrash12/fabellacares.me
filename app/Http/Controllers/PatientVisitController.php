<?php
// app/Http/Controllers/PatientVisitController.php
namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Models\Visit; 

class PatientVisitController extends Controller
{
    public function __construct()
    {
        // admin + encoder can view; patients may see only their own record
        $this->middleware('auth');
        $this->middleware('role:admin,encoder')
             ->except(['index','show']);
    }

public function index(Patient $patient)
    {
        $visits = $patient->visits()
                          ->with(['queue','department'])
                          ->orderByDesc('visited_at')
                          ->paginate(20);

        return view('visits.index', compact('patient','visits'));
    }

public function show(Patient $patient, $visitId)
{
    $visit = $patient->visits()
                     ->with(['queue', 'department', 'token'])
                     ->findOrFail($visitId);

    return view('visits.show', compact('patient','visit'));
}
}
