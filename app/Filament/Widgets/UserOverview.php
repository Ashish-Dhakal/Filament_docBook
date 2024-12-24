<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserOverview extends BaseWidget
{
    protected static bool $isLazy = true;

    protected static ?int $sort = 0;

  /**
     * Determine if the widget should be visible.
     *
     * @return bool
     */
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->roles === 'admin';
    }


    private function getUserCountByRole(string $role = null): int
    {
        if ($role) {
            return User::where('roles', $role)->count();
        }

        return User::count();
    }


    protected function getStats(): array
    {
        // Only show the user-related stats to the admin
        // $totalUsers = $totalPatients = $totalDoctors = 0;

        $totalUsers = $this->getUserCountByRole();
        $totalPatients = $this->getUserCountByRole('patient');
        $totalDoctors = $this->getUserCountByRole('doctor');


        return [

            Stat::make('Total Users', $totalUsers),

            Stat::make('Total Patients', $totalPatients),

            Stat::make('Total Doctors', $totalDoctors),

        ];
    }
}
