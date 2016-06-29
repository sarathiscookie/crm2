@extends('layouts.app')

@section('title', 'Events')

@section('style')

@endsection

<link href="/assets/css/jquery.scrollbar.css" rel="stylesheet" type="text/css" media="screen" />
<link class="main-stylesheet" href="/assets/css/calendar.css" rel="stylesheet" type="text/css" />
<!--[if lte IE 9]>
<link href="/assets/css/dialog.ie.css" rel="stylesheet" type="text/css" media="screen" />
<![endif]-->

@section('contents')
    <!-- START PAGE-CONTAINER -->
    <div class="page-container bg-white full-height">
        <!-- START PAGE CONTENT WRAPPER -->
        <div class="page-content-wrapper full-height">
            <!-- START PAGE CONTENT -->
            <div class="content full-height">
                <!-- START CALENDAR -->
                <div id="myCalendar" class="full-height"></div>
                <!-- END CALENDAR -->
                <!-- START Calendar Events Form -->
                <div class="quickview-wrapper calendar-event" id="calendar-event">
                    <div class="view-port clearfix" id="eventFormController">
                        <div class="view bg-white">
                            <div class="scrollable">
                                <div class="p-l-30 p-r-30 p-t-20">
                                    <a class="pg-close text-master link pull-right" data-toggle="quickview" data-toggle-element="#calendar-event" href="#"></a>
                                    <h4 id="event-date">&amp;</h4>
                                    <div class="m-b-20">
                                        <i class="fa fa-clock-o"></i>
                                        <span id="lblfromTime"></span> to
                                        <span id="lbltoTime"></span>
                                    </div>
                                </div>
                                <div class="p-t-15">
                                    <input id="eventIndex" name="eventIndex" type="hidden">
                                    <div class="form-group-attached">
                                        <div class="form-group form-group-default ">
                                            <label>Title</label>
                                            <input type="text" class="form-control" id="txtEventName" name="" placeholder="event name">
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-sm-9">
                                                <div class="form-group form-group-default">
                                                    <label>Location</label>
                                                    <input type="text" class="form-control" id="txtEventLocation" placeholder="name of place" name="">
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group form-group-default">
                                                    <label>Code</label>
                                                    <input type="text" class="form-control" id="txtEventCode" name="lastName">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="form-group form-group-default">
                                                <label>Note</label>
                                                <textarea class="form-control" placeholder="description" id="txtEventDesc"></textarea>
                                            </div>
                                        </div>
                                        <div class="row clearfix cursor">
                                            <div class="form-group form-group-default" data-navigate="view" data-view-port="#eventFormController" data-view-animation="push-parrallax">
                                                <label>Alerts</label>
                                                <div class="p-t-10">
                                                    <span class="pull-right p-r-10 p-b-5"><i class="pg-arrow_right"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-l-30 p-r-30 p-t-30">
                                    <button id="eventSave" class="btn btn-warning btn-cons">Save Event</button>
                                    <button id="eventDelete" class="btn btn-white"><i class="fa fa-trash-o"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="view bg-white">
                            <div class="navbar navbar-default navbar-sm">
                                <div class="navbar-inner">
                                    <a href="javascript:;" class="inline action p-l-10 link text-master" data-navigate="view" data-view-port="#eventFormController" data-view-animation="push-parrallax">
                                        <i class="pg-arrow_left"></i>
                                    </a>
                                    <div class="view-heading">
                                        <span class="font-montserrat text-uppercase fs-13">Alerts</span>
                                    </div>
                                    <a href="#" class="inline action p-r-10 pull-right link text-master">
                                        <i class="pg-search"></i>
                                    </a>
                                </div>
                            </div>
                            <p class="p-l-30 p-r-30 p-t-30"> This is a Demo</p>
                        </div>
                    </div>
                </div>
                <!-- END Calendar Events Form -->
            </div>
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTENT WRAPPER -->
    </div>
@endsection

@push('script')
<script src="/assets/js/modernizr.custom.js" type="text/javascript"></script>
<script src="/assets/js/jquery.scrollbar.min.js"></script>
<script src="/assets/js/interact.min.js" type="text/javascript"></script>
<script src="/assets/js/moment-with-locales.min.js"></script>
<script src="/assets/js/pages.calendar.js"></script>
<script>
    (function($) {

        'use strict';

        $(document).ready(function() {
            var selectedEvent;
            $.get("/events/list", function(data){
                $('#myCalendar').pagescalendar({
                    //Loading Dummy EVENTS for demo Purposes, you can feed the events attribute from
                    //Web Service
                    events: data,
                    view:"week",
                    slotDuration: '15',
                    onViewRenderComplete: function() {
                        //You can Do a Simple AJAX here and update
                    },
                    /*onEventClick: function(event) {
                        //Open Pages Custom Quick View
                        if (!$('#calendar-event').hasClass('open'))
                            $('#calendar-event').addClass('open');


                        selectedEvent = event;
                        setEventDetailsToForm(selectedEvent);
                    },*/
                    onEventDragComplete: function(event) {
                        selectedEvent = event;
                        setEventDetailsToForm(selectedEvent);

                    },
                    onEventResizeComplete: function(event) {
                        selectedEvent = event;
                        setEventDetailsToForm(selectedEvent);
                    },
                    /*onTimeSlotDblClick: function(timeSlot) {
                        $('#calendar-event').removeClass('open');
                        //Adding a new Event on Slot Double Click
                        var newEvent = {
                            title: 'my new event',
                            class: 'bg-success-lighter',
                            start: timeSlot.date,
                            end: moment(timeSlot.date).add(1, 'hour').format(),
                            allDay: false,
                            other: {
                                //You can have your custom list of attributes here
                                note: 'test'
                            }
                        };
                        selectedEvent = newEvent;
                        $('#myCalendar').pagescalendar('addEvent', newEvent);
                        setEventDetailsToForm(selectedEvent);
                    }*/
                });
                // Some Other Public Methods That can be Use are below \
                //console.log($('body').pagescalendar('getEvents'))
                //get the value of a property
                //console.log($('body').pagescalendar('getDate','MMMM'));

                function setEventDetailsToForm(event) {
                    $('#eventIndex').val();
                    $('#txtEventName').val();
                    $('#txtEventCode').val();
                    $('#txtEventLocation').val();
                    //Show Event date
                    $('#event-date').html(moment(event.start).format('MMM, D dddd'));

                    $('#lblfromTime').html(moment(event.start).format('h:mm A'));
                    $('#lbltoTime').html(moment(event.end).format('H:mm A'));

                    //Load Event Data To Text Field
                    $('#eventIndex').val(event.index);
                    $('#txtEventName').val(event.title);
                    $('#txtEventCode').val(event.other.code);
                    $('#txtEventLocation').val(event.other.location);
                }

                $('#eventSave').on('click', function() {
                    selectedEvent.title = $('#txtEventName').val();

                    //You can add Any thing inside "other" object and it will get save inside the plugin.
                    //Refer it back using the same name other.your_custom_attribute

                    selectedEvent.other.code = $('#txtEventCode').val();
                    selectedEvent.other.location = $('#txtEventLocation').val();

                    $('#myCalendar').pagescalendar('updateEvent',selectedEvent);

                    $('#calendar-event').removeClass('open');
                });

                $('#eventDelete').on('click', function() {
                    $('#myCalendar').pagescalendar('removeEvent', $('#eventIndex').val());
                    $('#calendar-event').removeClass('open');
                });
            });
        });

    })(window.jQuery);
</script>
@endpush


