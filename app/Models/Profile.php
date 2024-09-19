<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    // Define the table if it's not the default 'profiles'
    protected $table = 'profiles';

    // Define the fields that are mass assignable
    protected $fillable = [
        'user_id',
        'full_name',
        'address',
        'gender',
        'marital_status',
    ];

    // Relationship with the User model (each profile belongs to a user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
