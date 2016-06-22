@extends('layouts.app')

@section('title',' Create Customer')

@section('style')
    <link rel="stylesheet" href="/assets/css/editor.css">
@endsection

@section('content')
    <div class="row">
        <h1 class="page-header">Create Customer</h1>
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
                <button type="button" id="btnCreate" class="btn btn-primary btn-lg btn-block">Save</button>
            </div>
            <input type="hidden" name="street_number" id="street_number" value="{{ old('street_number') }}">
            <input type="hidden" name="route" id="route" value="{{ old('route') }}">
            <input type="hidden" name="city" id="locality" value="{{ old('city') }}">
            <input type="hidden" name="country" id="country" value="{{ old('country') }}">
            <input type="hidden" name="postal" id="postal_code" value="{{ old('postal') }}">
        </form>
    </div>
@endsection

@push('script')
    <script src="/assets/js/editor.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>
    <script>
        //Customer Address - google places auto-complete
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
                $('#street_number').val('');
                $('#route').val('');
                $('#locality').val('');
                $('#country').val('');
                $('#postal_code').val('');
                var place = autocomplete.getPlace();
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];
                    if (componentForm[addressType]) {
                        var val = place.address_components[i][componentForm[addressType]];
                        $('#'+addressType).val(val);
                    }
                }
            }
        });

        //WYSIWYG Editor
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

        //Submit Form- button action
        $('#btnCreate').click( function () {
            $("#txtEditor").html($("#txtEditor").Editor("getText"));
            $("#addCustomerFrm").submit();
        });
    </script>
@endpush
