<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Parental\HasParent;

class Farm extends Model
{
    /** @use HasFactory<\Database\Factories\FarmFactory> */
    use HasFactory;
    use HasParent;

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'farm_user', 'farm_id', 'user_id')->withTimestamps();
    }
}
