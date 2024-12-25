<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Service\AppointmentService;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AppointmentResource;
use App\Models\AppointmentSlot;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;
use Livewire\WithFileUploads;


class CreateAppointment extends CreateRecord
{
    // use WithFileUploads;

    protected static string $resource = AppointmentResource::class;
    protected function handleRecordCreation(array $data): Model
    {
        $service = new AppointmentService();

        try {
            $appointment = $service->createAppointment($data);
            // Success notification
            Notification::make()
                ->title('Appointment Created')
                ->success()
                ->body('The appointment has been successfully created.')
                ->send();

            return $appointment;
        } catch (ValidationException $e) {
            // Error notification
            Notification::make()
                ->title('Error Creating Appointment')
                ->danger()
                ->body($e->getMessage())
                ->send();

            throw $e; // Propagate the exception
        }
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
