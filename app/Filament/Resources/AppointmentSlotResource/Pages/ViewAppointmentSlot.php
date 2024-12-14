<?php

namespace App\Filament\Resources\AppointmentSlotResource\Pages;

use App\Filament\Resources\AppointmentSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAppointmentSlot extends ViewRecord
{
    protected static string $resource = AppointmentSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
