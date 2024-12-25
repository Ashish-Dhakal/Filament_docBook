<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PaymentChart extends ChartWidget
{
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '400px';
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Payment Analytics';
    protected static ?string $description = 'Distribution of completed vs pending payments for the current year';

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->roles === 'admin';
    }

    public function getPaymentsData(int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;

        $paymentsCompleted = Payment::where('payment_status', 'completed')
            ->whereYear('created_at', $year)
            ->get();

        $paymentsPending = Payment::where('payment_status', 'pending')
            ->whereYear('created_at', $year)
            ->get();

        $completedPayments = $paymentsCompleted->sum('amount');
        $pendingPayments = $paymentsPending->sum('amount');

        // Calculate percentages
        $total = $completedPayments + $pendingPayments;
        $completedPercentage = $total > 0 ? round(($completedPayments / $total) * 100, 1) : 0;
        $pendingPercentage = $total > 0 ? round(($pendingPayments / $total) * 100, 1) : 0;

        return [
            'completedPayments' => $completedPayments,
            'pendingPayments' => $pendingPayments,
            'completedPercentage' => $completedPercentage,
            'pendingPercentage' => $pendingPercentage,
            'total' => $total
        ];
    }

    protected function getData(): array
    {
        $year = Carbon::now()->year;
        $paymentData = $this->getPaymentsData($year);

        return [
            'datasets' => [
                [
                    'data' => [
                        $paymentData['completedPayments'],
                        $paymentData['pendingPayments']
                    ],
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.9)',  // Green for completed
                        'rgba(234, 179, 8, 0.9)'   // Yellow for pending
                    ],
                    'borderColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(234, 179, 8, 1)'
                    ],
                    'borderWidth' => 2,
                    'hoverBackgroundColor' => [
                        'rgba(34, 197, 94, 1)',
                        'rgba(234, 179, 8, 1)'
                    ],
                    'hoverBorderColor' => '#ffffff',
                    'hoverBorderWidth' => 4,
                ],
            ],
            'labels' => [
                'Completed (' . $paymentData['completedPercentage'] . '%)',
                'Pending (' . $paymentData['pendingPercentage'] . '%)'
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'boxWidth' => 15,
                        'boxHeight' => 15,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'font' => [
                            'size' => 13,
                            'weight' => 'bold',
                        ],
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'padding' => 12,
                    'titleFont' => [
                        'size' => 14,
                    ],
                    'bodyFont' => [
                        'size' => 13,
                    ],
                    'callbacks' => [
                        'label' => "function(context) {
                            return ' $' + context.raw.toLocaleString();
                        }",
                    ],
                ],
                'doughnutlabel' => [
                    'labels' => [
                        [
                            'text' => '{{value}}',
                            'font' => [
                                'size' => 20,
                                'weight' => 'bold',
                            ],
                        ],
                    ],
                ],
            ],
            'cutout' => '65%',
            'responsive' => true,
            'maintainAspectRatio' => false,
            'animation' => [
                'animateScale' => true,
                'animateRotate' => true,
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

    public function getCenterLabel(): ?string
    {
        $data = $this->getPaymentsData();
        return '$' . number_format($data['total'], 2);
    }
}

// <?php

// namespace App\Filament\Widgets;

// use Carbon\Carbon;
// use App\Models\Payment; // Ensure you have a Payment model
// use Filament\Widgets\ChartWidget;
// use Illuminate\Support\Facades\Auth;

// class PaymentChart extends ChartWidget
// {
//     protected static ?int $sort = 3;

//     protected static ?string $maxHeight = '241px';

//     protected static ?string $heading = 'Payment Chart';

//     protected static ?string $description = 'Comparison of completed and pending payments in the specified year.';
//     public static function canView(): bool
//     {
//         return Auth::check() && Auth::user()->roles === 'admin';
//     }

//     /**
//      * Get the payment data for completed and pending status in the specified year.
//      *
//      * @return array
//      */
//     public function getPaymentsData(int $year = null): array
//     {
//         // If no year is provided, default to the current year
//         $year = $year ?? Carbon::now()->year;

//         // Fetch payments filtered by year
//         $paymentsCompleted = Payment::where('payment_status', 'completed')
//             ->whereYear('created_at', $year)
//             ->get();

//         $paymentsPending = Payment::where('payment_status', 'pending')
//             ->whereYear('created_at', $year)
//             ->get();

//         // Initialize arrays to store the total amount for completed and pending payments
//         $completedPayments = 0;
//         $pendingPayments = 0;

//         // Calculate the total amount for completed payments
//         foreach ($paymentsCompleted as $payment) {
//             $completedPayments += $payment->amount;
//         }

//         // Calculate the total amount for pending payments
//         foreach ($paymentsPending as $payment) {
//             $pendingPayments += $payment->amount;
//         }

//         return [
//             'completedPayments' => $completedPayments,
//             'pendingPayments' => $pendingPayments,
//         ];
//     }

//     /**
//      * Prepare the data for the chart widget.
//      *
//      * @return array
//      */
//     protected function getData(): array
//     {
//         $year = Carbon::now()->year; // Use the current year for example
//         $paymentData = $this->getPaymentsData($year);

//         return [
//             'datasets' => [
//                 [
//                     'data' => [
//                         $paymentData['completedPayments'],
//                         $paymentData['pendingPayments']
//                     ],
//                     'backgroundColor' => ['#36A2EB', '#FF6384'],  // Different colors for each slice
//                     'borderColor' => ['#9BD0F5', '#FFB1C1'],  // Border colors for the slices
//                     'borderWidth' => 2,
//                 ],
//             ],
//             'labels' => ['Completed Payments', 'Pending Payments'], // Labels for each slice
//         ];
//     }

//     /**
//      * Define the type of the chart (pie chart in this case).
//      *
//      * @return string
//      */
//     protected function getType(): string
//     {
//         return 'pie'; // Use 'pie' for a pie chart
//     }
// }
