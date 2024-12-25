<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use Stripe\Stripe;
use Stripe\Charge;
use App\Models\Payment;
use Illuminate\Http\Request;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;
use App\Filament\Resources\PaymentResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;

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
        if (!isset($this->record)) {
            throw new \Exception('Record not initialized');
        }

        // Use the record ID passed in the route to retrieve the payment model
        return Payment::where('slug', $this->record)->firstOrFail();
    }

    public function mount($record): void
    {
        // Ensure the record is available and set the payment model
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

    // public function createCharge(Request $request ,$payment_id)
    // {
    //     $payment = Payment::find($payment_id);
    //     // dd($request);

    //     Stripe::setApiKey(env('STRIPE_SECRET'));
    //     Charge::create([
    //         "amount" => $payment->amount,
    //         "currency" => "usd",
    //         "source" => $request->stripeToken,
    //         "description" => "Binaryboxtuts Payment Test"
    //     ]);

    //     $payment->update([
    //         'payment_status' => 'completed',
    //         'transaction_id' => $request->stripeToken,
    //         'payment_type' => 'online'
    //     ]);

    //     // Return with success notification
    //     return redirect()->route('filament.admin.resources.payments.index')->with('success', 'Payment successfully done!');
    // }
    
    public function createCharge(Request $request, $slug)
    {
        $payment = Payment::find($slug);
    
        // Check if the payment amount is less than $1
        if ($payment->amount < 100) { // Stripe amount is in cents
            Notification::make()
                ->title('Payment Failed')
                ->body('The payment amount must be at least $1. Please review your payment details and try again.')
                ->danger()
                ->send();
    
            return redirect()
                ->route('filament.admin.resources.payments.index');
        }
    
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
    
            $charge = Charge::create([
                "amount" => $payment->amount, // Amount in cents
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Binaryboxtuts Payment Test"
            ]);
    
            // Update payment details
            $payment->update([
                'payment_status' => 'completed',
                'transaction_id' => $charge->id,
                'payment_type' => 'online'
            ]);
    
            // Success notification
            Notification::make()
                ->title('Payment Successful')
                ->body('The payment has been completed successfully.')
                ->success()
                ->send();
    
            return redirect()
                ->route('filament.admin.resources.payments.index');
        } catch (CardException $e) {
            Notification::make()
                ->title('Payment Failed')
                ->body('Your card is invalid. Please check your card details and try again.')
                ->danger()
                ->send();
        } catch (InvalidRequestException $e) {
            Notification::make()
                ->title('Payment Failed')
                ->body('Invalid payment request. Please verify your payment details and try again.')
                ->danger()
                ->send();
        } catch (ApiConnectionException $e) {
            Notification::make()
                ->title('Payment Failed')
                ->body('There was a network error while processing your payment. Please try again later.')
                ->danger()
                ->send();
        } catch (ApiErrorException $e) {
            Notification::make()
                ->title('Payment Failed')
                ->body('An error occurred with the payment gateway. Please try again or contact support.')
                ->danger()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Payment Failed')
                ->body('An unexpected error occurred. Please try again later or contact support.')
                ->danger()
                ->send();
        }
    
        // Redirect in case of any failure
        return redirect()
            ->route('filament.admin.resources.payments.index');
    }
    
}
