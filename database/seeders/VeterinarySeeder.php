<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class VeterinarySeeder extends CsvSeeder
{
    public function __construct()
    {
        $this->table = 'veterinaries';
        $this->filename = base_path() . '/database/seeders/csv/list_veterinary.csv';
        $this->should_trim = true;
        $this->timestamps = true;
        $this->created_at = now();
        $this->updated_at = now();
    }
    public function run()
    {
        parent::run();
    }
}
