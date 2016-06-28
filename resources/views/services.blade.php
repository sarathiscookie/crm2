@extends('layouts.app')

@section('title', 'Services')

@section('style')
@endsection

@section('content')
    <div class="row">
        <h1 class="page-header">Services</h1>
        <form class="form-inline">
            <div id="form-errors"></div>
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
                <button type="button" class="btn btn-default btn-primary" id="createServices">Create Service</button>
                <div id="loadings" class="pull-right" style="position:relative; top:5px;"></div>
            </div>
            <br />
            <br />
            <div id="update-form-errors"></div>
            <br>
            <table class="table table-hover table-bordered">
                <thead>
                <tr>
                    <th>Title</th>
                    <th>Hours</th>
                    <th>Rate</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($listServices))
                    @forelse ($listServices as $listService)
                        <tr>
                            <td id="{{$listService->id}}" class="edit_td">
                                <span data-toggle="tooltip" data-placement="top" title="Click here for edit title {{$listService->title}}" id="title_{{$listService->id}}" class="text" >{{$listService->title}}</span>
                                <input type="text" value="{{$listService->title}}" id="title_input_{{$listService->id}}" class="form-control editbox" maxlength="100">
                            </td>

                            <td id="{{$listService->id}}" class="edit_td">
                                <span data-toggle="tooltip" data-placement="top" title="Click here for edit hour {{$listService->hours}}" id="hours_{{$listService->id}}" class="text" >{{$listService->hours}}</span>
                                <input type="text" value="{{$listService->hours}}" id="hours_input_{{$listService->id}}" class="form-control editbox" maxlength="100">
                            </td>
                            <td id="{{$listService->id}}" class="edit_td">
                                <span data-toggle="tooltip" data-placement="top" title="Click here for edit rate {{$listService->rate}}" id="rate_{{$listService->id}}" class="text" >{{$listService->rate}}</span>
                                <input type="text" value="{{$listService->rate}}" id="rate_input_{{$listService->id}}" class="form-control editbox" maxlength="100">
                            </td>
                        </tr>
                    @empty
                        <p>No services</p>
                    @endforelse
                @endif
                </tbody>
            </table>
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

        /* Tooltip */
        $('[data-toggle="tooltip"]').tooltip();

        /*Store service*/
        $("#createServices").on("click", function(){
            $('#loadings').html('<img src="/assets/img/loading.gif" alt="loading" class="media-middle loadingIcn" width="24px">').fadeIn('slow');
            var title = $("#title").val();
            var hours = $("#hours").val();
            var rate  = $("#rate").val();
            $.post("/services/save", {title: title, hours: hours, rate: rate}, function(response){
                $('#loadings').fadeOut('slow');
                location.reload();
                $("#title").val("");
                $("#hours").val("");
                $("#rate").val("");
            }).fail(function(response) {
                $('#loadings').fadeOut('slow');
                var errors = response.responseJSON;
                errorsHtml = '<div class="alert alert-danger"><ul>';

                $.each( errors, function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>'; //showing only the first error.
                });
                errorsHtml += '</ul></di>';
                $( '#form-errors' ).html( errorsHtml );
            });
        });

        /*Edit services*/
        $(".editbox").hide();
        $(".text").show();
        $(".edit_td").click(function()
        {
            var ID=$(this).attr('id');
            $("#title_"+ID).hide();
            $("#hours_"+ID).hide();
            $("#rate_"+ID).hide();
            $("#title_input_"+ID).show();
            $("#hours_input_"+ID).show();
            $("#rate_input_"+ID).show();
        }).change(function()
        {
            var ID             = $(this).attr('id');
            var title_data     = $("#title_input_"+ID).val();
            var hour_data      = $("#hours_input_"+ID).val();
            var rate_data      = $("#rate_input_"+ID).val();
            $.post("/services/"+ID, {title: title_data, hours: hour_data, rate: rate_data}, function(data){
                $("#title_"+ID).html(title_data);
                $("#hours_"+ID).html(hour_data);
                $("#rate_"+ID).html(rate_data);
                $("#update-form-errors").hide();
            }).fail(function(response) {
                var errors = response.responseJSON;
                errorsHtml = '<div class="alert alert-danger"><ul>';

                $.each( errors, function( key, value ) {
                    errorsHtml += '<li>' + value[0] + '</li>';
                });
                errorsHtml += '</ul></di>';
                $("#update-form-errors").html(errorsHtml);
            });
        });
        // Edit input box click action
        $(".editbox").mouseup(function()
        {
            return false;
        });
        // Outside click action
        $(document).mouseup(function()
        {
            $(".editbox").hide();
            $(".text").show();
        });

    });
</script>
@endpush


