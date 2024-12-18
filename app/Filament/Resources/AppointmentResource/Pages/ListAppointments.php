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
use App\Models\AppointmentSlot;
use App\Service\AppointmentService;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\NotificationType;
use Symfony\Component\HttpFoundation\Response;
use App\Filament\Resources\AppointmentResource;





class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

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
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'booked' => 'Booked',
                        'cancelled' => 'Cancelled',
                    ])
                    ->afterStateUpdated(fn($state, $record) => $this->updateStatus($state, $record)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


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
}
