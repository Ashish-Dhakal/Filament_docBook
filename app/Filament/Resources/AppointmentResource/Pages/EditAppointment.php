<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use Filament\Actions;
use App\Service\AppointmentService;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\AppointmentResource;

class EditAppointment extends EditRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
