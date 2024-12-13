<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;


class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

     // Override to handle the record creation process
     protected function handleRecordCreation(array $data): Model
     {
         // First, create the user record with common data
         $user = User::create([
             'name' => $data['name'],
             'email' => $data['email'],
             'password' => bcrypt($data['password']),
             'phone' => $data['phone'],
             'address' => $data['address'],
             'age' => $data['age'],
             'blood_group' => $data['blood_group'],
             'gender' => $data['gender'],
             'roles' => $data['roles'], 
         ]);
 
         // Now, depending on the role, create a related Patient or Doctor record
         if ($data['roles'] === 'patient') {
             // Create a Patient record and store only the user_id
             $user->roles->$data['roles'];
             Patient::create([
                 'user_id' => $user->id, // Save user_id in the patient table
             ]);
         }
 
         if ($data['roles'] === 'doctor') {
             // Create a Doctor record and store only the user_id
           
             Doctor::create([
                 'user_id' => $user->id, // Save user_id in the doctor table
                 'speciality_id' => $data['speciality_id'],
                 'hourly_rate' => $data['hourly_rate'],
             ]);
         }
 
         // Return the created user (or you can return the related model like Patient/Doctor)
         return $user;
     }


}
