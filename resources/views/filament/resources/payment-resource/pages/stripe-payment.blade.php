<x-filament::page>
@php
    $payment = $this->payment;
@endphp
    <div class="container mx-auto">
        <div class="flex justify-center">
            <div class="w-full">
                <div class="bg-white rounded-lg shadow-md dark:bg-white dark:border-gray-600">
                    <div class="p-6">
                        <h2 class="text-2xl font-semibold mb-4">Payment for Order #{{ $payment->id }}</h2>
                        <p class="text-lg mb-4">Amount: ${{ number_format($payment->amount, 2) }}</p>

                        @if (session('success'))
                        <div 
                            class="text-green-600 border-2 border-green-600 text-center p-2 mb-4">
                            Payment Successful!
                        </div>
                        @endif

                        <form id="checkout-form" method="POST" action="{{ route('stripe.create-charge', ['payment' => $payment->id]) }}">
                            @csrf
                            <input type="hidden" name="stripeToken" id="stripe-token-id">
                            
                            <label for="card-element" class="block text-lg font-medium text-gray-700 mb-5">Card Details</label>
                            <div id="card-element" class="form-control border border-gray-300 dark:border-gray-600 rounded-lg p-2 mb-4"></div>
                            
                            <button type="button" id="pay-btn" class="bg-green-500 text-black mt-4 w-full py-2 rounded-lg hover:bg-green-600 transition-colors " onclick="createToken()">
                                PAY ${{ number_format($payment->amount, 2) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('{{ env('STRIPE_KEY') }}');
        var elements = stripe.elements();
        var cardElement = elements.create('card');
        cardElement.mount('#card-element');

        function createToken() {
            document.getElementById("pay-btn").disabled = true;
            stripe.createToken(cardElement).then(function(result) {
                if (result.error) {
                    document.getElementById("pay-btn").disabled = false;
                    alert(result.error.message);
                }
                if (result.token) {
                    document.getElementById("stripe-token-id").value = result.token.id;
                    document.getElementById('checkout-form').submit();
                }
            });
        }
    </script>

</x-filament::page>