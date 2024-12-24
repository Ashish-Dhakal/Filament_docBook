<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use App\Filament\Resources\PaymentResource\Pages\StripePayment;
 
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('stripe/{id}', [StripeController::class, 'index'])->name('stripe.index');
Route::post('stripe/create-charge/{payment}', [StripeController::class, 'createCharge'])->name('stripe.create-charge');



Route::post('payments/{record}/stripe-payment', [StripePayment::class, 'createCharge'])
    ->name('filament.resources.payment-resource.pages.stripe-payment.create-charge');