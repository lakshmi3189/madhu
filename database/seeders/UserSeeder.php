<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Aadrika',
            'email' => 'aadrika@gmail.com',
            'password' => Hash::make('123'),
            'c_password' => '123',
            'ip_address' => '127.0.0.1'
        ]);
    }
}
