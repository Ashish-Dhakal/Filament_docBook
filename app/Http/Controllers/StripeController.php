<?php
  
namespace App\Http\Controllers;
  
use Stripe;
use App\Models\Payment;
use Illuminate\Http\Request;
  
class StripeController extends Controller
{
    public function index($payment)
    {
        $payment = Payment::find($payment);
        return view('checkout' , compact('payment'));
    }
     
      
    public function createCharge(Request $request , $payment)
    {
        $payment = Payment::find($payment);
      
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => $payment->amount,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $payment->update(['payment_status' => 'completed',
            'payment_type' => 'online']);

     
        // return redirect_route('');
        return redirect()->route('filament.admin.resources.payments.index' )->with('success', 'Payment successfully done!');
    }
}