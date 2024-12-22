<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn; // Add this import
use Carbon\Carbon;

class UpCommingAppointment extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        $today = Carbon::today();
        $nextMonth = Carbon::today()->addMonth(30);
        
        $user = auth()->user();  // Get the currently authenticated user
    
        // Base query to filter appointments within the next week
        $query = Appointment::query()
            ->whereBetween('date', [$today, $nextMonth]);
    
        // If the user is an admin, show all appointments
        if ($user->hasRole('admin')) {
            // No additional filtering needed for admins
        } 
        // If the user is a doctor, filter appointments where the doctor is associated
        elseif ($user->hasRole('doctor')) {
            $query->where('doctor_id', $user->doctor->id);  // Assuming the user has a `doctor` relationship
        } 
        // If the user is a patient, filter appointments where the patient is associated
        elseif ($user->hasRole('patient')) {
            $query->where('patient_id', $user->patient->id);  // Assuming the user has a `patient` relationship
        }
    
        return $table
            ->query($query)
            ->columns([
                TextColumn::make('patient.user.name')
                    ->label('Patient Name')
                    ->searchable(),
                TextColumn::make('doctor.user.name')
                    ->label('Doctor Name'),
                TextColumn::make('date'),
                TextColumn::make('start_time'),
                TextColumn::make('status'),
            ]);
    }
}
