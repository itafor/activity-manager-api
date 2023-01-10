<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

  protected  $fillable = ['title', 'description', 'activity_date', 'image_url', 'activity_type'];

     public function users()
    {
        return $this->belongsToMany(User::class, 'activity_users');
    }
}
