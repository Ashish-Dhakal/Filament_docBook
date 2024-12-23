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


    private function getUserCountByRole(string $role = null): int
    {
        if ($role) {
            return User::where('roles', $role)->count();
        }

        return User::count();
    }

    /**
     * Get the authenticated user's related ID (patient or doctor).
     */
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

    /**
     * Get the count of appointments by status and user role.
     */
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
      // Only show the user-related stats to the admin
    //   if (Auth::check() && Auth::user()->hasRole('admin')) {
        $totalUsers = $this->getUserCountByRole();
        $totalPatients = $this->getUserCountByRole('patient');
        $totalDoctors = $this->getUserCountByRole('doctor');

        $stats[] = Stat::make('Total Users', $totalUsers);
        $stats[] = Stat::make('Total Patients', $totalPatients);
        $stats[] = Stat::make('Total Doctors', $totalDoctors);
    // }

        // Appointment stats
        $totalAppointments = $this->getAppointmentsCountByStatus();
        $totalCompletedAppointments = $this->getAppointmentsCountByStatus('completed');
        $totalPendingAppointments = $this->getAppointmentsCountByStatus('pending');
        $totalBookedAppointments = $this->getAppointmentsCountByStatus('booked');

        // Get the role of the authenticated user
        $userRole = Auth::check() ? Auth::user()->roles : null;

        return [

            // Stat::make('Total Users', $totalUsers),

            Stat::make('Total Patients', $totalPatients),

            Stat::make('Total Doctors', $totalDoctors),

            Stat::make('Total Appointments', $totalAppointments)
            ->url(route('filament.admin.resources.appointments.index'). '?activeTab=All'),

            Stat::make('Completed Appointments', $totalCompletedAppointments)
                ->color('success')
                ->url(route('filament.admin.resources.appointments.index'). '?activeTab=Completed'),

            Stat::make('Pending Appointments', $totalPendingAppointments)
                ->color('warning')
                ->url(route('filament.admin.resources.appointments.index'). '?activeTab=Pending'),

            Stat::make('Booked Appointments', $totalBookedAppointments)
                ->color('info')
                ->url(route('filament.admin.resources.appointments.index'). '?activeTab=Booked'),
        ];
    }
}
