<?php
namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PaymentChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $minHeight = '400px';
    protected static ?string $maxHeight = '500px';
    protected static ?string $heading = 'Payment Chart';
    protected static ?string $description = 'Monthly payment trends for completed and pending payments for this year and last year.';

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->roles === 'admin';
    }

    public function getMonthlyPaymentsData(int $year): array
    {
        $monthlyData = [
            'completed' => array_fill(0, 12, 0),
            'pending' => array_fill(0, 12, 0),
        ];

        $payments = Payment::whereYear('created_at', $year)->get();

        foreach ($payments as $payment) {
            $month = Carbon::parse($payment->created_at)->month - 1;

            if ($payment->payment_status === 'completed') {
                $monthlyData['completed'][$month] += $payment->amount;
            } elseif ($payment->payment_status === 'pending') {
                $monthlyData['pending'][$month] += $payment->amount;
            }
        }

        return $monthlyData;
    }

    protected function getData(): array
    {
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        $currentYearData = $this->getMonthlyPaymentsData($currentYear);
        $previousYearData = $this->getMonthlyPaymentsData($previousYear);

        return [
            'datasets' => [
                [
                    'label' => "Completed Payments ($currentYear)",
                    'data' => $currentYearData['completed'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointStyle' => 'circle',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => 'rgba(54, 162, 235, 1)',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
                [
                    'label' => "Pending Payments ($currentYear)",
                    'data' => $currentYearData['pending'],
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointStyle' => 'circle',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => 'rgba(255, 99, 132, 1)',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
                [
                    'label' => "Completed Payments ($previousYear)",
                    'data' => $previousYearData['completed'],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointStyle' => 'circle',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => 'rgba(75, 192, 192, 1)',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
                [
                    'label' => "Pending Payments ($previousYear)",
                    'data' => $previousYearData['pending'],
                    'backgroundColor' => 'rgba(255, 205, 86, 0.2)',
                    'borderColor' => 'rgba(255, 205, 86, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointStyle' => 'circle',
                    'pointRadius' => 5,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => 'rgba(255, 205, 86, 1)',
                    'pointBorderColor' => '#ffffff',
                    'pointBorderWidth' => 2,
                ],
            ],
            'labels' => [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December',
            ],
        ];
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

    protected function getType(): string
    {
        return 'line';
    }
}
