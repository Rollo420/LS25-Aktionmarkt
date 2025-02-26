<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class password extends Model
{
    use HasFactory;

    protected $hidden = ['id', 'created_at', 'updated_at', 'hash'];
}
