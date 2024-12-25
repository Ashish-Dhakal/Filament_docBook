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
    protected static ?string $maxHeight = '200px';
    // protected int $columnSpan = 'full';

    protected function getColumns(): int
    {
        return match (app()->environment()) {
            'production' => 3,
            default => 3,
        };
    }

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
        $totalUsers = $this->getUserCountByRole();
        $totalPatients = $this->getUserCountByRole('patient');
        $totalDoctors = $this->getUserCountByRole('doctor');

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('All registered users')
                ->descriptionIcon('heroicon-m-users')
                ->icon('heroicon-o-users')
                ->color('primary')
                ->chart([8, 6, 7, 9, 8, 10, 11, 12])
                ->extraAttributes([
                    'class' => 'shadow-xl border-t-4 border-t-purple-500 rounded-xl p-6 bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'
                ]),

            Stat::make('Total Patients', $totalPatients)
                ->description('Registered patients')
                ->descriptionIcon('heroicon-m-user-group')
                ->icon('heroicon-o-user')
                ->color('success')
                ->chart([5, 4, 6, 7, 8, 9, 8, 10])
                ->extraAttributes([
                    'class' => 'shadow-xl border-t-4 border-t-emerald-500 rounded-xl p-6 bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'
                ]),

            Stat::make('Total Doctors', $totalDoctors)
                ->description('Medical professionals')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->icon('heroicon-o-user-circle')
                ->color('info')
                ->chart([3, 4, 5, 6, 7, 6, 7, 8])
                ->extraAttributes([
                    'class' => 'shadow-xl border-t-4 border-t-blue-500 rounded-xl p-6 bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700'
                ]),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
}


// <?php

// namespace App\Filament\Widgets;

// use App\Models\User;
// use Illuminate\Support\Facades\Auth;
// use Filament\Widgets\StatsOverviewWidget\Stat;
// use Filament\Widgets\StatsOverviewWidget as BaseWidget;

// class UserOverview extends BaseWidget
// {
//     protected static bool $isLazy = true;

//     protected static ?int $sort = 0;

//   /**
//      * Determine if the widget should be visible.
//      *
//      * @return bool
//      */
//     public static function canView(): bool
//     {
//         return Auth::check() && Auth::user()->roles === 'admin';
//     }


//     private function getUserCountByRole(string $role = null): int
//     {
//         if ($role) {
//             return User::where('roles', $role)->count();
//         }

//         return User::count();
//     }


//     protected function getStats(): array
//     {
//         // Only show the user-related stats to the admin
//         // $totalUsers = $totalPatients = $totalDoctors = 0;

//         $totalUsers = $this->getUserCountByRole();
//         $totalPatients = $this->getUserCountByRole('patient');
//         $totalDoctors = $this->getUserCountByRole('doctor');


//         return [

//             Stat::make('Total Users', $totalUsers),

//             Stat::make('Total Patients', $totalPatients),

//             Stat::make('Total Doctors', $totalDoctors),

//         ];
//     }
// }
