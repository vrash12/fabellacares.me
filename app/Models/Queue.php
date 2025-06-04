<?php
// app/Models/Queue.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    // Make token_counter mass-assignable so that $queue->update(['token_counter' => …]) works
    protected $fillable = [
        'name',
        'parent_id',
        'token_counter',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    public function nextPendingToken()
    {
        return $this->hasOne(Token::class)
                    ->whereNull('served_at')
                    ->orderBy('created_at', 'asc');
    }
}
