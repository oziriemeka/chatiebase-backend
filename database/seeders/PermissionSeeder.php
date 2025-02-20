<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Can join chat', 'group' => 'Chat'],
            ['name' => 'Can assign chat', 'group' => 'Chat'],
            ['name' => 'Can add team member', 'group' => 'Team'],
            ['name' => 'Can delete team member', 'group' => 'Team'],
            ['name' => 'Can update team member', 'group' => 'Team'],
            ['name' => 'Can update settings', 'group' => 'Settings'],
            ['name' => 'Can update widget', 'group' => 'Widget'],
            ['name' => 'Can customize widgets', 'group' => 'Widget'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}
