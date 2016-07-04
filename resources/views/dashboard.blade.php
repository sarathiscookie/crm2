@extends('layouts.app')

@section('title', 'Dashboard')

@section('style')
@endsection

@inject('listTodayTomorrowEvents', 'App\Http\Controllers\DashboardController')

@section('content')
    <div class="col-md-4">
        @if(isset($listCustomers))
        <div class="list-group">
            <a href="/customers" class="list-group-item active">
                <span class="badge">{{ count($listCustomers) }}</span>
                Customer List
            </a>
            @forelse ($listCustomers as $listCustomer)
                <a href="#" class="list-group-item">
                    <h5 class="list-group-item-heading">{{$listCustomer->firstname}} {{$listCustomer->lastname}}</h5>
                    <p class="list-group-item-text">{{$listCustomer->company}}</p>
                    <p class="list-group-item-text">{{$listCustomer->erp_id}}</p>
                    <p class="list-group-item-text">@if($listCustomer->created_at == "") {{ "Nill" }} @else {{ date('d.m.Y H:i', strtotime($listCustomer->created_at)) }} @endif</p>
                </a>
            @empty
                <a href="#" class="list-group-item">No new customers</a>
            @endforelse
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="list-group">
            <a href="#" class="list-group-item active">
                <span class="badge">20</span>
                Calls and msg
            </a>
            <a href="#" class="list-group-item">calls and msg</a>
            <a href="#" class="list-group-item">calls and msg</a>
            <a href="#" class="list-group-item">calls and msg</a>
            <a href="#" class="list-group-item">calls and msg</a>
        </div>
    </div>

    <div class="col-md-4">
        <div class="list-group">
            <a href="#" class="list-group-item active">
                <span class="badge">{{ count($listTodayTomorrowEvents->getTodayEvents()) }}</span>
                Today
            </a>
            @forelse($listTodayTomorrowEvents->getTodayEvents() as $listTodayEvent)
                <a href="#" class="list-group-item">
                    <h5 class="list-group-item-text">{{$listTodayEvent->begin_at}} - @if($listTodayEvent->end_at == ""){{ "Nill" }} @else {{$listTodayEvent->end_at}} @endif</h5>
                    <p class="list-group-item-heading">{{$listTodayEvent->firstname}} {{$listTodayEvent->lastname}}</p>
                    <p class="list-group-item-text">{{$listTodayEvent->company}}</p>
                    <p class="list-group-item-text">{{$listTodayEvent->erp_id}}</p>
                    @foreach($listTodayTomorrowEvents->getCarName($listTodayEvent->execution_id) as $carDetails)
                        <?php
                        $text = "<small>" . $carDetails->marke_name . " " . $carDetails->modell_name . "</small> " . $carDetails->tpbezeichnung;

                        if ($carDetails->motor_power)
                        $power = $carDetails->motor_power;
                        else
                        $power = $carDetails->ps_from_dimsport_kw;

                        echo '<p class="list-group-item-text">' . substr(utf8_encode($text), 0, 55) . '<small> mit ' . $power . 'PS</small></p>';
                        ?>
                    @endforeach
                </a>
            @empty
                <a href="#" class="list-group-item">No events today</a>
            @endforelse
        </div>
        <br>

        <div class="list-group">
            <a href="#" class="list-group-item active">
                <span class="badge">{{ count($listTodayTomorrowEvents->getTodayEvents()) }}</span>
                Tomorrow
            </a>
            @forelse($listTodayTomorrowEvents->getTomorrowEvents() as $listTomorrowEvent)
                <a href="#" class="list-group-item">
                    <h5 class="list-group-item-text">{{$listTomorrowEvent->begin_at}} - @if($listTomorrowEvent->end_at == ""){{ "Nill" }} @else {{$listTomorrowEvent->end_at}} @endif</h5>
                    <p class="list-group-item-heading">{{$listTomorrowEvent->firstname}} {{$listTomorrowEvent->lastname}}</p>
                    <p class="list-group-item-text">{{$listTomorrowEvent->company}}</p>
                    <p class="list-group-item-text">{{$listTomorrowEvent->erp_id}}</p>
                    @foreach($listTodayTomorrowEvents->getCarName($listTomorrowEvent->execution_id) as $carDetails)
                        <?php
                        $text = "<small>" . $carDetails->marke_name . " " . $carDetails->modell_name . "</small> " . $carDetails->tpbezeichnung;

                        if ($carDetails->motor_power)
                            $power = $carDetails->motor_power;
                        else
                            $power = $carDetails->ps_from_dimsport_kw;

                        echo '<p class="list-group-item-text">' . substr(utf8_encode($text), 0, 55) . '<small> mit ' . $power . 'PS</small></p>';
                        ?>
                    @endforeach
                </a>
            @empty
                <a href="#" class="list-group-item">No events tomorrow</a>
            @endforelse
        </div>
    </div>
@endsection

@push('script')
@endpush


