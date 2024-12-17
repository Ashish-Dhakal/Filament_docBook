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
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                    ]),
            ]);
      
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.user.name')
                    ->sortable()
                    ->label('Patient Name')
                    ->searchable(),
                // ->visible(fn ($record) => Auth::user()->roles === 'patient'),
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->sortable()
                    ->label('Doctor Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('status'),
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
                // Get the currently authenticated user
                $user = User::find(Auth::user()->id);
                // $user = User::find(Filament::auth()->user()->id);
            // dd(Auth::user()->id);
                // If the user is an admin, they can see all appointments
                if ($user->hasRole('admin')) {
                    return $query;
                }

                // If the user is a doctor, only their appointments are shown
                if ($user->hasRole('doctor')) {
                    return $query->where('doctor_id', $user->doctor->id);
                }

                // If the user is a patient, only their appointments are shown
                if ($user->hasRole('patient')) {

                    if ($user->patient) {
                        return $query->where('patient_id', $user->patient->id);
                    } else {
                        return $query->whereRaw('1 = 0'); // If no patient relationship, show no appointments
                    }
                }

                // Default to no appointments
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
                
                        Tables\Actions\DeleteBulkAction::make()
                        // ->visible(
                        //     fn () => Auth::user()->roles === 'admin'
                        // )
                    
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Add relations if needed
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
