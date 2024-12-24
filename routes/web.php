<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
use App\Filament\Resources\PaymentResource\Pages\StripePayment;
 
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::post('stripe/create-charge/{payment}', [StripePayment::class, 'createCharge'])->name('stripe.create-charge');