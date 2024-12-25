<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Appointment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;
use Carbon\Carbon;

class UpComingAppointment extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Upcoming Appointments';
    protected static ?string $description = 'View and manage upcoming appointments for the next 30 days';
    protected static ?string $icon = 'heroicon-o-calendar';
    protected static ?string $maxHeight = '500px';
    protected int $pageSize = 10;

    public function table(Table $table): Table
    {
        $today = Carbon::today();
        $nextMonth = Carbon::today()->addMonth(30);
        $user = auth()->user();

        $query = Appointment::query()
            ->whereBetween('date', [$today, $nextMonth])
            ->whereIn('status', ['booked', 'pending']);

        if ($user->hasRole('admin')) {
            // No additional filtering for admins
        } elseif ($user->hasRole('doctor')) {
            $query->where('doctor_id', $user->doctor->id);
        } elseif ($user->hasRole('patient')) {
            $query->where('patient_id', $user->patient->id);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('patient.user.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->extraAttributes(['class' => 'text-gray-900 dark:text-gray-100']),

                TextColumn::make('doctor.user.name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->extraAttributes(['class' => 'text-gray-900 dark:text-gray-100']),

                TextColumn::make('date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->extraAttributes(['class' => 'text-gray-600 dark:text-gray-300']),

                TextColumn::make('start_time')
                    ->label('Time')
                    ->time('h:i A')
                    ->sortable()
                    ->extraAttributes(['class' => 'text-gray-600 dark:text-gray-300']),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'booked' => 'success',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'booked' => 'heroicon-o-check-circle',
                        'pending' => 'heroicon-o-clock',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'booked' => 'Booked',
                        'pending' => 'Pending',
                    ])
                    ->label('Status')
                    ->placeholder('All Statuses'),

                SelectFilter::make('date')
                    ->options([
                        'today' => 'Today',
                        'tomorrow' => 'Tomorrow',
                        'this_week' => 'This Week',
                        'next_week' => 'Next Week',
                    ])
                    ->label('Date Range')
                    ->placeholder('All Dates')
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'today') {
                            $query->whereDate('date', Carbon::today());
                        } elseif ($data['value'] === 'tomorrow') {
                            $query->whereDate('date', Carbon::tomorrow());
                        } elseif ($data['value'] === 'this_week') {
                            $query->whereBetween('date', [Carbon::today(), Carbon::today()->endOfWeek()]);
                        } elseif ($data['value'] === 'next_week') {
                            $query->whereBetween('date', [Carbon::today()->next('Monday'), Carbon::today()->next('Monday')->endOfWeek()]);
                        }
                    }),
            ])
            ->defaultSort('date', 'asc')
            ->striped()
            ->searchable()
            ->paginated([5, 10, 25, 50])
            ->poll('30s')
            // ->extraAttributes([
            //     'class' => 'shadow-xl rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800',
            // ])
            ;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [5, 10, 25, 50];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No upcoming appointments';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Appointments will appear here when they are scheduled.';
    }
}


// <?php

// namespace App\Filament\Widgets;

// use Filament\Tables;
// use Filament\Tables\Table;
// use App\Models\Appointment;
// use Filament\Widgets\TableWidget as BaseWidget;
// use Filament\Tables\Columns\TextColumn; // Add this import
// use Carbon\Carbon;

// class UpComingAppointment extends BaseWidget
// {
//     protected int | string | array $columnSpan = 'full';

//     protected static ?int $sort = 4;

//     protected static ?string $icon = 'heroicon-o-calendar';

//     protected static ?string $heading = 'Upcoming Appointments';
//     public function table(Table $table): Table
//     {
//         $today = Carbon::today();
//         $nextMonth = Carbon::today()->addMonth(30);
        
//         $user = auth()->user();  // Get the currently authenticated user
    
//         // Base query to filter appointments within the next week
//         $query = Appointment::query()
//             ->whereBetween('date', [$today, $nextMonth])
//             ->whereIn('status', ['booked', 'pending']);
    
//         // If the user is an admin, show all appointments
//         if ($user->hasRole('admin')) {
//             // No additional filtering needed for admins
//         } 
//         // If the user is a doctor, filter appointments where the doctor is associated
//         elseif ($user->hasRole('doctor')) {
//             $query->where('doctor_id', $user->doctor->id);  // Assuming the user has a `doctor` relationship
//         } 
//         // If the user is a patient, filter appointments where the patient is associated
//         elseif ($user->hasRole('patient')) {
//             $query->where('patient_id', $user->patient->id);  // Assuming the user has a `patient` relationship
//         }
    
//         return $table
//             ->query($query)
//             ->columns([
//                 TextColumn::make('patient.user.name')
//                     ->label('Patient Name')
//                     ->searchable(),
//                 TextColumn::make('doctor.user.name')
//                     ->label('Doctor Name'),
//                 TextColumn::make('date'),
//                 TextColumn::make('start_time'),
//                 TextColumn::make('status')
//                 ->searchable(),
//             ]);
//     }
// }
