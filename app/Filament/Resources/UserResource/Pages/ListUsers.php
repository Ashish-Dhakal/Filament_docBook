<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;



class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()
                ->badge(User::count()),
            'Doctors' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('roles', 'doctor'))
                ->badge(User::where('roles', 'doctor')->count()),
            'Patients' => Tab::make()
                ->modifyQueryUsing(fn(Builder $query) => $query->where('roles', 'patient'))
                ->badge(User::where('roles', 'patient')->count()),
        ];
    }
}
