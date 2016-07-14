@extends('layouts.app')

@section('title', 'Edit Customer')

@inject('customerObj', 'App\Http\Controllers\CustomerController')

@section('style')
    <link rel="stylesheet" href="/assets/css/editor.css">
    <style>
        #advertiser-result {
            background: transparent none repeat scroll 0% 0%;
            position: absolute;
            display: block;
            left: 15px;
            top: 62px;
            z-index: 1;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        #advertiser-result.insearch {
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
        <h1 class="page-header">{{ trans('messages.customerEditFormHeadingLabel') }} <small>( {{ $customer->erp_id }} )</small></h1>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="edtCustomerFrm" action="{{ url('/customer/update/'.$customer->id) }}" method="post">
            {{ csrf_field() }}
            <div class="row">
                @if($customer->advertiser_id>0)
                    <div class="form-group col-md-6">
                        <label class="control-label">{{ trans('messages.customerCreateFormLabelAdvertiser') }} </label>
                        <p class="form-control-static">{{ $customerObj->getAdvertiser($customer->advertiser_id) }}</p>
                    </div>
                @else
                    <div class="form-group col-md-6">
                        <label for="name">{{ trans('messages.customerCreateFormLabelAdvertiser') }}</label>
                        <div id="advertiserInput">
                            <input type="text" class="form-control" name="advertiser" id="advertiser"  onkeydown="clearOut()" onkeyup="doSearch()" autocomplete="off" value="">
                            <div id="advertiser-result" class="col-md-6 search-box"></div>
                        </div>
                        <div id="selectedIdDiv" style="display:none">
                            <input type="hidden" id="advertiser_id" name="advertiser_id" value="">
                            <div class="well well-sm" id="selectedId"></div>
                        </div>
                    </div>
                @endif
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelCompany') }}</label>
                    <input type="text" class="form-control" name="company" value="{{ $customer->company }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelTitle') }}</label>
                    <select name="title" class="form-control">
                        @foreach($customerTitles as $titleKay =>$titleLabel)
                            <option @if($titleKay==$customer->title || $titleKay==old('title')) selected="selected" @endif value="{{ $titleKay }}">{{ $titleLabel }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelFirstName') }}</label>
                    <input type="text" class="form-control" name="firstname" value="{{ $customer->firstname }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelLastName') }}</label>
                    <input type="text" class="form-control" name="lastname" value="{{ $customer->lastname }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelEmail') }}</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ $customer->email }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label class="control-label">{{ trans('messages.customerCreateFormLabelAddress') }} </label>
                    <p class="form-control-static">@if($customer->street){{ $customer->street }}, @endif
                        @if($customer->postal){{ $customer->postal }}@endif {{ $customer->city }},
                        {{ $customer->country_long }}</p>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.customerCreateFormLabelAdditionalAddress') }}</label>
                    <input type="text" class="form-control" id="additional_address" name="additional_address" value="{{ $customer->additional_address }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-2">
                    <label for="name">{{ trans('messages.customerCreateFormLabelPhone1') }}</label>
                    <input type="text" class="form-control" name="phone" value="{{ $customer->phone_1 }}">
                </div>
                <div class="form-group col-md-2">
                    <label for="name">{{ trans('messages.customerCreateFormLabelPhone2') }}</label>
                    <input type="text" class="form-control" name="phone_2" value="{{ $customer->phone_2 }}">
                </div>
                <div class="form-group col-md-2">
                    <label for="name">{{ trans('messages.customerCreateFormLabelPhone') }}</label>
                    <input type="text" class="form-control" name="phone_mobile" value="{{ $customer->phone_mobile }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="status">{{ trans('messages.customerCreateFormLabelCustomerStatus') }}</label>
                    <select class="form-control" name="status" id="statusInp">
                        @foreach($customerStatus as $key=>$statusLabel)
                            <option @if($key == $customer->status || $key == old('status')) selected="selected" @endif value="{{ $key }}">{{ $statusLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="name">{{ trans('messages.customerEditFormLabelFreetext') }}</label>
                    <textarea id="txtEditor" name="freetext">{{ $customer->freetext }}</textarea>
                </div>
            </div>
            <div class="form-group">
                <button type="button" id="btnCreate" class="btn btn-primary btn-lg btn-block">{{ trans('messages.customerEditFormSubmitButton') }}</button>
            </div>
        </form>
@endsection

@push('script')
    <script src="/assets/js/editor.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDBbZst8ih34yxe9TZYH6Em8IQN0zGHU-Y&libraries=places"></script>
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

        //Initialize WYSIWYG Editor
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
        });

        //Submit Form- button action
        $('#btnCreate').click( function () {
            $("#txtEditor").html($("#txtEditor").Editor("getText"));
            $("#edtCustomerFrm").submit();
        });

        /*Search customers as Advertiser*/
        var timer;
        function doSearch(){
            timer = setTimeout(function(){
                var keywords = $("#advertiser").val();
                if(keywords.length >0){
                    $.post("/search/advertiser", {keywords: keywords}, function(response){
                        $("#advertiser-result").html(response);
                        $("#advertiser-result").fadeIn("fast");

                        $(".list-group-item").on("click", function(){
                            $("#advertiserInput").hide();
                            $("#advertiser_id").attr("value", $(this).attr("data-id"));
                            $("#selectedId").html($(this).attr("data-model")+'<span class="glyphicon glyphicon glyphicon-remove pull-right" aria-hidden="true" style="cursor: pointer;"></span>');
                            $("#selectedIdDiv").show();

                            $(".glyphicon-remove").on("click", function(){
                                $("#advertiserInput").show();
                                $("#selectedIdDiv").hide();
                                $("#advertiser_id").val('');
                            });
                        });
                    });
                }
                if(keywords.length == 0){
                    $("#advertiser-result").fadeOut("fast");
                }
            }, 500);
        }

        function clearOut(){
            clearTimeout(timer);
        }
    </script>
@endpush
