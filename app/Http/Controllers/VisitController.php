<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

  public function index(Patient $patient)
    {
        // 1) Grab all visits for this patient (you can paginate or not):
        $visits = Visit::where('patient_id', $patient->id)
                       ->orderBy('visited_at', 'desc')
                       ->paginate(10);

        // 2) Return the view, passing both $patient and $visits
        return view('visits.index', compact('patient', 'visits'));
    }
}
