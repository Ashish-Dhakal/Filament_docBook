<?php

namespace App\Filament\Resources\AppointmentSlotResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\AppointmentSlot;
use App\Service\AppointmentSlotService;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\AppointmentSlotResource;

class CreateAppointmentSlot extends CreateRecord
{
    protected static string $resource = AppointmentSlotResource::class;


    protected function handleRecordCreation(array $data): Model
    {
        // Use the AppointmentSlotService to handle the logic
        $service = new AppointmentSlotService();

        // Call the createAppointmentSlot method from the service to handle the logic
        try {
            $appointmentSlot = $service->createAppointmentSlot($data);
            Notification::make()
            ->title('AppointmentSlot Created')
            ->success()
            ->body('The appointmentSlot has been successfully created.')
            ->send();
        } catch (ValidationException $e) {
            Notification::make()
            ->title('Error Creating AppointmentSlot')
            ->danger()
            ->body($e->getMessage())
            ->send();
            throw $e; // Re-throw the validation exception to propagate it
        }

        return $appointmentSlot;
    }
}
