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
use App\Filament\Resources\AppointmentResource\RelationManagers\PaymentRelationManager;
use App\Filament\Resources\AppointmentResource\RelationManagers\ReviewsRelationManager;
use App\Models\AppointmentSlot;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-pause-circle';

    protected static ?string $navigationGroup = 'Appointments Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Split::make([
                    Section::make([
                        Forms\Components\Select::make('patient_id')
                            ->options(Patient::with('user')->get()->pluck('user.name', 'id'))
                            ->label('Patient Name')
                            ->rules('exists:patients,id')
                            ->required()
                            ->visible(Auth::user()->hasRole('admin')),

                        Forms\Components\Select::make('doctor_id')
                            ->options(Doctor::with('user')->get()->pluck('user.name', 'id'))
                            ->label('Doctor')
                            ->rules('exists:doctors,id')
                            ->required()
                            ->reactive()
                            ->default(fn ($record) => $record ? $record->doctor_id : null),  
                            

                        Forms\Components\DatePicker::make('date')
                            ->minDate(now()->toDateString())
                            ->required(),

                        Forms\Components\TimePicker::make('start_time')
                            ->required(),

                        Forms\Components\TimePicker::make('end_time')
                            ->after('start_time')
                            ->required(),
                    ]),
                ])->from('lg')
               ,

                Split::make([


                    Forms\Components\Placeholder::make('schedules')
                    ->label('Schedules')
                    ->content(function ($get) {
                        $doctorId = $get('doctor_id');
                        if (!$doctorId) {
                            return 'No doctor selected.';
                        }

                        $schedules = AppointmentSlot::where('doctor_id', $doctorId)->get();

                        if ($schedules->isEmpty()) {
                            return 'No schedules available for this doctor.';
                        }

                        $schedulesData = $schedules->map(function ($schedule) {
                            return [
                                'date' => $schedule->date,
                                'start_time' => $schedule->start_time,
                                'end_time' => $schedule->end_time,
                                'status' => $schedule->status,
                            ];
                        })->values()->toArray();

                        return view('filament.forms.components.list', [
                            'columns' => ['day', 'time', 'status'],
                            'rows' => $schedulesData,
                        ]);
                    })
                    ->columnSpanFull(),
                
                    
                ]),
            ]);
    }


    // public static function fetchDoctorInfo($doctorId)
    // {
    //   dd($doctorId);
    // }
    



    public static function getRelations(): array
    {
        return [
            ReviewsRelationManager::class,
            PaymentRelationManager::class,
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
