@extends('layouts.app')

@section('title', 'Dashboard')

@section('style')
@endsection

@section('content')
    <div class="col-md-4">
        @if(isset($listCustomers))
        <div class="list-group">
            <a href="#" class="list-group-item active">
                <span class="badge">{{ count($listCustomers) }}</span>
                Customer List
            </a>
            @forelse ($listCustomers as $listCustomer)
                <a href="#" class="list-group-item">
                    <h4 class="list-group-item-heading">{{$listCustomer->firstname}} . {{$listCustomer->lastname}}</h4>
                    <p class="list-group-item-text">{{$listCustomer->company}}</p>
                    <p class="list-group-item-text">{{$listCustomer->erp_id}}</p>
                    <p class="list-group-item-text">{{$listCustomer->created_at}}</p>
                </a>
            @empty
                <p>No new customers</p>
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
                <span class="badge">5</span>
                Today
            </a>
            <a href="#" class="list-group-item">Dapibus ac facilisis in</a>
            <a href="#" class="list-group-item">Morbi leo risus</a>
            <a href="#" class="list-group-item">Porta ac consectetur ac</a>
            <a href="#" class="list-group-item">Vestibulum at eros</a>
        </div>
        <br>

        <div class="list-group">
            <a href="#" class="list-group-item active">
                <span class="badge">5</span>
                Tomorrow
            </a>
            <a href="#" class="list-group-item">Dapibus ac facilisis in</a>
            <a href="#" class="list-group-item">Morbi leo risus</a>
            <a href="#" class="list-group-item">Porta ac consectetur ac</a>
            <a href="#" class="list-group-item">Vestibulum at eros</a>
        </div>
    </div>
@endsection

@push('script')
@endpush


