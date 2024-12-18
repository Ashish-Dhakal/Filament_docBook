<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="container mx-auto">
        <div class="flex justify-center">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6">
                        @if (session('success'))
                        <div 
                            class="text-green-600 border-2 border-green-600 text-center p-2 mb-4">
                            Payment Successful!
                        </div>
                        @endif
                        <form id='checkout-form' method='post' action="{{route('stripe.create-charge' , ['payment' => $payment])}}">   
                            @csrf             
                            <input type='hidden' name='stripeToken' id='stripe-token-id'>                             
                            <label for="card-element" class="block text-lg font-medium text-gray-700 mb-5">Checkout Form</label>
                            <div id="card-element" class="form-control border border-gray-300 rounded-lg p-2"></div>
                            <button 
                                id='pay-btn'
                                class="bg-green-500 text-white mt-4 w-full py-2 rounded-lg hover:bg-green-600 transition-colors"
                                type="button"
                                onclick="createToken()">PAY ${{$payment->amount}}
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
</body>
</html>
