<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FoodPreference;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::create([
            'email' => 'athatsaaqif@gmail.com',
            'name' => 'Atha Tsaqif',
            'username' => 'qif',
            'password' => Hash::make('password'),
            'avatar' => 'user_avatars/default.jpg',
        ]);

        $this->call([
            CategorySeeder::class,
        ]);

        $categoryIds = Category::pluck('id')->take(3);

        $user->preferences()->sync($categoryIds);
    }
}
