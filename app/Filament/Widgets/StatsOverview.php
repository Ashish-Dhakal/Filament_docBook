<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = true;
    protected static ?int $sort = 1;

    // Add custom styling to the widget
    protected static ?string $maxHeight = '200px';
    // protected int $columnSpan = 'full';

    protected function getColumns(): int
    {
        // Responsive columns based on screen size
        return match (app()->environment()) {
            'production' => 4,
            default => 4,
        };
    }

    private function getAuthenticatedRelatedId(): ?int
    {
        $userId = Auth::id();

        if (Auth::user()->roles === 'patient') {
            $patient = Patient::where('user_id', $userId)->first();
            return $patient ? $patient->id : null;
        }

        if (Auth::user()->roles === 'doctor') {
            $doctor = Doctor::where('user_id', $userId)->first();
            return $doctor ? $doctor->id : null;
        }

        return null;
    }

    private function getAppointmentsCountByStatus(string $status = null): int
    {
        $query = Appointment::query();

        if ($status) {
            $query->where('status', $status);
        }

        if (Auth::check()) {
            $relatedId = $this->getAuthenticatedRelatedId();

            if (Auth::user()->roles === 'doctor') {
                $query->where('doctor_id', $relatedId);
            } elseif (Auth::user()->roles === 'patient') {
                $query->where('patient_id', $relatedId);
            }
        }

        return $query->count();
    }

    protected function getStats(): array
    {
        $totalAppointments = $this->getAppointmentsCountByStatus();
        $totalCompletedAppointments = $this->getAppointmentsCountByStatus('completed');
        $totalPendingAppointments = $this->getAppointmentsCountByStatus('pending');
        $totalBookedAppointments = $this->getAppointmentsCountByStatus('booked');

        return [
            Stat::make('Total Appointments', $totalAppointments)
                ->url(route('filament.admin.resources.appointments.index') . '?activeTab=All')
                ->icon('heroicon-o-calendar')
                ->description('All appointments')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('gray')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
                ->extraAttributes([
                    'class' => 'shadow-xl border-t-4 border-t-gray-500 rounded-xl p-6 bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'
                ]),

            Stat::make('Completed Appointments', $totalCompletedAppointments)
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->description('Successfully completed')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([3, 5, 7, 8, 6, 9, 8, 10])
                ->url(route('filament.admin.resources.appointments.index') . '?activeTab=Completed')
                ->extraAttributes([
                    'class' => 'shadow-xl border-t-4 border-t-green-500 rounded-xl p-6 bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'
                ]),

            Stat::make('Pending Appointments', $totalPendingAppointments)
                ->color('warning')
                ->icon('heroicon-o-clock')
                ->description('Awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->chart([4, 3, 6, 5, 7, 6, 5, 4])
                ->url(route('filament.admin.resources.appointments.index') . '?activeTab=Pending')
                ->extraAttributes([
                    'class' => 'shadow-xl border-t-4 border-t-yellow-500 rounded-xl p-6 bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'
                ]),

            Stat::make('Booked Appointments', $totalBookedAppointments)
                ->color('info')
                ->icon('heroicon-o-bookmark')
                ->description('Confirmed bookings')
                ->descriptionIcon('heroicon-m-calendar')
                ->chart([5, 4, 6, 7, 8, 7, 6, 5])
                ->url(route('filament.admin.resources.appointments.index') . '?activeTab=Booked')
                ->extraAttributes([
                    'class' => 'shadow-xl border-t-4 border-t-blue-500 rounded-xl p-6 bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'
                ]),
        ];
    }
}


// <?php

// namespace App\Filament\Widgets;

// use App\Models\User;
// use App\Models\Patient;
// use App\Models\Doctor;
// use App\Models\Appointment;
// use Illuminate\Support\Facades\Auth;
// use Filament\Widgets\StatsOverviewWidget\Stat;
// use Filament\Widgets\StatsOverviewWidget as BaseWidget;

// class StatsOverview extends BaseWidget
// {
//     protected static bool $isLazy = true;

//     protected static ?int $sort = 1;



//     /**
//      * Get the authenticated user's related ID (patient or doctor).
//      */
//     private function getAuthenticatedRelatedId(): ?int
//     {
//         $userId = Auth::id();

//         if (Auth::user()->roles === 'patient') {
//             $patient = Patient::where('user_id', $userId)->first();
//             return $patient ? $patient->id : null;
//         }

//         if (Auth::user()->roles === 'doctor') {
//             $doctor = Doctor::where('user_id', $userId)->first();
//             return $doctor ? $doctor->id : null;
//         }

//         return null;
//     }

//     /**
//      * Get the count of appointments by status and user role.
//      */
//     private function getAppointmentsCountByStatus(string $status = null): int
//     {
//         $query = Appointment::query();

//         if ($status) {
//             $query->where('status', $status);
//         }

//         if (Auth::check()) {
//             $relatedId = $this->getAuthenticatedRelatedId();

//             if (Auth::user()->roles === 'doctor') {
//                 $query->where('doctor_id', $relatedId);
//             } elseif (Auth::user()->roles === 'patient') {
//                 $query->where('patient_id', $relatedId);
//             }
//         }

//         return $query->count();
//     }

//     protected function getStats(): array
//     {

//         // Appointment stats
//         $totalAppointments = $this->getAppointmentsCountByStatus();
//         $totalCompletedAppointments = $this->getAppointmentsCountByStatus('completed');
//         $totalPendingAppointments = $this->getAppointmentsCountByStatus('pending');
//         $totalBookedAppointments = $this->getAppointmentsCountByStatus('booked');

//         // Get the role of the authenticated user
//         // $userRole = Auth::check() ? Auth::user()->roles : null;

//         return [

//             Stat::make('Total Appointments', $totalAppointments)
//             ->url(route('filament.admin.resources.appointments.index'). '?activeTab=All')
//             ->description('Your Total Appointments')
//             ->descriptionIcon('heroicon-s-calendar'),

//             Stat::make('Completed Appointments', $totalCompletedAppointments)
//                 // ->color('success')
//                 ->url(route('filament.admin.resources.appointments.index'). '?activeTab=Completed')
//                 ->description('Your Completed Appointments')
//                 ->descriptionIcon('heroicon-s-check-badge'),

//             Stat::make('Pending Appointments', $totalPendingAppointments)
//                 ->color('warning')
//                 ->url(route('filament.admin.resources.appointments.index'). '?activeTab=Pending')
//                 ->description('Your Pending Appointments')
//                 ->descriptionIcon('heroicon-s-shield-exclamation'),

//             Stat::make('Booked Appointments', $totalBookedAppointments)
//                 ->color('info')
//                 ->url(route('filament.admin.resources.appointments.index'). '?activeTab=Booked'),
//         ];
//     }
// }
