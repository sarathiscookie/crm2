@extends('layouts.app')

@section('title', 'Customer details')

@section('content')

    <div class="row">
        <h1 class="page-header">Customer Details</h1>
        <div class="col-md-6">
            <h2>Customer {{ title_case($customer->firstname) }}  {{ title_case($customer->lastname) }}</h2>
            @if($customer->company)<label>{{ title_case($customer->company) }}</label>@endif <br>
            <label>{{ title_case($customer->firstname) }}  {{ title_case($customer->lastname) }} </label>
            <address>
                @if($customer->steet){{ $customer->steet }}, @endif
                    {{ $customer->city }},
                    @if($customer->state){{ $customer->state }}, @endif
                    {{ $customer->country }}
                    @if($customer->postal) - {{ $customer->postal }}@endif
            </address>
            <label>{{ $customer->email }} </label>
            <label>{{ $customer->phone }} </label>
            <hr>
            <h3>Meetings</h3>
            <div class="panel-group" id="accordionEvent" role="tablist" aria-multiselectable="true">

                {!! $events !!}

            </div>
        </div>
        <div class="col-md-6"><h2>Customer Cars</h2></div>
    </div>
@endsection