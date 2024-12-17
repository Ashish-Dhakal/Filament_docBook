<?php

namespace App\Filament\Resources\AppointmentSlotResource\Pages;

use Filament\Actions;
use App\Service\AppointmentSlotService;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\AppointmentSlotResource;
use Illuminate\Validation\ValidationException;



class EditAppointmentSlot extends EditRecord
{
    protected static string $resource = AppointmentSlotResource::class;
    
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Use the AppointmentSlotService to handle the logic
        $service = new AppointmentSlotService();

        // Call the createAppointmentSlot method from the service to handle the logic
        try {
            $appointmentSlot = $service->createAppointmentSlot($data);
        } catch (ValidationException $e) {
            throw $e; // Re-throw the validation exception to propagate it
        }

        return $appointmentSlot;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
