<?php

namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // same filter logic as your index
        $subQuery = \App\Models\OpdSubmission::whereHas('form', fn($q) => $q->where('form_no','OPD-F-07'))
            ->get()
            ->pluck('patient_id')
            ->unique();

        return Patient::with('profile')
            ->whereIn('id', $subQuery)
            ->get()
            ->map(fn($p) => [
                'ID'        => $p->id,
                'Name'      => $p->name,
                'Sex'       => ucfirst($p->profile->sex ?? '—'),
                'Age'       => $p->profile->birth_date ? now()->diffInYears($p->profile->birth_date) : '—',
                'Visits'    => $p->visits_count,
                'Created At'=> $p->created_at->format('Y-m-d'),
            ]);
    }

    public function headings(): array
    {
        return ['ID','Name','Sex','Age','Visits','Created At'];
    }
}
