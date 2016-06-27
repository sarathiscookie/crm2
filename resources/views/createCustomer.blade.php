@extends('layouts.app')

@section('title',' Create Customer')

@section('style')
    <link rel="stylesheet" href="/assets/css/jquery.taghandler.css">
    <link rel="stylesheet" href="/assets/css/editor.css">
    <style>
        #search-result{
            background: transparent none repeat scroll 0% 0%;
            position: absolute;
            display: block;
            left: 15px;
            top: 62px;
            z-index: 1;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        #search-result .insearch {
            background: #FFF;
            box-shadow: 0 20px 15px rgba(0,0,0,.2);
            border: solid 1px #bebebe;
            border-top: 0;
            width: 301px;
            padding: 10px;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
    </style>
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
                <div class="form-group col-md-12">
                    <label for="name">Company</label>
                    <input type="text" class="form-control" name="company" value="{{ old('company') }}">
                </div>
            </div>
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
                    <label class="radio-inline"><input type="radio" name="payment" value="paypal">Paypal</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="cash" checked="checked">Cash</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="invoice">Invoice</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Vehicle</label>
                    <div id="vehicleInputBox">
                        <input type="text" class="form-control" name="vehicle" id="vehicle"  onkeydown="down()" onkeyup="up()" autocomplete="off" value="">
                        <div id="search-result" class="col-md-6 search-box"></div>
                    </div>
                    <div id="vehicleAppendDiv" style="display:none">
                        <input type="hidden" id="pgsqlVehicleId" name="vehicle" value="">
                        <div class="well well-sm" id="vehicleAppend"></div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Stage</label>
                    <input type="text" class="form-control" name="stage" value="">
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
                    <label for="name">Vehicle Hardware</label>
                    <ul class="tag-handler form-control">
                    </ul>
                    <input type="hidden" id="hardwares" name="hardwares" value="">
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
            <input type="hidden" name="state" id="administrative_area_level_1" value="{{ old('state') }}">
            <input type="hidden" name="country" id="country" value="{{ old('country') }}">
            <input type="hidden" name="postal" id="postal_code" value="{{ old('postal') }}">
        </form>
    </div>
@endsection

@push('script')
    <script src="/assets/js/jquery.taghandler.js"></script>
    <script src="/assets/js/editor.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
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

        /* Tag handler */
        $(".tag-handler").tagHandler({
            getURL: '/tag/hardware',
            autocomplete: true,
            initLoad: false,
            autoUpdate: true,
            onAdd: function (tag) {
                assignedTags: [ tag ],
                        $('#hardwares').val(function(i,val) {
                            return val + (!val ? '' : ',') + tag;
                        });
            },
            afterDelete: function (tag) {
                $("#hardwares").val($(".tag-handler").tagHandler("getSerializedTags"));
            }
        });

        /* Search vehicles */
        var timer;
        function up(){
            timer = setTimeout(function(){
                var keywords = $("#vehicle").val();

                if(keywords.length >0){
                    $.post("/search/vehicle", {keywords: keywords}, function(response){
                        $("#search-result").html(response);
                        $("#search-result").fadeIn("fast");

                        $(".list-group-item").on("click", function(){
                            $("#vehicleInputBox").hide();
                            $("#pgsqlVehicleId").attr("value", $(this).attr("data-id"));
                            $("#vehicleAppend").html($(this).attr("data-model")+'<span class="glyphicon glyphicon glyphicon-remove pull-right" aria-hidden="true" style="cursor: pointer;"></span>');
                            $("#vehicleAppendDiv").show();

                            $(".glyphicon-remove").on("click", function(){
                                $("#vehicleInputBox").show();
                                $("#vehicleAppendDiv").hide();
                            });
                        });
                        //$('#loadings').fadeOut('slow');
                    });
                }
                if(keywords.length == 0){
                    /* $(".searchPanelBody").hide();
                     $('#loadings').fadeOut('slow');*/
                     $("#search-result").fadeOut("fast");
                }
            }, 500);
        }

        function down(){
            /*$('#loadings').html('<img src="/assets/img/loading.gif" alt="loading" class="media-middle loadingIcn" width="24px">').fadeIn('slow');*/
            clearTimeout(timer);
        }
    </script>
@endpush
