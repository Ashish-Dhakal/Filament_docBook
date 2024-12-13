<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Speciality;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Ashish Dhakal',
            'email' => 'ashish@test.com',
            'roles' => 'admin',
            'gender' => 'male',
            'phone' => '9861234567',
            'address' => 'Pokhara',
            'age' => 30,
            'blood_group' => 'O+',
            'password' => bcrypt('password')
        ]);

        $doctor = User::create([
            'name' => 'Ashish Doctor',
            'email' => 'doctor@test.com',
            'roles' => 'doctor',
            'gender' => 'male',
            'age' => 30,
            'phone' => '9861234567',
            'blood_group' => 'O+',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address' => 'Kathmandu',
            'email_verified_at' => now(),

        ]);

        $speciality = Speciality::create([
            'name' => 'Cardiology',
        ]);

        Doctor::create([
            'user_id' => $doctor->id,
            'experience' => 10,
            'qualification' => 'MBBS',
            'speciality_id' => $speciality->id, 
            'department' => 'Cardiology',
            'hourly_rate' => 1000,
        ]);


        $patient = User::create([
            'name' => 'Ashish Patient',
            'email' => 'patient@test.com',
            'roles' => 'patient',
            'gender' => 'male',
            'phone' => '9861234567',
            'blood_group' => 'O+',
            'age' => 30,
            'password' => bcrypt('password'),
            'phone' => '1234567890',
            'address' => 'Kathmandu',
            'email_verified_at' => now(),
        ]);

        Patient::create([
            'user_id' => $patient->id,
        ]);

    }
}
