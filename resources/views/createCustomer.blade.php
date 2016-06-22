@extends('layouts.app')

@section('style')
    <link rel="stylesheet" href="/assets/css/editor.css">
@endsection

@section('content')
    <div class="row">
        <h1 class="page-header">Create Customer</h1>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="addCustomerFrm" action="{{ url('/customer/save') }}" method="post">
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">First name</label>
                    <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Last name</label>
                    <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Email</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Phone</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="payment">Payment</label><br />
                    <label class="radio-inline"><input type="radio" name="payment" value="creditcard">Credit card</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="bank">Bank</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="cash" checked="checked">Cash</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="invoice">Invoice</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">License Plate</label>
                    <input type="text" class="form-control" name="license" value="{{ old('license') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Chassis number</label>
                    <input type="text" class="form-control" name="chassis" value="{{ old('chassis') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Mileage</label>
                    <input type="text" class="form-control" name="mileage" value="{{ old('mileage') }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="tuning">Tuning</label><br />
                    <label class="radio-inline"><input type="radio" name="tuning" value="yes">Yes</label>
                    <label class="radio-inline"><input type="radio" name="tuning" value="no" checked="checked">No</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="dyno" >Dyno</label><br />
                    <label class="radio-inline"><input type="radio" name="dyno" value="yes">Yes</label>
                    <label class="radio-inline"><input type="radio" name="dyno" value="no" checked="checked">No</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="name">Freetext</label>
                    <textarea id="txtEditor" name="freetext">{{ old('freetext') }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" id="btnCreate" class="btn btn-primary btn-lg btn-block">Save</button>
            </div>
            <input type="hidden" name="address_places" id="addressval">

        </form>
    </div>
@endsection

@push('script')
    <script src="/assets/js/editor.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>
    <script>
        //contact section city- google places autocomplete
        $("#addCustomerFrm").ready( function () {
            var inputID = "address";
            var placeSearch, autocomplete;
            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            };
            autocomplete = new google.maps.places.Autocomplete(
                    (document.getElementById(inputID)),
                    {types: ['geocode']});
            autocomplete.addListener('place_changed', fillInAddress);

            function fillInAddress() {
                var place = autocomplete.getPlace();
                var params = '';
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];
                    if (componentForm[addressType]) {
                        var val = place.address_components[i][componentForm[addressType]];
                        if(params!='')
                            params = params+'|';
                        params += addressType+':'+val;
                    }
                }
                $('#addressval').val(params);
            }
        });


        $(document).ready(function() {
            $("#txtEditor").Editor({
                'l_align':false,
                'r_align':false,
                'c_align':false,
                'justify':false,
                'insert_link':false,
                'unlink':false,
                'insert_img':false,
                'hr_line':false,
                'block_quote':false,
                'source':false,
                'strikeout':true,
                'indent':false,
                'outdent':false,
                'fonts':false,
                'styles':false,
                'print':false,
                'rm_format':false,
                'status_bar':false,
                'font_size':false,
                'color':false,
                'splchars':false,
                'insert_table':false,
                'select_all':false,
                'togglescreen':false
            });

            $("#txtEditor").Editor("setText", $("#txtEditor").text());

        });

        $('#btnCreate').click( function () {
            $("#txtEditor").html($("#txtEditor").Editor("getText"));
        })

        $('div.alert').delay(3000).slideUp(300);
    </script>
@endpush
