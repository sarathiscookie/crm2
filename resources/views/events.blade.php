@extends('layouts.app')

@section('title', 'Events')

@section('style')
    <style>
        .page-container .page-content-wrapper .content {
            padding: 0;
        }

        .page-container {
            padding: 0;
        }

        .calendar .calendar-header > .drager {
            border: none;
        }
    </style>
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
                <!-- Modal begins -->
                <div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="eventModalLabel">Event Details</h4>
                            </div>
                            <div class="modal-body">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal end -->
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
                    ui: {
                        year: {
                            visible: true,
                            format: 'YYYY',
                            startYear: '2014',
                            endYear: moment().add(1, 'year').format('YYYY'),
                            eventBubble: true
                        },
                        //Month Selector
                        month: {
                            visible: true,
                            format: 'MMMM',
                            eventBubble: true
                        },
                        dateHeader: {
                            format: 'dddd, D MMMM YYYY',
                            visible: true,
                        },
                        week: {
                            day: {
                                format: 'D'
                            },
                            header: {
                                format: 'dd'
                            },
                            eventBubble: true,
                            startOfTheWeek: '0',
                            endOfTheWeek:'6'
                        },
                        grid: {
                            dateFormat: 'D dddd',
                            timeFormat: 'hh:mm',
                            eventBubble: false,
                        }
                    },
                    locale: 'de',
                    timeFormat: 'hh:mm',
                    minTime:8,
                    maxTime:19,
                    dateFormat: 'MMMM Do YYYY',
                    slotDuration: '15', //In Mins : supports 15, 30 and 60
                    onViewRenderComplete: function() {
                        //You can Do a Simple AJAX here and update
                    },
                    onEventClick: function(event) {
                        var eventId = event.other.eventId;
                        $.get('/events/list/'+eventId, function(response){
                            $('#eventModal').modal('toggle');
                            $(".modal-body").html(response)
                        });
                    },
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


