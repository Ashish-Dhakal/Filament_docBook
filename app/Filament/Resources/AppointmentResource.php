<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Patient;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Actions\RestoreAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Filament\Resources\AppointmentResource\RelationManagers\ReviewsRelationManager;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Appointments Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->options(Patient::with('user')->get()->pluck('user.name', 'id'))
                    ->label('Patient Name')
                    ->rules('exists:patients,id')
                    ->required(),
                Forms\Components\Select::make('doctor_id')
                    ->options(Doctor::with('user')->get()->pluck('user.name', 'id'))
                    ->label('Doctor')
                    ->rules('exists:doctors,id')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\TimePicker::make('start_time')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->required(),
                // Forms\Components\Select::make('status')
                //     ->required()
                //     ->options([
                //         'pending' => 'Pending',
                //     ]),

                Forms\Components\Select::make('status')
                    ->required()
                    ->options(function () {
                        // Determine options based on the authenticated user's role
                        if (Auth::user()->roles === 'admin') {
                            return [
                                'booked' => 'Booked',
                            ];
                        } elseif (Auth::user()->roles === 'patient') {
                            return [
                                'pending' => 'Pending',
                            ];
                        }
                    })
                    ->default(function () {
                        // Automatically set default value based on the user's role
                        return Auth::user()->roles === 'admin' ? 'booked' : 'pending';
                    })
                    ->hidden(fn () => Auth::user()->roles === 'patient'),

            ]);
    }

  

    public static function getRelations(): array
    {
        return [
            ReviewsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
