<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'parenting'],
            ['name' => 'parenting', 'slug' => 'parenting']
        );

        $starts = now()->addDays(7);
        $ends = $starts->copy()->addHours(3);

        $category->events()->create([
            'name' => 'Parenting Akbar 2026',
            'slug' => 'parenting-akbar-2026',
            'starts_at' => $starts,
            'ends_at' => $ends,
            'location' => 'Sukabumi',
            'status' => 'published',
        ]);
    }
}
