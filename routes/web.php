<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeController;
 
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('stripe/{id}', [StripeController::class, 'index'])->name('stripe.index');
Route::post('stripe/create-charge/{payment}', [StripeController::class, 'createCharge'])->name('stripe.create-charge');
