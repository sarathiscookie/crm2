@extends('layouts.app')

@section('title', 'Customer details')

@section('style')
    <link rel="stylesheet" href="/assets/css/editor.css">
    <style>
        #search-result{
            background: transparent none repeat scroll 0% 0%;
            position: absolute;
            z-index: 100;
            left:0;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        #search-result .insearch {
            background: #FFF;
            box-shadow: 0 20px 15px rgba(0,0,0,.2);
            border: solid 1px #bebebe;
            border-top: 0;
            padding: 10px;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        #search-result a{
            cursor: pointer;
        }
    </style>
@endsection

@section('content')

    <h1 class="page-header">Kunden-Details</h1>
    <div class="row">
        <div class="col-md-6">
            <h2>{{ title_case($customer->firstname) }}  {{ title_case($customer->lastname) }} ( {{ $customer->erp_id }} )</h2>
            @if($customer->company)<label>{{ $customer->company }}</label>@endif <br>
            <address>
                @if($customer->additional_address){{ $customer->additional_address }}@endif<br>
                @if($customer->street){{ $customer->street }}@endif<br>
                    @if($customer->postal){{ $customer->postal }}@endif {{ $customer->city }}<br>
                    {{ $customer->country_long }}

            </address>
            <label>E-Mail: <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></label><br>
            <label>Telefon: {{ $customer->phone_1 }} </label>
            @if($customer->phone_2) <br><label>Telefon 2: {{ $customer->phone_2 }} </label> @endif
            @if($customer->phone_mobile) <br><label>Mobile: {{ $customer->phone_mobile }} </label> @endif
            <hr>
            <h3>Termine</h3>
            <div class="panel-group" id="accordionEvent" role="tablist" aria-multiselectable="true">

                {!! $events !!}

            </div>
        </div>
        <div class="col-md-6">
            <h2>Fahrzeuge <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#addVehicleModal">Neues Fahrzeug hinzufügen</button></h2>
            <div class="panel-group" id="accordionVehicle" role="tablist" aria-multiselectable="true">

                {!! $vehicles !!}

            </div>
        </div>
    </div>
    {{--Add car Modal--}}
    <div class="modal fade" id="addVehicleModal" tabindex="-1" role="dialog" aria-labelledby="addVehicleModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addVehicleModalLabel">Neues Fahrzeug hinzufügen</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert" id="errMsg" style="display: none"></div>
                    <form id="addVehicleFrm" method="post" action="{{ url('/vehicle/save') }}">
                        {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-9">
                            <label for="name">Vehicle</label>
                            <div id="vehicleInputBox">
                                <input type="text" class="form-control" name="vehicle" id="vehicle"  onkeydown="down()" onkeyup="up()" autocomplete="off" value="">
                                <div id="search-result" class="search-box col-md-12"></div>
                            </div>
                            <div id="vehicleAppendDiv" style="display:none">
                                <input type="hidden" id="executionId" name="vehicle" value="">
                                <div class="well well-sm" id="vehicleAppend"></div>
                            </div>
                        </div>
                        <div class="form-group col-md-9">
                            <label for="name">Fahrgestellnummer</label>
                            <input type="text" class="form-control txtInput" id="chassis" name="chassis" value="">
                        </div>
                        <div class="form-group col-md-9">
                            <label for="name">KFZ-Kennzeichen</label>
                            <input type="text" class="form-control txtInput" id="license" name="license" value="">
                        </div>
                        <div class="form-group col-md-9">
                            <label for="name">Gearbox</label>
                            <select class="form-control" id="gearbox" name="gearbox">
                                @foreach($gears as $optKey => $optLabel)
                                    <option @if($optKey==1) selected="selected" @endif value="{{ $optKey }}">{{ $optLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="name">Zusatzinformationen</label>
                            <textarea id="txtEditor" name="freetext"></textarea>
                        </div>
                    </div>
                        <input type="hidden" name="customer" value="{{ $customer->id }}">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary disabled" disabled="disabled" id="btnSave" data-loading-text="Saving..." autocomplete="off">Save</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    {{--Add car Modal End--}}

    {{--Hidden info Modal--}}
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="infoModalLabel">Hidden Info</h4>
                </div>
                <div class="modal-body">
                    <img src="/assets/img/loading.gif" class="media-middle info-loader hidden" width="32px" alt="loading" >
                    <div id="infoContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    {{--Hidden info Modal End--}}
@endsection

@push('script')
<script src="/assets/js/editor.js"></script>
<script>
    /*CSRF _token for ajax post methods*/
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                        var customer_id  = '{{ $customer->id }}';
                        var execution_id = $(this).attr("data-id");
                        var data_model   = $(this).attr("data-model");
                        $.post("/vehicle/check", {customer:customer_id, execution_id:execution_id}, function (data) {
                            if(data==0){
                                $('#errMsg').hide();
                                $("#vehicleInputBox").hide();
                                $("#executionId").attr("value", execution_id);
                                $("#vehicleAppend").html(data_model +'<span class="glyphicon glyphicon glyphicon-remove pull-right" aria-hidden="true" style="cursor: pointer;"></span>');
                                $("#vehicleAppendDiv").show();
                                if($("#executionId").val()>0)
                                    $('#btnSave').attr('disabled', false).removeClass('disabled');

                                $(".glyphicon-remove").on("click", function(){
                                    $("#vehicleInputBox").show();
                                    $("#vehicleAppendDiv").hide();
                                    $("#executionId").val('');
                                    $('#btnSave').attr('disabled', true).addClass('disabled');
                                });
                            }
                            else {
                                $('#errMsg').html('<strong>Error!</strong> ' + data);
                                $('#errMsg').fadeIn();
                                return false;
                            }
                        });
                    });
                });
            }
            if(keywords.length == 0){
                $("#search-result").fadeOut("fast");
            }
        }, 500);
    }
    /*On Key down event*/
    function down(){
        clearTimeout(timer);
    }

    /*WYSIWYG Editor Init*/
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

    /*Submit Form- button action*/
    $('#btnSave').click( function () {
        $('#errMsg').html('');
        $('#errMsg').fadeOut("fast");
        $("#txtEditor").html($("#txtEditor").Editor("getText"));
        if($.trim($('#executionId').val())=='' || $('#executionId').val()==0){
            $('#errMsg').html('<strong>Error!</strong> Vehicle should not be empty');
            $('#errMsg').fadeIn("fast");
            return false;
        }
        else if($.trim($('#chassis').val())==''){
            $('#errMsg').html('<strong>Error!</strong> Fahrgestellnummer should not be empty');
            $('#errMsg').fadeIn("fast");
            $('#chassis').focus();
            return false;
        }
        else if($.trim($('#license').val())==''){
            $('#errMsg').html('<strong>Error!</strong> KFZ-Kennzeichen should not be empty');
            $('#errMsg').fadeIn("fast");
            $('#license').focus();
            return false;
        }
        var $btn = $(this).button('loading');
        setTimeout(function(){
            $("#addVehicleFrm").submit();
            $btn.button('reset');
        }, 1000)

    });

    /*Modal close event action - Reset form and other credentials*/
    $('#addVehicleModal').on('hidden.bs.modal', function () {
        $("#addVehicleFrm")[0].reset();
        $("#txtEditor").Editor("setText", '');
        $("#executionId").val('');
        $("#vehicleInputBox").show();
        $("#vehicleAppendDiv").hide();
        $("#search-result").fadeOut("fast");
        $('#errMsg').fadeOut("fast");
    });

    /*Fadeout error message if input not empty*/
    $('.txtInput').blur( function () {
        if($.trim($(this).val())!='')
            $('#errMsg').fadeOut();
    });


    /*Vehicle documents - File upload*/
    $('#accordionVehicle').on('change', '.upload-input', function(e){
        e.preventDefault();
        var formData = new FormData($(this).parents('form')[0]);
        var elementID = $(this).attr('id').split('_')[1];
        $('.file-loader').toggleClass('invisible');

        $.ajax({
            url: '/vehicle/upload',
            type: 'POST',
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                return myXhr;
            },
            success: function (data) {
                $('.file-loader').toggleClass('invisible');
                $('#fileList_'+elementID).html(data);
                $('.upload-input').val('');
            },
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    });

    /*Fetch hidden info - Modal loading*/
    $('#infoModal').on('shown.bs.modal', function (e) {
        $('.info-loader').toggleClass('hidden');
        var id = e.relatedTarget.id;
        var content='';
        $.get( "/event/info", { event_id: id }, function( data ) {
            $('.info-loader').toggleClass('hidden');
            if(data.mes=='done') {
                content = '<label>{{ trans('messages.customerCreateFormLabelFreetextInternal') }} : </label>' + data.response.freetext_internal;
                $('#infoContent').html(content);
            }
            else {
                content = '<div class="alert alert-danger">' + data.mes + '</div>';
                $('#infoContent').html(content);
            }
        }, 'json');
    });
    $('#infoModal').on('hidden.bs.modal', function (e) {
        $('#infoContent').html('');
    });
</script>
@endpush