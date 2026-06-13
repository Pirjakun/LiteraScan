<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Student;
use App\Models\Book;

class DataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Students
        Student::updateOrCreate(
            ['rfid_uid' => 'A2 42 E2 2E'],
            [
                'nim' => '103032330001',
                'name' => 'Ayu',
                'major' => 'Information Technology'
            ]
        );

        Student::updateOrCreate(
            ['rfid_uid' => 'C6 A5 E2 2E'],
            [
                'nim' => '103032330002',
                'name' => 'Budi',
                'major' => 'Information Technology'
            ]
        );

        Student::updateOrCreate(
            ['rfid_uid' => '5F E5 97 C2'],
            [
                'nim' => '103032330003',
                'name' => 'Pirja_Admin',
                'major' => 'Information Technology'
            ]
        );

        // Seed Book
        Book::updateOrCreate(
            ['rfid_uid' => '04 E5 D7 08 C1 2A 81'],
            [
                'title' => 'Tips & Trick Excel',
                'author' => 'Microsoft Press',
                'status' => 'available'
            ]
        );
    }
}
