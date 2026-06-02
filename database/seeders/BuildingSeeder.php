<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Building;
class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data=[
            [
            'user_id' => '',
            'type'=> '',
            'level'=> '',
            'name'=> '',
            'svg_file'=> '',
            'completed_count'=> '',
            'grid_x'=> '',
            'grid_y'=> ''
            ],
            []];
        Building::create($data);
    }
}
