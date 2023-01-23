<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::factory()
            ->has(User::factory()
                ->state(new Sequence(fn () => [
                    'name' => 'Test User',
                    'email' => 'test@test',
                    'password' => Hash::make('test')
                ])),
                "users")
            ->state(new Sequence(fn () => [
                'name' => 'Test'
            ]))->create();

        User::factory()->create([
            'name' => 'Root User',
            'email' => 'root@root',
            'password' => Hash::make('root'),
            'is_admin' => true
        ]);

        $this->call([
            CategorySeeder::class,
            SectionSeeder::class
        ]);
    }
}
