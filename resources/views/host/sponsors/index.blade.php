@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sponsorizza i tuoi appartamenti</h1>
    @if (!$locations->isEmpty())
        @foreach ($locations as $location)
            <div class="row location">
                <div class="col-6">
                    <a href="#">{{ $location->name }}</a>
                </div>

                <div class="col-6 d-flex">
                    @foreach ($sponsors as $sponsor)
                        <div class="mx-4">
                            <a class="subscription $sponsor->subscription ? $sponsor->subscription : '' " href="#">{{ $sponsor->subscription }}</a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <span>Non hai ancora nessun annuncio</span>
    @endif
    
</div>
@endsection

<script>
    var button = document.querySelector('#submit-button');

    braintree.dropin.create({
        authorization: "{{ Braintree_ClientToken::generate() }}",
        container: '#dropin-container'
        }, function (createErr, instance) {
        button.addEventListener('click', function () {
            instance.requestPaymentMethod(function (err, payload) {
            $.get('{{ route('payment.process') }}', {payload}, function (response) {
                if (response.success) {
                alert('Payment successfull!');
                } else {
                alert('Payment failed');
                }
            }, 'json');
            });
        });
    });
  </script>

<style>
    .location{
        border: 1px solid lightgray;
        padding: 15px 15px;
        border-radius: 25px;
    }

    .subscription{
        border: 1px solid lightgray;
        padding: 6px 15px;
        border-radius: 25px;
    }

    .gold{
        background-color: yellow;
    }

</style>