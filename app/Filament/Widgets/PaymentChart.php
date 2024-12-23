<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Payment; // Ensure you have a Payment model
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PaymentChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected static ?string $maxHeight = '241px';

    protected static ?string $heading = 'Payment Chart';

    protected static ?string $description = 'Comparison of completed and pending payments in the specified year.';
    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->roles === 'admin';
    }

    /**
     * Get the payment data for completed and pending status in the specified year.
     *
     * @return array
     */
    public function getPaymentsData(int $year = null): array
    {
        // If no year is provided, default to the current year
        $year = $year ?? Carbon::now()->year;

        // Fetch payments filtered by year
        $paymentsCompleted = Payment::where('payment_status', 'completed')
            ->whereYear('created_at', $year)
            ->get();

        $paymentsPending = Payment::where('payment_status', 'pending')
            ->whereYear('created_at', $year)
            ->get();

        // Initialize arrays to store the total amount for completed and pending payments
        $completedPayments = 0;
        $pendingPayments = 0;

        // Calculate the total amount for completed payments
        foreach ($paymentsCompleted as $payment) {
            $completedPayments += $payment->amount;
        }

        // Calculate the total amount for pending payments
        foreach ($paymentsPending as $payment) {
            $pendingPayments += $payment->amount;
        }

        return [
            'completedPayments' => $completedPayments,
            'pendingPayments' => $pendingPayments,
        ];
    }

    /**
     * Prepare the data for the chart widget.
     *
     * @return array
     */
    protected function getData(): array
    {
        $year = Carbon::now()->year; // Use the current year for example
        $paymentData = $this->getPaymentsData($year);

        return [
            'datasets' => [
                [
                    'data' => [
                        $paymentData['completedPayments'],
                        $paymentData['pendingPayments']
                    ],
                    'backgroundColor' => ['#36A2EB', '#FF6384'],  // Different colors for each slice
                    'borderColor' => ['#9BD0F5', '#FFB1C1'],  // Border colors for the slices
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Completed Payments', 'Pending Payments'], // Labels for each slice
        ];
    }

    /**
     * Define the type of the chart (pie chart in this case).
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'pie'; // Use 'pie' for a pie chart
    }
}
