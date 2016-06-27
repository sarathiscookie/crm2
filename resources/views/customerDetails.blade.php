@extends('layouts.app')

@section('title', 'Customer details')

@section('content')

    <h1 class="page-header">Kunden-Details</h1>
    <div class="row">
        <div class="col-md-6">
            <h2>{{ title_case($customer->firstname) }}  {{ title_case($customer->lastname) }}</h2>
            @if($customer->company)<label>{{ $customer->company }}</label>@endif <br>
            <address>
                @if($customer->street){{ $customer->street }}@endif<br>
                    @if($customer->postal){{ $customer->postal }}@endif {{ $customer->city }}<br>
                    {{ $customer->country }}

            </address>
            <label>E-Mail: <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></label><br>
            <label>Telefon: {{ $customer->phone }} </label>
            <hr>
            <h3>Termine</h3>
            <div class="panel-group" id="accordionEvent" role="tablist" aria-multiselectable="true">

                {!! $events !!}

            </div>
        </div>
        <div class="col-md-6">
            <h2>Fahrzeuge <button class="btn btn-primary pull-right">Neues Fahrzeug hinzufügen</button></h2>
            <div class="panel-group" id="accordionVehicle" role="tablist" aria-multiselectable="true">

                {!! $vehicles !!}

            </div>
        </div>
    </div>
@endsection