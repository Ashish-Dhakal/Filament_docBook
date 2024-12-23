<?php

namespace App\Http\Controllers;

use Stripe;
use App\Models\Payment;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function index($payment)
    {

        $payment = Payment::find($payment);
        if (!$payment) {
            Notification::make()
                ->title('Payment not found')
                ->body('Payment not found!')
                ->danger()
                ->send();
            return redirect()->route('filament.admin.resources.payments.index')
                ->with('error', 'Payment not found!');
        }
        if ($payment->payment_status == 'completed') {
            Notification::make()
                ->title('Payment Status')
                ->body('Payment already done!')
                ->danger()
                ->send();
            return redirect()->route('filament.admin.resources.payments.index')->with('success', 'Payment already done!');
        }
        return view('checkout', compact('payment'));
    }


    public function createCharge(Request $request, $payment)
    {
        $payment = Payment::find($payment);

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create([
            "amount" => $payment->amount,
            "currency" => "usd",
            "source" => $request->stripeToken,
            "description" => "Binaryboxtuts Payment Test"
        ]);
        $payment->update([
            'payment_status' => 'completed',
            'payment_type' => 'online'
        ]);


        // return redirect_route('');
        return redirect()->route('filament.admin.resources.payments.index')->with('success', 'Payment successfully done!');
    }
}
