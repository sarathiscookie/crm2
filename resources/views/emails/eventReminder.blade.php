Hello @if($title) {{ $title }} @endif {{ title_case($event->firstname) }} {{ title_case($event->lastname) }}
<br><br>
This is to remind you about the following event  <br /><br />
Event details: <br /><br />
{{ title_case($event->title) }}  Begin at: {{ date('d.m.Y H:i', strtotime($event->begin_at)) }}
<br><br>
Vehicle : {{ $vehicle }} <br><br>
License plate: {{ $event->license_plate }} <br><br>

