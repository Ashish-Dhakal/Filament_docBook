<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use Filament\Actions;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AppointmentResource;


class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function userId()
    {
        if (Auth::user()->roles == 'doctor') {
            $userId =  Auth::user()->id;
            $doctorId = Doctor::where('user_id', $userId)->first();
            return $doctorId->id;
        } elseif (Auth::user()->roles == 'patient') {
            $userId =  Auth::user()->id;
            $patientId = Patient::where('user_id', $userId)->first();
            return $patientId->id;
        }
    }

    // public function getTabs(): array
    // {
    //     return [
    //         'All' => Tab::make()
    //             ->badge(Appointment::count()),
    //         'Pending' => Tab::make()
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
    //             ->badge(Appointment::where('status', 'pending')->count()),
    //         'Completed' => Tab::make()
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
    //             ->badge(Appointment::where('status', 'completed')->count()),
    //         'Booked' => Tab::make()
    //             ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'booked'))
    //             ->badge(Appointment::where('status', 'booked')->count()),
    //     ];
    // }

    public function getTabs(): array
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Initialize the tabs array
        $tabs = [];

        if ($user->roles == 'doctor') {
            // Get the doctor's ID based on the user
            $doctorId = $this->userId();

            // Define tabs for doctor
            $tabs = [
                'All' => Tab::make()
                    ->badge(Appointment::where('doctor_id', $doctorId)->count()),

                'Pending' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('doctor_id', $doctorId)->where('status', 'pending'))
                    ->badge(Appointment::where('doctor_id', $doctorId)->where('status', 'pending')->count()),

                'Completed' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('doctor_id', $doctorId)->where('status', 'completed'))
                    ->badge(Appointment::where('doctor_id', $doctorId)->where('status', 'completed')->count()),

                'Booked' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('doctor_id', $doctorId)->where('status', 'booked'))
                    ->badge(Appointment::where('doctor_id', $doctorId)->where('status', 'booked')->count()),
            ];
        } elseif ($user->roles == 'patient') {
            // Get the patient's ID based on the user
            $patientId = $this->userId();

            // Define tabs for patient
            $tabs = [
                'All' => Tab::make()
                    ->badge(Appointment::where('patient_id', $patientId)->count()),

                'Pending' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('patient_id', $patientId)->where('status', 'pending'))
                    ->badge(Appointment::where('patient_id', $patientId)->where('status', 'pending')->count()),

                'Completed' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('patient_id', $patientId)->where('status', 'completed'))
                    ->badge(Appointment::where('patient_id', $patientId)->where('status', 'completed')->count()),

                'Booked' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('patient_id', $patientId)->where('status', 'booked'))
                    ->badge(Appointment::where('patient_id', $patientId)->where('status', 'booked')->count()),
            ];
        } elseif ($user->roles == 'admin') {
            // Admin can see all appointments
            $tabs = [
                'All' => Tab::make()
                    ->badge(Appointment::count()),

                'Pending' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                    ->badge(Appointment::where('status', 'pending')->count()),

                'Completed' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                    ->badge(Appointment::where('status', 'completed')->count()),

                'Booked' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'booked'))
                    ->badge(Appointment::where('status', 'booked')->count()),
            ];
        }

        return $tabs;
    }
}
