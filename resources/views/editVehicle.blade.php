@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('style')
    <link rel="stylesheet" href="/assets/css/editor.css">
@endsection

@section('content')
    <h1 class="page-header">{{ trans('messages.editVehicleHeader') }}</h1>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="edtVehicleFrm" method="post" action="{{ url('/vehicle/update/'.$vehicle->id) }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="form-group col-md-6">
                <label class="control-label">{{ trans('messages.editVehicleVehicleLabel') }}</label>
                <p class="form-control-static">{{ $vehicle_title }}</p>
            </div>
            <div class="form-group col-md-6">
                <label for="name">{{ trans('messages.editVehicleChassisNumberLabel') }}</label>
                <input type="text" class="form-control txtInput" id="chassis" name="chassis" value="{{ $vehicle->chassis_number or old('chassis') }}">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <label for="name">{{ trans('messages.editVehicleLicensePlateLabel') }}</label>
                <input type="text" class="form-control txtInput" id="license" name="license" value="{{ $vehicle->license_plate or old('license') }}">
            </div>
            <div class="form-group col-md-6">
                <label for="name">{{ trans('messages.editVehicleGearboxLabel') }}</label>
                <select class="form-control" id="gearbox" name="gearbox">
                    @foreach($gears as $optKey => $optLabel)
                        <option @if($optKey==$vehicle->gearbox || $optKey==old('gearbox')) selected="selected" @endif value="{{ $optKey }}">{{ $optLabel }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-12">
                <label for="name">{{ trans('messages.editVehicleAdditionalInformationLabel') }}</label>
                <textarea id="txtEditor" name="freetext">{{ $vehicle->freetext or old('freetext') }}</textarea>
            </div>
        </div>
        <div class="row">
            @if($vehicle->status=='online')
            <div class="col-md-6">
                <button type="button" class="btn btn-danger btn-lg btn-block" id="btnDel">{{ trans('messages.editVehicleDeleteButton') }}</button>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary btn-lg btn-block" id="btnUpdate">{{ trans('messages.editVehicleUpdateButton') }}</button>
            </div>
            @else
                <div class="col-md-12">
                    <button type="button" class="btn btn-primary btn-lg btn-block" id="btnUpdate">{{ trans('messages.editVehicleUpdateButton') }}</button>
                </div>
            @endif
        </div>
    </form>

@endsection
@push('script')
<script src="/assets/js/editor.js"></script>
<script>
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
    $('#btnUpdate').click( function () {
        $("#txtEditor").html($("#txtEditor").Editor("getText"));
        $("#edtVehicleFrm").submit();
    });

    /*Delete btn action*/
    var vehicle = {{  $vehicle->id }}
    $('#btnDel').click( function () {
        if(confirm('Do you really want to delete this?')){
            $.post('/vehicle/delete', { id: vehicle }, function ( data ) {
                if(data.mes=='done') {
                    window.location = '{{ url("/customer/details") }}/'+ data.ref;
                }
                else {
                    var err = '<div class="alert alert-danger">'+ data.mes +'</div>';
                    $(err).insertBefore('#edtVehicleFrm');
                }
            },'json');


        }
    });

    /*CSRF _token for ajax post methods*/
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@endpush