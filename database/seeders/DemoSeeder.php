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
            ['slug' => 'seminar'],
            ['name' => 'Seminar', 'slug' => 'seminar']
        );

        $starts = now()->addDays(7);
        $ends = $starts->copy()->addHours(3);

        $category->events()->create([
            'name' => 'Seminar Laravel QR 2026',
            'slug' => 'seminar-laravel-qr-2026',
            'starts_at' => $starts,
            'ends_at' => $ends,
            'location' => 'Jakarta',
            'status' => 'published',
        ]);
    }
}
