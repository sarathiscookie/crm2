@extends('layouts.app')

@section('title', 'Services')

@section('style')
@endsection

@section('content')
    <div class="row">
        <h1 class="page-header">Services</h1>
        <form class="form-inline">
            <div id="form-errors"></div>
            <div class="row">
                <div class="form-group">
                    <label class="sr-only" for="title">Title</label>
                    <input type="text" class="form-control" id="title" placeholder="Title">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="hours">Hours</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="hours" placeholder="Hours">
                        <div class="input-group-addon">h</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="exampleInputPassword3">Rate</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="rate" placeholder="Rate">
                        <div class="input-group-addon">EUR / hour</div>
                    </div>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-default" id="createServices">Create Service</button>
                </div>
            </div>
            <br>
            <div class="row">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Title</th>
                        <th>Title</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Data</td>
                        <td>Data</td>
                        <td>Data</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
@endsection

@push('script')
<script>
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#createServices").on("click", function(e){
            e.preventDefault();
            var title = $("#title").val();
            var hours = $("#hours").val();
            var rate  = $("#rate").val();
            $.post("/services/save", {title: title, hours: hours, rate: rate}, function(response){
                alert(response);
            }).fail(function(response) {
                var errors = response.responseJSON;
                errorsHtml = '<div class="alert alert-danger"><ul>';

                $.each( errors, function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
                });
                errorsHtml += '</ul></di>';
                $( '#form-errors' ).html( errorsHtml );
            });
        });
    });
</script>
@endpush


