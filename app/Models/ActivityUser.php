<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ActivityUser extends Pivot
{
    use HasFactory;

     /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
    

     public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

     public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }
}
