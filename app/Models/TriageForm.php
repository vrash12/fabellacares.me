<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TriageForm extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'triage_forms';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'patient_id',
        'tetanus_t1_date',
        'tetanus_t1_signature',
        'tetanus_t2_date',
        'tetanus_t2_signature',
        'tetanus_t3_date',
        'tetanus_t3_signature',
        'tetanus_t4_date',
        'tetanus_t4_signature',
        'tetanus_t5_date',
        'tetanus_t5_signature',
        'present_health_problems',
        'present_problems_other',
        'danger_signs',
        'danger_signs_other',
        'ob_history',
        'family_planning',
        'prev_pnc',
        'lmp',
        'edc',
        'gravida',
        'parity_t',
        'parity_p',
        'parity_a',
        'parity_l',
        'aog_weeks',
        'chief_complaint',
        'physical_exam_log',
        'heent',
        'heart_lungs',
        'diagnosis',
        'prepared_by',
        'blood_type',
        'delivery_type',
        'birth_weight',
        'birth_length',
        'apgar_appearance',
        'apgar_pulse',
        'apgar_grimace',
        'apgar_activity',
        'apgar_respiration',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'tetanus_t1_date'           => 'date',
        'tetanus_t2_date'           => 'date',
        'tetanus_t3_date'           => 'date',
        'tetanus_t4_date'           => 'date',
        'tetanus_t5_date'           => 'date',
        'present_health_problems'   => 'array',
        'danger_signs'              => 'array',
        'ob_history'                => 'array',
        'lmp'                       => 'date',
        'edc'                       => 'date',
        'physical_exam_log'         => 'array',
        'birth_weight'              => 'decimal:2',
        'birth_length'              => 'decimal:2',
    ];

    /**
     * Relations
     */

    /**
     * Each triage form belongs to one patient.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
