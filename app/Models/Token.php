<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'queue_id',
        'department_id',   // ← already used in patient flow
        'patient_id',      // ← already used in patient flow
        'code',
        'served_at',
    ];

    protected $casts = [
        'served_at' => 'datetime',
    ];

    /* ─────────── relationships ─────────── */
    public function queue()      { return $this->belongsTo(Queue::class); }
    public function department() { return $this->belongsTo(Department::class); }

    public function visit()      { return $this->hasOne(Visit::class); }
    public function submission()
{
    return $this->belongsTo(OpdSubmission::class);
}

public function patient()
{
    return $this->belongsTo(Patient::class);
}


}
