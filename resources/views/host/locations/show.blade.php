@extends('layouts.app')

@section('content')
    <section>
        <div class="container">
            <h1>{{ $location->name }}</h1>
            
            <div class="img-fluid">
                @if (str_contains($location->photo, 'location_photos'))
                    <img src="{{ asset('storage/' . $location->photo) }}" class="big_picture rounded mb-3" alt="{{ $location->name }}">
                @else
                    <img src="{{ $location->photo }}" class="big_picture rounded mb-3" alt="{{ $location->name }}">
                @endif
            </div>

            <div class="mb-2"><strong>Categoria:</strong> {{ $location->category ? $location->category->name : 'nessuna' }}</div>

            <div class="mb-2"><strong>Features:</strong>
                @forelse ($location->features as $feature)
                    {{ $feature->name }}{{ $loop->last ? '' : ', ' }}
                @empty
                    nessuna
                @endforelse
            </div>

            <div class="mb-2">
                <strong>Indirizzo:</strong> {{$location->country}} {{$location->city}} {{$location->address}}, {{$location->number}}
            </div>

            <div class="mb-2">
                <strong>Camere:</strong> {{$location->rooms}}
            </div>
            <div class="mb-2">
                <strong>Posti letto:</strong> {{$location->beds}}
            </div>
            <div class="mb-2">
                <strong>Bagni:</strong> {{$location->bathrooms}}
            </div>
            <div class="mb-2">
                <strong>Metri quadrati:</strong> {{$location->square_meters}} 
            </div>
            <div class="mb-2">
                <strong>Prezzo a notte:</strong> {{$location->price}} €
            </div>         
               
            <p>{{ $location->description }}</p>

            <div>
                <a href="{{ route('host.locations.edit', ['location' => $location->id]) }}">Modifica location</a>
            </div>

            <div class="mt-3">
                <form action="{{ route('host.locations.destroy', ['location' => $location->id]) }}" method="post">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-danger" onclick="return confirm('Sei sicuro di voler cancellare?')">Cancella</button>
                </form>
            </div>

            <div id="dropin-wrapper">
                <div id="checkout-message"></div>
                <div id="dropin-container"></div>
                <button id="submit-button">Submit payment</button>
            </div>

            {{-- </Payament> --}}

            <location-map :lng="{{ $location -> long }}" :lat="{{ $location -> lat }}"></location-map>

        </div>
    </section>
@endsection

<script>
    var button = document.querySelector('#submit-button');

braintree.dropin.create({
  // Insert your tokenization key here
  authorization: 'sandbox_386dd9rr_mpnfctm2gjmrx73x',
  container: '#dropin-container'
}, function (createErr, instance) {
  button.addEventListener('click', function () {
    instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
      // When the user clicks on the 'Submit payment' button this code will send the
      // encrypted payment information in a variable called a payment method nonce
      $.ajax({
        type: 'POST',
        url: '/checkout',
        data: {'paymentMethodNonce': payload.nonce}
      }).done(function(result) {
        // Tear down the Drop-in UI
        instance.teardown(function (teardownErr) {
          if (teardownErr) {
            console.error('Could not tear down Drop-in UI!');
          } else {
            console.info('Drop-in UI has been torn down!');
            // Remove the 'Submit payment' button
            $('#submit-button').remove();
          }
        });

        if (result.success) {
          $('#checkout-message').html('<h1>Success</h1><p>Your Drop-in UI is working! Check your <a href="https://sandbox.braintreegateway.com/login">sandbox Control Panel</a> for your test transactions.</p><p>Refresh to try another transaction.</p>');
        } else {
          console.log(result);
          $('#checkout-message').html('<h1>Error</h1><p>Check your console.</p>');
        }
      });
    });
  });
});
</script>

<style>

    section{
        height: calc(100vh - 100px);
        overflow-y: auto;
    }
    h1{
        text-align: center;
    }

    .big_picture{
        height: 50vh;
        margin: auto;
        display: flex;
    }
</style>
