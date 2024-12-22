<?php
namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Payment; // Ensure you have a Payment model
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PaymentChart extends ChartWidget
{
    // protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    /**
     * Get the payment data for completed and pending status.
     *
     * @return array
     */
    public function getPaymentsData(): array
    {
        // Fetch all payments with 'completed' and 'pending' status
        $paymentsCompleted = Payment::where('payment_status', 'completed')->get();
        $paymentsPending = Payment::where('payment_status', 'pending')->get();



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
        $paymentData = $this->getPaymentsData();

        // dD($paymentData);
        return [
            'datasets' => [
                [
                    'label' => 'Completed Payments',
                    'data' => [$paymentData['completedPayments']],
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Pending Payments',
                    'data' => [$paymentData['pendingPayments']],
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FFB1C1',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Payments'], // Single label for the entire dataset
        ];
    }

    /**
     * Define the type of the chart (bar chart in this case).
     *
     * @return string
     */
    protected function getType(): string
    {
        return 'bar'; // Use 'bar' for a bar chart
    }

    /**
     * Additional styling for the chart.
     *
     * @return array
     */
    protected function getStyles(): array
    {
        return [
            'chart' => [
                'width' => '100%',
                'max-width' => '100%',
                'height' => '400px',
            ],
        ];
    }
}
