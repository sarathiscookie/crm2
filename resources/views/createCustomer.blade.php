@extends('layouts.app')

@section('title',' Create Customer')

@section('style')
    <link rel="stylesheet" href="/assets/css/jquery.taghandler.css">
    <link rel="stylesheet" href="/assets/css/editor.css">
    <link rel="stylesheet" href="/assets/css/daterangepicker.css">
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
        <h1 class="page-header">{{ trans('messages.customerCreateFormHeadingLabel') }}</h1>
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
                    <label for="name">{{ trans('messages.customerCreateFormLabelCompany') }}</label>
                    <input type="text" class="form-control" name="company" value="{{ old('company') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelTitle') }}</label>
                    <select name="title" class="form-control">
                        @foreach($typesCustomerAndGearbox->customerTitle as $titleKay =>$titleLabel)
                            <option @if($titleKay==1 || $titleKay==old('title')) selected="selected" @endif value="{{ $titleKay }}">{{ $titleLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelFirstName') }}</label>
                    <input type="text" class="form-control" name="firstname" value="{{ old('firstname') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelLastName') }}</label>
                    <input type="text" class="form-control" name="lastname" value="{{ old('lastname') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelEmail') }}</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelAddress') }}</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelAdditionalAddress') }}</label>
                    <input type="text" class="form-control" id="additional_address" name="additional_address" value="{{ old('additional_address') }}">
                </div>
                <div class="form-group col-md-2">
                    <label for="name">{{ trans('messages.customerCreateFormLabelPhone1') }}</label>
                    <input type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                </div>
                <div class="form-group col-md-2">
                    <label for="name">{{ trans('messages.customerCreateFormLabelPhone2') }}</label>
                    <input type="text" class="form-control" name="phone_2" value="{{ old('phone_2') }}">
                </div>
                <div class="form-group col-md-2">
                    <label for="name">{{ trans('messages.customerCreateFormLabelPhone') }}</label>
                    <input type="text" class="form-control" name="phone_mobile" value="{{ old('phone_mobile') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelVehicle') }}</label>
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
                    <label for="payment">{{ trans('messages.customerCreateFormLabelPayment') }}</label><br />
                    <label class="radio-inline"><input type="radio" name="payment" value="cash" checked="checked">{{ trans('messages.customerCreateFormPaymentLabelCash') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="bank">{{ trans('messages.customerCreateFormPaymentLabelBank') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="creditcard">{{ trans('messages.customerCreateFormPaymentLabelCreditcard') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="paypal">{{ trans('messages.customerCreateFormPaymentLabelPaypal') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="invoice">{{ trans('messages.customerCreateFormLabelVehicleInvoice') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelStage') }}</label>
                    <select class="form-control" name="stage" id="stage">
                        @for($i=1;$i<=5;$i++)
                            <option @if($i ==1 || $i== old('stage')) selected="selected" @endif value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label for="name">{{ trans('messages.customerCreateFormLabelLicensePlate') }}</label>
                    <input type="text" class="form-control" name="license" value="{{ old('license') }}">
                </div>
                <div class="form-group col-md-4">
                    <label for="name">{{ trans('messages.customerCreateFormLabelChassis') }}</label>
                    <input type="text" class="form-control" name="chassis" value="{{ old('chassis') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelMileage') }}</label>
                    <input type="text" class="form-control mileageNumber" name="mileage" value="{{ old('mileage') }}">
                </div>
                <div class="form-group col-md-3">
                    <label for="tuning">{{ trans('messages.customerCreateFormLabelTuning') }}</label><br />
                    <label class="radio-inline"><input type="radio" name="tuning" value="yes">{{ trans('messages.customerCreateFormTuningLabelYes') }}</label>
                    <label class="radio-inline"><input type="radio" name="tuning" value="no" checked="checked">{{ trans('messages.customerCreateFormTuningLabelNo') }}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="dyno" >{{ trans('messages.customerCreateFormLabelDyno') }}</label><br />
                    <label class="radio-inline"><input type="radio" name="dyno" value="yes">{{ trans('messages.customerCreateFormDynoLabelYes') }}</label>
                    <label class="radio-inline"><input type="radio" name="dyno" value="no" checked="checked">{{ trans('messages.customerCreateFormDynoLabelNo') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="gearbox">{{ trans('messages.customerCreateFormLabelGearbox') }}</label>
                    <select class="form-control" name="gearbox" id="gearbox">
                        @foreach($typesCustomerAndGearbox->gearbox() as $key=>$gearboxeTypes)
                            <option @if($key == 1 || $key == old('gearbox')) selected="selected" @endif value="{{ $key }}">{{ $gearboxeTypes }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="gearbox">{{ trans('messages.customerCreateFormLabelCustomerStatus') }}</label>
                    <select class="form-control" name="customerstatus" id="customerstatus">
                        @foreach($typesCustomerAndGearbox->customerStatus() as $key=>$custStatus)
                            <option @if($key == 'customer' || $key == old('customerstatus')) selected="selected" @endif value="{{ $key }}">{{ $custStatus }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelHardwareTag') }}</label>
                    <ul class="tag-handler form-control" style="margin: 0">
                    </ul>
                    <input type="hidden" id="hardwares" name="hardwares" value="">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Begin_at - End_at</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="eventrange" name="eventrange" value="{{ old('eventrange') }}" readonly aria-describedby="cal-addon">
                        <span class="input-group-addon" id="cal-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelFreetext') }}</label>
                    <textarea id="txtEditor" name="freetext">{{ old('freetext') }}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelFreetextInternal') }}</label>
                    <textarea id="txtEditor_i" name="freetext_internal">{{ old('freetext_internal') }}</textarea>
                </div>
            </div>
            @foreach($typesCustomerAndGearbox->showFormGroup() as $formGroup)
                <h4 class="page-header">{{ $formGroup->title }}</h4>
                @foreach($typesCustomerAndGearbox->showFormFields($formGroup->id) as $formField)
                    @if ($formField->type == 'input')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="{{ $formField->title }}">{{ $formField->title }}</label>
                                <input type="text" class="form-control" name="dynField_{{$formField->id}}" id="fieldInput" placeholder="{{ $formField->placeholder }}" value="{{ old('dynField_'.$formField->id) }}" maxlength="100">
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'textarea')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="{{ $formField->title }}">{{ $formField->title }}</label>
                                <textarea class="form-control" name="dynField_{{$formField->id}}" rows="3" id="fieldTextarea" placeholder="{{ $formField->placeholder }}" maxlength="255">{{ old('dynField_'.$formField->id) }}</textarea>
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'checkbox')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <input type="checkbox" name="dynField_{{$formField->id}}" id="fieldCheckbox{{$formField->id}}" placeholder="{{ $formField->placeholder }}">
                                <label for="fieldCheckbox{{$formField->id}}">{{ $formField->title }}</label>
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'select')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="Lbl{{ $formField->id }}">{{ $formField->title }}</label>
                                <select name="dynField_{{$formField->id}}" id="Lbl{{ $formField->id }}" class="form-control">
                                    @forelse(explode("|",$formField->options) as $options)
                                        <option value="{{ explode(":",$options)[0] }}">{{ explode(":",$options)[1] }}</option>
                                    @empty
                                        <option value="">No options available</option>
                                    @endforelse
                                </select>
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'radio')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>{{ $formField->title }}</label>
                                @foreach(explode("|",$formField->options) as $options)
                                    <input type="radio" name="dynField_{{$formField->id}}" value="{{ explode(":",$options)[0] }}" id="dynField_{{explode(":",$options)[1]}}">
                                    <label for="dynField_{{explode(":",$options)[1]}}">{{ explode(":",$options)[1] }}</label>
                                @endforeach
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
            <div class="form-group">
                <button type="button" id="btnCreate" class="btn btn-primary btn-lg btn-block">{{ trans('messages.customerCreateFormSubmitButton') }}</button>
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
    <script src="/assets/js/moment.min.js"></script>
    <script src="/assets/js/daterangepicker.js"></script>
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
                'l_align':false, 'r_align':false, 'c_align':false,
                'justify':false, 'insert_link':false, 'unlink':false,
                'insert_img':false, 'hr_line':false, 'block_quote':false,
                'source':false, 'strikeout':true, 'indent':false,
                'outdent':false, 'fonts':false, 'styles':false,
                'print':false, 'rm_format':false, 'status_bar':false,
                'font_size':false, 'color':false, 'splchars':false,
                'insert_table':false, 'select_all':false, 'togglescreen':false
            });

            $("#txtEditor").Editor("setText", $("#txtEditor").text());

            $("#txtEditor_i").Editor({
                'l_align':false, 'r_align':false, 'c_align':false,
                'justify':false, 'insert_link':false, 'unlink':false,
                'insert_img':false, 'hr_line':false, 'block_quote':false,
                'source':false, 'strikeout':true, 'indent':false,
                'outdent':false, 'fonts':false, 'styles':false,
                'print':false, 'rm_format':false, 'status_bar':false,
                'font_size':false, 'color':false, 'splchars':false,
                'insert_table':false, 'select_all':false, 'togglescreen':false
            });

            $("#txtEditor_i").Editor("setText", $("#txtEditor_i").text());

        });

        //Submit Form- button action
        $('#btnCreate').click( function () {
            $("#txtEditor").html($("#txtEditor").Editor("getText"));
            $("#txtEditor_i").html($("#txtEditor_i").Editor("getText"));
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

        //Date picker
        $('#eventrange').daterangepicker({
            timePicker:true,
            timePickerIncrement:15,
            timePicker24Hour: true,
            drops: 'up',
            startDate: '{{ $begin_at }}',
            endDate: '{{ $end_at }}',
            locale: {
                "format": "DD-MM-YYYY H:mm",
                "separator": " To ",
            },
        });
    </script>
@endpush
