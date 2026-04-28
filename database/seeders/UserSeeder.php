<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'email' => 'athatsaaqif@gmail.com',
            'name' => 'Atha Tsaqif',
            'username' => 'qif',
            'password' => Hash::make('password'),
            'avatar' => 'images/user/default.jpg',
        ]);

        $categoryIds = Category::pluck('id')->take(3);

        $user->preferences()->sync($categoryIds);
    }
}
