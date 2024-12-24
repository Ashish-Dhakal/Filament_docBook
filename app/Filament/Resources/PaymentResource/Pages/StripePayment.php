<?php
namespace App\Filament\Resources\PaymentResource\Pages;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Payment;
use Illuminate\Http\Request;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use App\Filament\Resources\PaymentResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class StripePayment extends Page
{
    use InteractsWithRecord;

    public $payment;

    protected static string $resource = PaymentResource::class;

    protected static string $view = 'filament.resources.payment-resource.pages.stripe-payment';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // Override getRecord() to ensure it always returns an Eloquent model
    public function getRecord(): Payment
    {
        // Use the route parameter to retrieve the payment model
        return Payment::findOrFail($this->record);
    }

    public function mount($record): void
    {
        // Retrieve the payment model instance using getRecord()
        $payment = $this->getRecord();

        if (!$payment) {
            Notification::make()
                ->title('Payment not found')
                ->body('Payment not found!')
                ->danger()
                ->send();

            // Perform the redirection without returning it from mount
            $this->redirectRoute('filament.admin.resources.payments.index');
            return; // Ensure no further processing happens
        }

        if ($payment->payment_status == 'completed') {
            Notification::make()
                ->title('Payment Status')
                ->body('Payment already done!')
                ->danger()
                ->send();

            // Perform the redirection without returning it from mount
            $this->redirectRoute('filament.admin.resources.payments.index');
            return; // Ensure no further processing happens
        }

        // Set the payment to be used in the view
        $this->payment = $payment;
    }

    public function createCharge(Request $request)
    {
        // Retrieve the payment model instance using getRecord()
        $payment = $this->getRecord();

        // If payment doesn't exist, show a failure notification
        if (!$payment) {
            Notification::make()
                ->title('Payment Error')
                ->body('Payment record not found!')
                ->danger()
                ->send();

            // Perform the redirection without returning it from createCharge
            return $this->redirectRoute('filament.admin.resources.payments.index');
        }

        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Create Stripe charge
            Charge::create([
                "amount" => $payment->amount * 100, // Amount in cents
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Payment for Order #" . $payment->id,
            ]);

            // Update the payment status to completed
            $payment->update([
                'payment_status' => 'completed',
                'payment_type' => 'online'
            ]);

            // Notify user of successful payment
            Notification::make()
                ->title('Payment Successful')
                ->body('Your payment has been successfully completed.')
                ->success()
                ->send();

            // Perform the redirection without returning it from createCharge
            return $this->redirectRoute('filament.admin.resources.payments.index');
        } catch (\Exception $e) {
            // Handle errors and notify user
            Notification::make()
                ->title('Payment Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();

            // Perform the redirection without returning it from createCharge
            return $this->redirectRoute('filament.admin.resources.payments.index');
        }
    }
}
