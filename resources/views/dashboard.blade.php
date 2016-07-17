@extends('layouts.app')

@section('title', 'Dashboard')

@section('style')
@endsection

@inject('listTodayTomorrowEvents', 'App\Http\Controllers\DashboardController')

@section('content')
    <div class="row" style="margin-top: 50px;">
        <div class="col-md-4">
            @if(isset($listCustomers))
                <div class="list-group">
                    <a href="/customers" class="list-group-item active">
                        <span class="badge">{{ count($listCustomers) }}</span>
                        {{ trans('messages.dashboardItemHeaderCustomersList') }}
                    </a>
                    @forelse ($listCustomers as $listCustomer)
                        <a href="/customer/details/{{ $listCustomer->id }}" class="list-group-item">
                            <h5 class="list-group-item-heading">{{$listCustomer->firstname}} {{$listCustomer->lastname}}</h5>
                            <p class="list-group-item-text">{{$listCustomer->company}}</p>
                            <p class="list-group-item-text">{{$listCustomer->erp_id}}</p>
                            <p class="list-group-item-text">@if($listCustomer->created_at == "") {{ "Nill" }} @else {{ date('d.m.Y H:i', strtotime($listCustomer->created_at)) }} @endif</p>
                        </a>
                    @empty
                        <a href="#"
                           class="list-group-item">{{ trans('messages.dashboardItemHeaderNoCustomerMessage') }}</a>
                    @endforelse
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <h1>BETA-Version</h1><br>
            Es können noch Fehler auftreten und die erfassten Daten könnten auch noch mal gelöscht werden.<br>
            <br>
            Alles Fehler bitte direkt in Redbooth <a href="https://redbooth.com/a/#!/projects/989360/tasks/24861073"
                                                     target="_blank">hier</a> melden.
        </div>

        <div class="col-md-4">
            <div class="list-group">
                <a href="#" class="list-group-item active">
                    <span class="badge">{{ count($listTodayTomorrowEvents->getTodayEvents()) }}</span>
                    {{ trans('messages.dashboardItemHeaderTodayList') }}
                </a>
                @forelse($listTodayTomorrowEvents->getTodayEvents() as $listTodayEvent)
                    <a href="/customer/details/{{$listTodayEvent->customer_id}}" class="list-group-item">
                        <h5 class="list-group-item-text">{{$listTodayEvent->begin_at}}
                            - @if($listTodayEvent->end_at == ""){{ "Nill" }} @else {{$listTodayEvent->end_at}} @endif</h5>
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
                    <a href="#"
                       class="list-group-item">{{ trans('messages.dashboardItemHeaderNoTodayListMessage') }}</a>
                @endforelse
            </div>
            <br>

            <div class="list-group">
                <a href="#" class="list-group-item active">
                    <span class="badge">{{ count($listTodayTomorrowEvents->getTomorrowEvents()) }}</span>
                    {{ trans('messages.dashboardItemHeaderTomorrowList') }}
                </a>
                @forelse($listTodayTomorrowEvents->getTomorrowEvents() as $listTomorrowEvent)
                    <a href="/customer/details/{{$listTomorrowEvent->customer_id}}" class="list-group-item">
                        <h5 class="list-group-item-text">{{$listTomorrowEvent->begin_at}}
                            - @if($listTomorrowEvent->end_at == ""){{ "Nill" }} @else {{$listTomorrowEvent->end_at}} @endif</h5>
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
                    <a href="#"
                       class="list-group-item">{{ trans('messages.dashboardItemHeaderNoTomorrowListMessage') }}</a>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush


