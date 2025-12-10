<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            'default user', 'admin', 'bank', 'farmer', 'forst', 'farm admin', 'farm manager', 'lohnnternehmer', 'landwirt', 'ackerbauer', 'tierbauer'
                
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}
