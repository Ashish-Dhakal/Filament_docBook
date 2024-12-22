<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected static bool $isLazy = true;

    protected ?string $heading = 'Analytics';

    protected ?string $description = 'An overview of some analytics.';

    private function getUserCountByRole(string $role = null): int
    {
        if ($role) {
            return User::where('roles', $role)->count();
        }

        return User::count();
    }

    public function userId()
    {
        $userId = Auth::id();
        if(Auth::user()->roles == 'patient'){
            $patientId = Patient::where('user_id', $userId)->first();
            return $patientId->id;
        }
        elseif(Auth::user()->roles == 'doctor'){
            $doctorId = Appointment::where('doctor_id', $userId)->first();
            return $doctorId->id;
        }
    }

    private function getAppointmentsCountByStatus(string $status = null, string $role = null): int
    {
        $query = Appointment::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($role && Auth::check()) {
            if (Auth::user()->roles === 'doctor' && $role === 'doctor') {
                $query->where('doctor_id', $this->userId());
            } elseif (Auth::user()->roles === 'patient' && $role === 'patient') {
                $query->where('patient_id',$this->userId());
            }
        }

        return $query->count();
    }

    protected function getStats(): array
    {
        // User stats
        $totalUsers = $this->getUserCountByRole();
        $totalPatients = $this->getUserCountByRole('patient');
        $totalDoctors = $this->getUserCountByRole('doctor');

        // Appointment stats
        $totalAppointments = $this->getAppointmentsCountByStatus();
        $totalCompletedAppointments = $this->getAppointmentsCountByStatus('completed');
        $totalPendingAppointments = $this->getAppointmentsCountByStatus('pending');
        $totalBookedAppointments = $this->getAppointmentsCountByStatus('booked');

        // Authenticated user role-specific appointments
        $userRole = Auth::check() ? Auth::user()->roles : null;
        $userAppointments = $this->getAppointmentsCountByStatus(null, $userRole);

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Patients', $totalPatients)
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Total Doctors', $totalDoctors)
                ->color('success'),

            Stat::make('Total Appointments', $totalAppointments)
                ->description('Across all users')
                ->color('info'),

            Stat::make('My Appointments', $userAppointments)
                ->description("Appointments for {$userRole}")
                ->color('primary'),

            Stat::make('Completed Appointments', $totalCompletedAppointments)
                ->color('success'),

            Stat::make('Pending Appointments', $totalPendingAppointments)
                ->color('warning'),

            Stat::make('Booked Appointments', $totalBookedAppointments)
                ->color('info'),
        ];
    }
}
