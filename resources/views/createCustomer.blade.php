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

@inject('typesCustomerAndGearbox', 'App\Http\Controllers\CustomerController')

@section('content')
        <h1 class="page-header">Kunden hinzufügen</h1>
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
                    <label for="name">Firma</label>
                    <input type="text" class="form-control" name="company" value="{{ old('company') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Vorname</label>
                    <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Nachname</label>
                    <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">E-Mail Adresse</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Adresse</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Telefon</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="payment">Zahlungsweise</label><br />
                    <label class="radio-inline"><input type="radio" name="payment" value="cash" checked="checked">Barzahlung</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="bank">EC-Karte</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="creditcard">Kreditkarte</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="paypal">PayPal</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="invoice">Rechnung</label>
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
                        <input type="hidden" id="remoteVehicleId" name="vehicle" value="">
                        <div class="well well-sm" id="vehicleAppend"></div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Stage</label>
                    <select class="form-control" name="stage" id="stage">
                        <option value="">Choose Stage</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">KFZ-Kennzeichen</label>
                    <input type="text" class="form-control" name="license" value="{{ old('license') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Fahrgestellnummer</label>
                    <input type="text" class="form-control" name="chassis" value="{{ old('chassis') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Kilometerstand</label>
                    <input type="text" class="form-control mileageNumber" name="mileage" value="{{ old('mileage') }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="tuning">Tuning bereits vorhanden?</label><br />
                    <label class="radio-inline"><input type="radio" name="tuning" value="yes">Ja</label>
                    <label class="radio-inline"><input type="radio" name="tuning" value="no" checked="checked">Nein</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="dyno" >Prüfstandslauf</label><br />
                    <label class="radio-inline"><input type="radio" name="dyno" value="yes">Ja</label>
                    <label class="radio-inline"><input type="radio" name="dyno" value="no" checked="checked">Nein</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="gearbox">Gearbox</label>
                    <select class="form-control" name="gearbox" id="gearbox">
                        @foreach($typesCustomerAndGearbox->gearbox() as $key=>$gearboxeTypes)
                            <option value="{{ $key }}">{{ $gearboxeTypes }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="gearbox">Customer Status</label>
                    <select class="form-control" name="customerstatus" id="customerstatus">
                        <option value="">Choose customer status</option>
                        @foreach($typesCustomerAndGearbox->customerStatus() as $key=>$custStatus)
                            <option value="{{ $key }}">{{ $custStatus }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="name">Bereits verbaute Komponenten</label>
                    <ul class="tag-handler form-control">
                    </ul>
                    <input type="hidden" id="hardwares" name="hardwares" value="">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="name">Zusatzinformationen</label>
                    <textarea id="txtEditor" name="freetext">{{ old('freetext') }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <button type="button" id="btnCreate" class="btn btn-primary btn-lg btn-block">Kunde & Termin anlegen</button>
            </div>
            <input type="hidden" name="street_number" id="street_number" value="{{ old('street_number') }}">
            <input type="hidden" name="route" id="route" value="{{ old('route') }}">
            <input type="hidden" name="city" id="locality" value="{{ old('city') }}">
            <input type="hidden" name="state" id="administrative_area_level_1" value="{{ old('state') }}">
            <input type="hidden" name="country" id="country" value="{{ old('country') }}">
            <input type="hidden" name="postal" id="postal_code" value="{{ old('postal') }}">
        </form>
@endsection

@push('script')
    <script src="/assets/js/jquery.taghandler.js"></script>
    <script src="/assets/js/editor.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDBbZst8ih34yxe9TZYH6Em8IQN0zGHU-Y&libraries=places"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //Add thousand seperator in mileage field
        $('input.mileageNumber').keyup(function(event) {

            // skip for arrow keys
            if(event.which >= 37 && event.which <= 40) return;

            // format number
            $(this).val(function(index, value) {
                return value
                        .replace(/\D/g, "")
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".")
                        ;
            });
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
                $('#administrative_area_level_1').val('');
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
            autoUpdate: true,
            minChars:2,
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
                            $("#remoteVehicleId").attr("value", $(this).attr("data-id"));
                            $("#vehicleAppend").html($(this).attr("data-model")+'<span class="glyphicon glyphicon glyphicon-remove pull-right" aria-hidden="true" style="cursor: pointer;"></span>');
                            $("#vehicleAppendDiv").show();

                            $(".glyphicon-remove").on("click", function(){
                                $("#vehicleInputBox").show();
                                $("#vehicleAppendDiv").hide();
                                $("#remoteVehicleId").val('');
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
