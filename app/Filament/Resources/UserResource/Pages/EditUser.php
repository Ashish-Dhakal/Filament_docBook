<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;



    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Fetch the user record, ensure it exists
        $user = User::find($record->id);
        if (!$user) {
            // Handle the case where the user does not exist, e.g., throw an exception
            throw new \Exception('User not found.');
        }

        // Update the common user attributes
        $user->name = $data['name'] ?? $user->name;
        $user->email = $data['email'] ?? $user->email;
        $user->password = isset($data['password']) ? ($data['password']) : $user->password;
        $user->phone = $data['phone'] ?? $user->phone;
        $user->address = $data['address'] ?? $user->address;
        $user->age = $data['age'] ?? $user->age;
        $user->blood_group = $data['blood_group'] ?? $user->blood_group;
        $user->gender = $data['gender'] ?? $user->gender;
        $user->roles = $data['roles'] ?? $user->roles;

        // If the role is 'doctor', update doctor-specific fields
        if ($user->roles === 'doctor') {
            if (isset($data['doctor']['speciality_id']) && isset($data['doctor']['hourly_rate'])) {
                // Update the related doctor record
                $doctor = $user->doctor; // Assuming the 'doctor' relationship is defined on the User model

                if (!$doctor) {
                    // If there's no doctor record, create a new one
                    $doctor = new Doctor();
                    $doctor->user_id = $user->id; // Ensure the user_id is set correctly
                }

                // Update doctor-specific fields
                $doctor->speciality_id = $data['doctor']['speciality_id'];
                $doctor->hourly_rate = $data['doctor']['hourly_rate'];

                // Save the doctor record
                $doctor->save();
            } else {
                // Handle missing doctor-specific data
                throw new \Exception('Doctor-specific data is missing.');
            }
        }

        $user->save();

        return $user;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
      $userId=$data["id"];
      if($data["roles"]=='doctor'){
        $doctor = Doctor::where('user_id', $userId)->first();
        $data["doctor"]["speciality_id"]=$doctor->speciality_id;
        $data["doctor"]["hourly_rate"]=$doctor->hourly_rate;
      }
    
        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
