<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'token_id',
        'patient_id',
        'visited_at',
        'department_id',
        'queue_id',
    ];

    // ← Add this:
    protected $casts = [
        'visited_at' => 'datetime',
    ];

    // Relationships:
    public function patient()
    {
        return $this->belongsTo(\App\Models\Patient::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function queue()
    {
        return $this->belongsTo(\App\Models\Queue::class);
    }

    public function token()
    {
        return $this->belongsTo(\App\Models\Token::class);
    }

    // Optional alias if you still want to use `$v->dept`:
    public function dept()
    {
        return $this->department();
    }
}
