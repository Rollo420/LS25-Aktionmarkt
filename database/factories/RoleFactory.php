<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = \App\Models\Role::class;

    public function definition()
    {
        static $index = 0;
        $roles = ['admin', 'bank', 'farmer', 'forst'];
        $role = $roles[$index % count($roles)];
        $index++;
        return [
            'name' => $role,
        ];
    }
}
