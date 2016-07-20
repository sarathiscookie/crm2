@extends('layouts.app')

@section('title',' Create Notice')

@section('style')
    <link rel="stylesheet" href="/assets/css/editor.css">
@endsection

@section('content')
    <h1 class="page-header">{{ trans('messages.noticeCreateFormHeadingLabel') }} <small>[{{ $vehicle }}]</small></h1>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form id="addNoticeFrm" action="{{ url('/notice/save') }}" method="post">
        {{ csrf_field() }}
        <div class="row">
            <div class="form-group col-md-12">
                <label for="name">{{ trans('messages.noticeCreateFormFreetextLabel') }}</label>
                <textarea id="txtEditor" name="freetext">{{ old('freetext') }}</textarea>
            </div>
        </div>
        <input type="hidden" name="vehicle_id" value="{{ $vehicle_id }}">
        <div class="form-group">
            <button type="button" id="btnCreate" class="btn btn-primary btn-lg btn-block">{{ trans('messages.noticeCreateSubmitButton') }}</button>
        </div>
    </form>
@endsection

@push('script')
<script src="/assets/js/editor.js"></script>
<script>
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
    });

    //Submit Form- button action
    $('#btnCreate').click( function () {
        $("#txtEditor").html($("#txtEditor").Editor("getText"));
        $("#addNoticeFrm").submit();
    });
</script>
@endpush