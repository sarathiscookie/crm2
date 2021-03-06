@extends('layouts.app')

@section('title',' Create Event')

@section('style')
    <link rel="stylesheet" href="/assets/css/jquery.taghandler.css">
    <link rel="stylesheet" href="/assets/css/editor.css">
    <link rel="stylesheet" href="/assets/css/daterangepicker.css">
@endsection

@inject('dynamicformdetails', 'App\Http\Controllers\EventController')

@section('content')
        <h1 class="page-header">{{ trans('messages.editEventHeading') }}</h1>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form id="addEventFrm" action="{{ url('/event/update/'.$event->id) }}" method="post">
            {{ csrf_field() }}
            <div class="row">
                <div class="form-inline col-md-6">
                    <label class="control-label">{{ trans('messages.editEventCustomerLabel') }} : </label>
                    <p class="form-control-static">{{ $customer_name }}</p>
                </div>
                <div class="form-inline col-md-6">
                    <label class="control-label">{{ trans('messages.editEventCarLabel') }} : </label>
                    <p class="form-control-static">{{ $car_name }}</p>
                </div>
                <br /><br />
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventTitleLabel') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ $event->title }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="payment">{{ trans('messages.editEventPaymentLabel') }}</label><br />
                    <label class="radio-inline"><input type="radio" name="payment" value="cash" @if($event->payment=='cash') checked="checked" @endif >{{ trans('messages.editEventPaymentLabelCash') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="bank" @if($event->payment=='bank') checked="checked" @endif >{{ trans('messages.editEventPaymentLabelBank') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="creditcard" @if($event->payment=='creditcard') checked="checked" @endif >{{ trans('messages.editEventPaymentLabelCreditcard') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="paypal" @if($event->payment=='paypal') checked="checked" @endif >{{ trans('messages.editEventPaymentLabelPaypal') }}</label>
                    <label class="radio-inline"><input type="radio" name="payment" value="invoice" @if($event->payment=='invoice') checked="checked" @endif >{{ trans('messages.editEventPaymentLabelInvoice') }}</label>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventFreetextExternalLabel') }}</label>
                    <textarea id="txtEditor_x" name="freetext_external">{{ $event->freetext_external }}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventFreetextInternalLabel') }}</label>
                    <textarea id="txtEditor_i" name="freetext_internal">{{ $event->freetext_internal }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventStageLabel') }}</label>
                    <select class="form-control" name="stage">
                        @for($i=1;$i<=5;$i++)
                            <option @if($i == $event->stage || $i == old('stage')) selected="selected" @endif value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventMileageLabel') }}</label>
                    <input type="text" class="form-control mileageNumber" name="mileage" value="{{ $event->mileage }}">
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-3">
                    <label for="tuning">{{ trans('messages.editEventTuningLabel') }}</label><br />
                    <label class="radio-inline"><input type="radio" name="tuning" value="yes" @if($event->tuning=='yes') checked="checked" @endif >{{ trans('messages.editEventTuningLabelYes') }}</label>
                    <label class="radio-inline"><input type="radio" name="tuning" value="no" @if($event->tuning=='no') checked="checked" @endif >{{ trans('messages.editEventTuningLabelNo') }}</label>
                </div>
                <div class="form-group col-md-3">
                    <label for="dyno" >{{ trans('messages.editEventDynoLabel') }}</label><br />
                    <label class="radio-inline"><input type="radio" name="dyno" value="yes" @if($event->dyno=='yes') checked="checked" @endif >{{ trans('messages.editEventDynoLabelYes') }}</label>
                    <label class="radio-inline"><input type="radio" name="dyno" value="no" @if($event->dyno=='no') checked="checked" @endif >{{ trans('messages.editEventDynoLabelNo') }}</label>
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventDateLabel') }}</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="eventrange" name="eventrange" value="{{ old('eventrange') }}" readonly aria-describedby="cal-addon">
                        <span class="input-group-addon" id="cal-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventPriceLabel') }}</label>
                    <input type="text" class="form-control" name="price" value="{{ $event->price }}">
                </div>
                <div class="form-group col-md-6">
                    <label for="name">{{ trans('messages.editEventHardwareLabel') }}</label>
                    <ul class="tag-handler form-control" style="margin: 0">
                    </ul>
                    <input type="hidden" id="hardwares" name="hardwares" value="{{ implode(",", json_decode($assignedTags)) }}">
                </div>
            </div>
            @foreach($dynamicformdetails->showFormGroup() as $formGroup)
                <h4 class="page-header">{{ $formGroup->title }}</h4>
                @foreach($dynamicformdetails->showFormFieldsEdit($formGroup->id, $event->id) as $formField)
                    @if ($formField->type == 'input')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="{{ $formField->title }}">{{ $formField->title }}</label>
                                <input type="text" class="form-control" name="dynField_{{$formField->id}}" id="fieldInput" placeholder="{{ $formField->placeholder }}" value="{{ $formField->value }}" maxlength="100">
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                                <input type="hidden" value="{{ $formField->formvalueid }}" name="formValueID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'textarea')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="{{ $formField->title }}">{{ $formField->title }}</label>
                                <textarea class="form-control" name="dynField_{{$formField->id}}" rows="3" id="fieldTextarea" placeholder="{{ $formField->placeholder }}" maxlength="255">{{ $formField->value }}</textarea>
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                                <input type="hidden" value="{{ $formField->formvalueid }}" name="formValueID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'checkbox')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <input type="checkbox" name="dynField_{{$formField->id}}" id="fieldCheckbox{{$formField->id}}" placeholder="{{ $formField->placeholder }}" @if ($formField->value!=null) value="{{ $formField->value }}" @endif @if ($formField->value) {{ "checked" }} @endif >
                                <label for="fieldCheckbox{{$formField->id}}">{{ $formField->title }}</label>
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                                <input type="hidden" value="{{ $formField->formvalueid }}" name="formValueID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'select')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="Lbl{{ $formField->id }}">{{ $formField->title }}</label>
                                <select name="dynField_{{$formField->id}}" id="Lbl{{ $formField->id }}" class="form-control">
                                    @forelse(explode("|",$formField->options) as $options)
                                        <option value="{{ explode(":",$options)[0] }}" @if ($formField->value==explode(":",$options)[0]) {{ "selected" }} @endif >{{ explode(":",$options)[1] }}</option>
                                    @empty
                                        <option value="">No options available</option>
                                    @endforelse
                                </select>
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                                <input type="hidden" value="{{ $formField->formvalueid }}" name="formValueID[]">
                            </div>
                        </div>
                    @endif
                    @if ($formField->type == 'radio')
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>{{ $formField->title }}</label>
                                @foreach(explode("|",$formField->options) as $options)
                                    <input type="radio" name="dynField_{{$formField->id}}" value="{{ explode(":",$options)[0] }}" @if ($formField->value==explode(":",$options)[0]) {{ "checked" }} @endif id="dynField_{{explode(":",$options)[1]}}">
                                    <label for="dynField_{{explode(":",$options)[1]}}">{{ explode(":",$options)[1] }}</label>
                                @endforeach
                                <input type="hidden" value="{{ $formField->id }}" name="fieldID[]">
                                <input type="hidden" value="{{ $formField->formvalueid }}" name="formValueID[]">
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
            <input type="hidden" name="vehicle_id" value="{{ $event->vehicle_id }}">
            <div class="form-group">
                <button type="button" id="btnCreate" class="btn btn-primary btn-lg btn-block">{{ trans('messages.editEventUpdateButton') }}</button>
            </div>
        </form>
@endsection

@push('script')
    <script src="/assets/js/jquery.taghandler.js"></script>
    <script src="/assets/js/editor.js"></script>
    <script src="/assets/js/moment.min.js"></script>
    <script src="/assets/js/daterangepicker.js"></script>
    <script>
        //WYSIWYG Editor
        $(document).ready(function() {
            $("#txtEditor_x").Editor({
                'l_align':false, 'r_align':false, 'c_align':false,
                'justify':false, 'insert_link':false, 'unlink':false,
                'insert_img':false, 'hr_line':false, 'block_quote':false,
                'source':false, 'strikeout':true, 'indent':false,
                'outdent':false, 'fonts':false, 'styles':false,
                'print':false, 'rm_format':false, 'status_bar':false,
                'font_size':false, 'color':false, 'splchars':false,
                'insert_table':false, 'select_all':false, 'togglescreen':false
            });

            $("#txtEditor_x").Editor("setText", $("#txtEditor_x").text());

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
            $("#txtEditor_x").html($("#txtEditor_x").Editor("getText"));
            $("#txtEditor_i").html($("#txtEditor_i").Editor("getText"));
            $("#addEventFrm").submit();
        });

        //Date picker
        $('#eventrange').daterangepicker({
                    timePicker:true,
                    timePickerIncrement:30,
                    timePicker24Hour: true,
                    drops: 'up',
                    startDate: '{{  date('d-m-Y H:i', strtotime($event->begin_at)) }}',
                    endDate: '{{  date('d-m-Y H:i', strtotime($event->end_at)) }}',
            locale: {
                "format": "DD-MM-YYYY H:mm",
                "separator": " To ",
            },
        });

        /* Tag handler */
        var vehicle = {{ $event->vehicle_id }}

        $(".tag-handler").tagHandler({
            getData: {vehicleid: vehicle},
            getURL: '/tag/hardware',
            autocomplete: true,
            autoUpdate: true,
            minChars: 2,
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
    </script>
@endpush
