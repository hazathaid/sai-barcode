<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $classes = [
            ['name' => 'Kelas A', 'code' => 'A'],
            ['name' => 'Kelas B', 'code' => 'B'],
            ['name' => 'Kelas C', 'code' => 'C'],
        ];

        foreach ($classes as $class) {
            ClassRoom::create($class);
        }
    }

}
