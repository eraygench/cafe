<?php

namespace Database\Seeders;

use App\Models\Desk;
use App\Models\Organization;
use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class SectionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Collection::times(3, fn ($section) => Section::factory()
            ->has(Desk::factory()
                ->count($desks = rand(5, 10))
                ->state(new Sequence(...Collection::times($desks, fn ($desk) => [
                    'name' => $desk,
                    'organization_id' => Organization::query()->first()->id,
                ]))),
                "desks")
            ->state(new Sequence(fn () => [
                'organization_id' => Organization::query()->first()->id
            ]))->create());
    }
}
