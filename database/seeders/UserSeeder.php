<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (config('openai.api_key')) {
            User::factory()->createOne([
                'email' => config('openai.gpt_user_email'),
                'name' => 'GPT Chatbot',
            ]);
        }

        // create 10 random users
        User::factory(10)->create();
    }
}
