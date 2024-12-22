<?php
namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class BlogPostsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    // protected int | string | array $columnSpan = 'full';

    /**
     * Determine if the widget should be visible.
     *
     * @return bool
     */
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->roles === 'admin';
    }

    /**
     * Get the appointment data for this year and last year.
     *
     * @return array
     */
    public function getAppointmentsData(): array
    {
        // Get the current year and last year
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;

        // Fetch the number of appointments for each month in the current year
        $appointmentsThisYear = Appointment::whereYear('date', $currentYear)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('m'); // Group by month
            });

        // Fetch the number of appointments for each month in the previous year
        $appointmentsLastYear = Appointment::whereYear('date', $lastYear)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->date)->format('m'); // Group by month
            });

        // Prepare data for each dataset
        $thisYearData = array_fill(0, 12, 0); 
        $lastYearData = array_fill(0, 12, 0); 

        // Count appointments for each month in the current year
        foreach ($appointmentsThisYear as $month => $appointments) {
            $thisYearData[(int)$month - 1] = $appointments->count();
        }

        // Count appointments for each month in the previous year
        foreach ($appointmentsLastYear as $month => $appointments) {
            $lastYearData[(int)$month - 1] = $appointments->count();
        }

        return [
            'thisYear' => $thisYearData,
            'lastYear' => $lastYearData,
        ];
    }

    /**
     * Prepare data for the chart widget.
     *
     * @return array
     */
    protected function getData(): array
    {
        $appointmentsData = $this->getAppointmentsData();

        // Get the current year and last year
        $currentYear = Carbon::now()->year;
        $lastYear = $currentYear - 1;

        return [
            'datasets' => [
                [
                    'label' => 'Appointments of ' . $currentYear,
                    'data' => $appointmentsData['thisYear'],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Appointments of ' . $lastYear,
                    'data' => $appointmentsData['lastYear'],
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FFB1C1',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Use 'bar' to display a bar chart
    }

    protected function getStyles(): array
    {
        return [
            'chart' => [
                'width' => '100%',  // Set the width to 100% of the parent container
                'max-width' => '100%',  // Ensure it does not exceed the full width
                'height' => '400px',  // Set your desired height
            ],
        ];
    }
}
