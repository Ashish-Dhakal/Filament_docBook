<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use Carbon\Carbon;
use App\Models\User;
use Filament\Tables;
use Filament\Actions;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use Filament\Tables\Table;
use App\Models\Appointment;
use App\Models\PatientHistory;
use App\Service\ReviewService;
use App\Models\AppointmentSlot;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\DB;
use App\Service\AppointmentService;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\NotificationType;
use Filament\Tables\Columns\TextInputColumn;
use Symfony\Component\HttpFoundation\Response;
use App\Filament\Resources\AppointmentResource;
use Filament\Tables\Actions\Action as TableAction;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    /**
     * Get the header actions. 
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Get the user ID based on their role 
     */

    public function userId()
    {
        if (Auth::user()->roles == 'doctor') {
            $userId =  Auth::user()->id;
            $doctorId = Doctor::where('user_id', $userId)->first();
            return $doctorId->id;
        } elseif (Auth::user()->roles == 'patient') {
            $userId =  Auth::user()->id;
            $patientId = Patient::where('user_id', $userId)->first();
            return $patientId->id;
        }
    }

    /**
     * Define the table columns.
     */
    public function getTabs(): array
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Initialize the tabs array
        $tabs = [];

        if ($user->roles == 'doctor') {
            // Get the doctor's ID based on the user
            $doctorId = $this->userId();

            // Define tabs for doctor
            $tabs = [
                'All' => Tab::make()
                    ->badge(Appointment::where('doctor_id', $doctorId)->count()),

                'Pending' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('doctor_id', $doctorId)->where('status', 'pending'))
                    ->badge(Appointment::where('doctor_id', $doctorId)->where('status', 'pending')->count()),

                'Completed' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('doctor_id', $doctorId)->where('status', 'completed'))
                    ->badge(Appointment::where('doctor_id', $doctorId)->where('status', 'completed')->count()),

                'Booked' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('doctor_id', $doctorId)->where('status', 'booked'))
                    ->badge(Appointment::where('doctor_id', $doctorId)->where('status', 'booked')->count()),
            ];
        } elseif ($user->roles == 'patient') {
            // Get the patient's ID based on the user
            $patientId = $this->userId();

            // Define tabs for patient
            $tabs = [
                'All' => Tab::make()
                    ->badge(Appointment::where('patient_id', $patientId)->count()),

                'Pending' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('patient_id', $patientId)->where('status', 'pending'))
                    ->badge(Appointment::where('patient_id', $patientId)->where('status', 'pending')->count()),

                'Completed' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('patient_id', $patientId)->where('status', 'completed'))
                    ->badge(Appointment::where('patient_id', $patientId)->where('status', 'completed')->count()),

                'Booked' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('patient_id', $patientId)->where('status', 'booked'))
                    ->badge(Appointment::where('patient_id', $patientId)->where('status', 'booked')->count()),
            ];
        } elseif ($user->roles == 'admin') {
            // Admin can see all appointments
            $tabs = [
                'All' => Tab::make()
                    ->badge(Appointment::count()),

                'Pending' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending'))
                    ->badge(Appointment::where('status', 'pending')->count()),

                'Completed' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'completed'))
                    ->badge(Appointment::where('status', 'completed')->count()),

                'Booked' => Tab::make()
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'booked'))
                    ->badge(Appointment::where('status', 'booked')->count()),
            ];
        }

        return $tabs;
    }

    /**
     * Configure the table fields and actions.  
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.user.name')
                    ->sortable()
                    ->label('Patient Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->sortable()
                    ->label('Doctor Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Appointment Status')
                    ->formatStateUsing(function ($state) {
                        // Return the state for display
                        return ucfirst($state);
                    })
                    ->searchable()
                    ->badge()
                    ->color(function ($state) {

                        return match ($state) {
                            'pending' => 'danger',
                            'completed' => 'success',
                            'booked' => 'primary',
                            default => 'secondary', // Optional default case
                        };
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('status', 'asc')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                if ($user->hasRole('admin')) {
                    return $query;
                }
                if ($user->hasRole('doctor')) {
                    return $query->where('doctor_id', $user->doctor->id);
                }
                if ($user->hasRole('patient')) {
                    return $query->where('patient_id', $user->patient->id);
                }
                return $query->whereRaw('1 = 0');
            })
            ->filters([
                // Add any filters if needed
            ])
            ->actions([
                ActionGroup::make([

                    TableAction::make('updateStatus')
                        ->label('Update Status')
                        ->form([
                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'booked' => 'Booked',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                        ])
                        ->action(function (Appointment $record, array $data): void {
                            $status = $data['status'];
                            $this->updateStatus($status, $record);
                        })
                        ->icon('heroicon-o-arrow-path')
                        ->hidden(fn(Appointment $record) => $record->status === 'completed')
                        // Visible for admin only
                        ->visible(fn() => Auth::user()->hasRole('admin')),

                    TableAction::make('giveReview')
                        ->label('Give Review')
                        ->form([
                            // TextInput::make('appointment_id')
                            //     ->label('Appointment')
                            //     ->required()
                            //     ->default(fn(Appointment $record) => $record->id),

                            // TextInput::make('appointment_id')
                            //     ->label('Appointment')
                            //     ->required()
                            //     ->default(fn(Appointment $record) => $record->id)
                            //     ->readonly()  // Makes the input field readonly
                            //     ->helperText(fn(Appointment $record) => 'Patient: ' . $record->patient->user->name . ', Doctor: ' . $record->doctor->user->name),  // Hint to display patient and doctor names

                            TextInput::make('appointment_id')
                                ->label('Appointment')
                                ->required()
                                ->default(fn(Appointment $record) => $record->id)
                                ->hidden()  // Hides the input field from the user
                                ->helperText(fn(Appointment $record) => 'Patient: ' . $record->patient->user->name . ', Doctor: ' . $record->doctor->user->name), // Display the names in the hint


                            TextInput::make('review')
                                ->label('Review')
                                ->required(),

                            FileUpload::make('pdf')
                                ->label('PDF')
                                ->required()
                                ->acceptedFileTypes(['application/pdf']),
                        ])
                        ->action(function (Appointment $record, array $data): void {
                            $this->addReview($data, $record);
                        })
                        ->icon('heroicon-o-arrow-path')
                        // Visible only if appointment status is 'completed' and user is a doctor
                        ->visible(fn(Appointment $record) => $record->status === 'booked' && Auth::user()->hasRole('doctor')),

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->hidden(fn(Appointment $record) => $record->status === 'completed'),

                ])
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Optional: Set a color for the action
                ]),
                BulkAction::make('booked_all')
                    ->label('Booked All')
                    ->action(function ($records) {
                        foreach ($records as $record) {
                            $status = 'booked';
                            $this->updateStatus($status, $record);
                        }
                    })
                    ->icon('heroicon-o-check-circle') // Optional: Set an icon for the action
                    ->color('info')
                    ->visible(fn() => Auth::user()->roles === 'admin'),

            ]);
    }
    /**
     * Info list for the appointment details. 
     */

    public function infolist(Infolist $infolist): Infolist

    {
        return $infolist
            ->schema([
                Section::make('State Details')
                    ->schema([
                        TextEntry::make('patient.user.name')->label('Patient Name'),
                        TextEntry::make('doctor.user.name')->label('Doctor Name'),
                    ])->columns(2),
                Section::make('Appointment Details')
                    ->schema([
                        TextEntry::make('status')->label('Appointment Status')
                            //based on appointmetn status change the color of the badge
                            ->color(function ($state) {
                                return match ($state) {
                                    'pending' => 'danger',
                                    'completed' => 'success',
                                    'booked' => 'primary',
                                    default => 'secondary', // Optional default case
                                };
                            }),
                        TextEntry::make('date')->label('Date'),
                        TextEntry::make('start_time')->label('Start Time'),
                        TextEntry::make('end_time')->label('End Time'),
                    ])->columns(4),


            ]);
    }

    /**
     * Update the status of an appointment. 
     */
    public function updateStatus(string $status, Appointment $appointment)
    {
        $service = new AppointmentService();
        $response = $service->updateStatus($status, $appointment);

        if ($response) {
            Notification::make()
                ->title('Error')
                ->body($response)
                ->danger()
                ->send();
        } else {
            Notification::make()
                ->title('Success')
                ->body('Appointment status updated successfully')
                ->success()
                ->send();
        }
    }

    public function addReview(array $data, Appointment $appointment)
    {
        // dd('sdfsdf');
        $service = new ReviewService();
        $response = $service->addReview($data, $appointment);
        if ($response) {
            Notification::make()
                ->title('Error')
                ->body($response)
                ->danger()
                ->send();
        } else {
            Notification::make()
                ->title('Success')
                ->body('Review added successfully')
                ->success()
                ->send();
        }
    }
}
