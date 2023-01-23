<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Organization;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Collection::times(5, fn ($category) => Category::factory()
            ->has(Product::factory()
                ->count($products = rand(1, 4))
                ->state(new Sequence(...Collection::times($products, fn ($product) => [
                    'organization_id' => Organization::query()->first()->id,
                ]))),
                "products")
            ->state(new Sequence(fn () => [
                'organization_id' => Organization::query()->first()->id
            ]))->create());
    }
}
