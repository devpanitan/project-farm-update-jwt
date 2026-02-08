<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_roles')->updateOrInsert(
            ['id' => 1],
            ['role_name' => 'Super Admin', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('user_roles')->updateOrInsert(
            ['id' => 2],
            ['role_name' => 'Farm Owner', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('user_roles')->updateOrInsert(
            ['id' => 3],
            ['role_name' => 'Farm Worker', 'created_at' => now(), 'updated_at' => now()]
        );
    }
}
