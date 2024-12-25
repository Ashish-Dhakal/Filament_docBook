<?php
namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class BlogPostsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $minHeight = '400px';
    protected static ?string $maxHeight = '500px';

    protected static ?string $heading = 'Appointments Analytics';
    protected static ?string $description = 'Monthly comparison of appointments between current and previous year';

    protected function getHeaderActions(): array
    {
        return [
            // Add export action if needed
        ];
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->roles === 'admin';
    }

    public function getAppointmentsData(): array
    {
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;

        // Fetch current year appointments
        $appointmentsThisYear = Appointment::whereYear('date', $currentYear)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('m');
            });

        // Fetch previous year appointments
        $appointmentsLastYear = Appointment::whereYear('date', $lastYear)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('m');
            });

        // Initialize arrays with zeros
        $thisYearData = array_fill(0, 12, 0);
        $lastYearData = array_fill(0, 12, 0);

        // Fill current year data
        foreach ($appointmentsThisYear as $month => $appointments) {
            $thisYearData[(int)$month - 1] = $appointments->count();
        }

        // Fill previous year data
        foreach ($appointmentsLastYear as $month => $appointments) {
            $lastYearData[(int)$month - 1] = $appointments->count();
        }

        return [
            'thisYear' => $thisYearData,
            'lastYear' => $lastYearData,
        ];
    }

    protected function getData(): array
    {
        $appointmentsData = $this->getAppointmentsData();
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;

        return [
            'datasets' => [
                [
                    'label' => $currentYear . ' Appointments',
                    'data' => $appointmentsData['thisYear'],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)', // Light blue background
                    'borderColor' => 'rgba(59, 130, 246, 1)', // Solid blue border
                    'borderWidth' => 2,
                    'tension' => 0.4, // Smooth curve
                    'fill' => true,
                    'pointStyle' => 'circle',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => 'rgba(59, 130, 246, 1)',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
                [
                    'label' => $lastYear . ' Appointments',
                    'data' => $appointmentsData['lastYear'],
                    'backgroundColor' => 'rgba(248, 113, 113, 0.1)', // Light red background
                    'borderColor' => 'rgba(248, 113, 113, 1)', // Solid red border
                    'borderWidth' => 2,
                    'tension' => 0.4, // Smooth curve
                    'fill' => true,
                    'pointStyle' => 'circle',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => 'rgba(248, 113, 113, 1)',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
            ],
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'padding' => 20,
                        'boxWidth' => 40,
                        'usePointStyle' => true,
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'padding' => 12,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.8)',
                    'titleFont' => [
                        'size' => 14,
                    ],
                    'bodyFont' => [
                        'size' => 13,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.05)',
                    ],
                    'ticks' => [
                        'font' => [
                            'size' => 12,
                        ],
                        'stepSize' => 1,
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }

    protected function extraCardClasses(): array
    {
        return [
            'bg-white dark:bg-gray-800',
            'shadow-xl',
            'rounded-xl',
            'border border-gray-200 dark:border-gray-700',
            'p-6',
        ];
    }
}

// <?php
// namespace App\Filament\Widgets;

// use Carbon\Carbon;
// use App\Models\Appointment;
// use Filament\Widgets\ChartWidget;
// use Illuminate\Support\Facades\Auth;

// class BlogPostsChart extends ChartWidget
// {
//     protected static ?int $sort = 2;
//     // protected int | string | array $columnSpan = 'full';

//     protected static ?string $minHeight = '300px';

//     protected static ?string $heading = 'Appointments Chart';

//     protected static ?string $description = 'Comparision of appointments between this year and last year.';

    

//     /**
//      * Determine if the widget should be visible.
//      *
//      * @return bool
//      */
//     public static function canView(): bool
//     {
//         return Auth::check() && Auth::user()->roles === 'admin';
//     }

//     /**
//      * Get the appointment data for this year and last year.
//      *
//      * @return array
//      */
//     public function getAppointmentsData(): array
//     {
//         // Get the current year and last year
//         $currentYear = Carbon::now()->year;
//         $lastYear = $currentYear - 1;

//         // Fetch the number of appointments for each month in the current year
//         $appointmentsThisYear = Appointment::whereYear('date', $currentYear)
//             ->get()
//             ->groupBy(function ($date) {
//                 return Carbon::parse($date->date)->format('m'); // Group by month
//             });

//         // Fetch the number of appointments for each month in the previous year
//         $appointmentsLastYear = Appointment::whereYear('date', $lastYear)
//             ->get()
//             ->groupBy(function ($date) {
//                 return Carbon::parse($date->date)->format('m'); // Group by month
//             });

//         // Prepare data for each dataset
//         $thisYearData = array_fill(0, 12, 0); 
//         $lastYearData = array_fill(0, 12, 0); 

//         // Count appointments for each month in the current year
//         foreach ($appointmentsThisYear as $month => $appointments) {
//             $thisYearData[(int)$month - 1] = $appointments->count();
//         }

//         // Count appointments for each month in the previous year
//         foreach ($appointmentsLastYear as $month => $appointments) {
//             $lastYearData[(int)$month - 1] = $appointments->count();
//         }

//         return [
//             'thisYear' => $thisYearData,
//             'lastYear' => $lastYearData,
//         ];
//     }

//     /**
//      * Prepare data for the chart widget.
//      *
//      * @return array
//      */
//     protected function getData(): array
//     {
//         $appointmentsData = $this->getAppointmentsData();

//         // Get the current year and last year
//         $currentYear = Carbon::now()->year;
//         $lastYear = $currentYear - 1;

//         return [
//             'datasets' => [
//                 [
//                     'label' => 'Appointments of ' . $currentYear,
//                     'data' => $appointmentsData['thisYear'],
//                     'backgroundColor' => '#36A2EB',
//                     'borderColor' => '#9BD0F5',
//                     'borderWidth' => 2,
//                 ],
//                 [
//                     'label' => 'Appointments of ' . $lastYear,
//                     'data' => $appointmentsData['lastYear'],
//                     'backgroundColor' => '#FF6384',
//                     'borderColor' => '#FFB1C1',
//                     'borderWidth' => 2,
//                 ],
//             ],
//             'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
//         ];
//     }

//     protected function getType(): string
//     {
//         return 'line'; // Use 'bar' to display a bar chart
//     }

//     protected function getStyles(): array
//     {
//         return [
//             'chart' => [
//                 'width' => '100%',  // Set the width to 100% of the parent container
//                 'max-width' => '100%',  // Ensure it does not exceed the full width
//                 'height' => '400px',  // Set your desired height
//             ],
//         ];
//     }
// }
