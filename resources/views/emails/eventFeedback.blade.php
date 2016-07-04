Hello @if($title) {{ $title }} @endif {{ title_case($event->firstname) }} {{ title_case($event->lastname) }}
<br><br>
We would like to know your feedback on the following event Held on  {{ date('d.m.Y H:i', strtotime($event->begin_at)) }} <br /><br />
Event details: <br /><br />
{{ title_case($event->title) }}
<br><br>
Vehicle : {{ $vehicle }} <br><br>
License plate: {{ $event->license_plate }} <br><br>

Please <a href="https://www.turboperformance.de/kundenmeinung">Click here</a> to give your feedback.

