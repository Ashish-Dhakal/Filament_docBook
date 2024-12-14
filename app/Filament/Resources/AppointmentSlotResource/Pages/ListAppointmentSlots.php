<?php

namespace App\Filament\Resources\AppointmentSlotResource\Pages;

use App\Filament\Resources\AppointmentSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointmentSlots extends ListRecords
{
    protected static string $resource = AppointmentSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
