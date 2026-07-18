<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'full_name',
        'dob',
        'gender',
        'phone',
        'address',
    ];

    /**
     * Relationship: Profile belongs to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
