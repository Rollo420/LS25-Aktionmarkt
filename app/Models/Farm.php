<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Parental\HasParent;

class Farm extends User
{
    /** @use HasFactory<\Database\Factories\FarmFactory> */
    use HasFactory, HasParent;

    protected $table = 'users';

    public function users()
    {
        return $this->belongsToMany(User::class, 'farm_user', 'farm_id', 'user_id')
            ->withPivot('invite_acception', 'user_role_id')
            ->withTimestamps();
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
