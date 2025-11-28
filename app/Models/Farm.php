<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasParent;

class Farm extends User
{
    /** @use HasFactory<\Database\Factories\FarmFactory> */
    use HasFactory;
    use HasParent;


    protected $table='users';

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'farm_user', 'farm_id', 'user_id')
            ->withPivot('invite_acception')   
            ->withTimestamps();
    }
}
