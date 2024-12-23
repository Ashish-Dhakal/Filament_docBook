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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->afterStateUpdated(function ($state, $component) {
                                // Emit the doctor selection event
                                $component->getLivewire()->dispatch('doctorSelected', [
                                    'context' => 'appointment_form',
                                    'doctor_id' => $state,
                                ]);

                                // self::fetchDoctorInfo($state);
                            })
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
                    Section::make([
                        Forms\Components\Placeholder::make('doctor_info')
                            ->label('Doctor Information')
                            ->content(function ($state, $component) {
                                $doctorInfo = $component->getLivewire()->doctorInfo;

                                if (empty($doctorInfo)) {
                                    return 'Select a doctor to view information';
                                }


                                $output = '';
                                foreach ($doctorInfo as $info) {
                                    if (isset($info['message'])) {
                                        $output =  $info['message'];
                                    } else {
                                        // Format the date and times to a more readable format
                                        $formattedDate = \Carbon\Carbon::parse($info['date'])->format('d M Y');
                                        $formattedStartTime = \Carbon\Carbon::parse($info['start_time'])->format('H:i');
                                        $formattedEndTime = \Carbon\Carbon::parse($info['end_time'])->format('H:i');

                                        $output .= 'Status: ' . ucfirst($info['status']);
                                        $output .= 'Date: ' . $formattedDate;
                                        $output .= 'Start Time: ' . $formattedStartTime;
                                        $output .= 'End Time: ' . $formattedEndTime;
                                    }
                                }

                                return $output;
                            })

                    ])->grow(false),
                ])->hiddenOn('edit'),
            ]);
    }


    // public static function fetchDoctorInfo($doctorId)
    // {
    //   dd($doctorId);
    // }
    



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
            // 'view' => Pages\ViewAppointment::route('/{record}'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }
}
