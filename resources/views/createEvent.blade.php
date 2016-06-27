@extends('layouts.app')

@section('title',' Create Event')

@section('style')
    <link rel="stylesheet" href="/assets/css/editor.css">
    <link rel="stylesheet" href="/assets/css/daterangepicker.css">
@endsection

@section('content')
        <h1 class="page-header">Create Event</h1>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="addEventFrm" action="{{ url('/event/save') }}" method="post">
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Customer</label>
                    <input type="text" class="form-control" name="customer" value="{{ old('customer') }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Car</label>
                    <input type="text" class="form-control" name="vehicle" value="{{ old('vehicle') }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Title</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title') }}">
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
                    <label for="name">Freetext External</label>
                    <textarea id="txtEditor_x" name="freetext_external">{{ old('freetext_external') }}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Freetext Internal</label>
                    <textarea id="txtEditor_i" name="freetext_internal">{{ old('freetext_internal') }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">Kilometerstand</label>
                    <input type="text" class="form-control" name="mileage" value="{{ old('mileage') }}">
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
                    <label for="name">Begin_at - End_at</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="eventrange" name="eventrange" value="{{ old('eventrange') }}" readonly aria-describedby="cal-addon">
                        <span class="input-group-addon" id="cal-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">Price</label>
                    <input type="text" class="form-control" name="price" value="{{ old('price') }}">
                </div>
            </div>
            <div class="row">


            </div>
            <div class="form-group">
                <button type="button" id="btnCreate" class="btn btn-primary btn-lg btn-block">Create Event</button>
            </div>
        </form>
@endsection

@push('script')
    <script src="/assets/js/jquery.taghandler.js"></script>
    <script src="/assets/js/editor.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places"></script>
    <script src="/assets/js/moment.min.js"></script>
    <script src="/assets/js/daterangepicker.js"></script>
    <script>
        //WYSIWYG Editor
        $(document).ready(function() {
            $("#txtEditor_x").Editor({
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

            $("#txtEditor_x").Editor("setText", $("#txtEditor_x").text());

            $("#txtEditor_i").Editor({
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

            $("#txtEditor_i").Editor("setText", $("#txtEditor_i").text());

        });

        //Submit Form- button action
        $('#btnCreate').click( function () {
            $("#txtEditor_x").html($("#txtEditor_x").Editor("getText"));
            $("#txtEditor_i").html($("#txtEditor_i").Editor("getText"));
            $("#addEventFrm").submit();
        });
        $('#eventrange').daterangepicker({
                    timePicker:true,
                    timePickerIncrement:30,
                    timePicker24Hour: true,
                    drops: 'up',
            locale: {
                "format": "DD-MM-YYYY H:mm",
                "separator": " To ",


            },

        });
    </script>
@endpush
