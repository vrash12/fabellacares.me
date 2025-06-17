<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Adjust these as needed to match your database columns.
     */
    protected $fillable = [
        'user_id',
        'name',
        'birth_date',
        'contact_no',
        'address',
    ];

    
    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * ─── Relationships ───
     */

    /**
     * A Patient belongs to a User (credentials, email, password, etc.).
     */

     public function submissions(): HasMany
{
    return $this->hasMany(OpdSubmission::class, 'patient_id');
}

public function tokens(): HasManyThrough
{
    return $this->hasManyThrough(
        Token::class,
        OpdSubmission::class,
        'patient_id',      // foreign on submissions
        'submission_id',   // foreign on tokens
        'id',              // local key on patients
        'id'               // local key on submissions
    );
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A Patient has one Profile (sex, religion, parental info, etc.).
     */
    public function profile()
    {
        return $this->hasOne(PatientProfile::class);
    }

     public function highRiskSubmissions(): HasMany
    {
        return $this->hasMany(OpdSubmission::class)
                    ->whereHas('form', fn ($q) => $q->where('form_no', 'OPD-F-09'))
                    ->latest();
    }

public function visits()
{
    return $this->hasMany(PatientVisit::class);
}

}
