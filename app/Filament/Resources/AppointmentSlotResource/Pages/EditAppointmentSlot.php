<?php

namespace App\Filament\Resources\AppointmentSlotResource\Pages;

use App\Filament\Resources\AppointmentSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAppointmentSlot extends EditRecord
{
    protected static string $resource = AppointmentSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
