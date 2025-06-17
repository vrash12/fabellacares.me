<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpdForm extends Model
{
    // ← make sure Laravel is pointed exactly at opd_forms
    protected $table = 'opd_forms';

    // your primary key is the default "id", so you can omit these if you like
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    // allow mass-assignment on these columns
    protected $fillable = [
        'name',
        'form_no',
        'department',
        'fields',
    ];

    // cast your JSON column back into an array
    protected $casts = [
        'fields' => 'array',
    ];
    public function queue()
{
    return $this->belongsTo(Queue::class);
}
}
