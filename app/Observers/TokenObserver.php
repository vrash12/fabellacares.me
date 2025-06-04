<?php
// app/Observers/TokenObserver.php
namespace App\Observers;

use App\Models\Token;
use App\Models\Visit;

class TokenObserver
{
  
   public function updated(Token $token): void
    {
        // 1) Did served_at just go from NULL → some timestamp?
        if (! $token->isDirty('served_at') || $token->served_at === null) {
            return;
        }

        // 2) If you only want to log “real patient” visits, skip admin/encoder tokens:
        if ($token->patient_id === null) {
            return;
        }

        // 3) Only create a Visit if one doesn’t exist already
        if ($token->visit()->exists()) {
            return;
        }

        Visit::create([
            'token_id'      => $token->id,
            'patient_id'    => $token->patient_id,    // must be non-null or DB will reject
            'department_id' => $token->queue_id,      // ← use queue_id as department
            'queue_id'      => $token->queue_id,
            'visited_at'    => $token->served_at,
        ]);
    }
}
