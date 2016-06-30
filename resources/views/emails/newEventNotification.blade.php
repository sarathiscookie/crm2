<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<b>A new Event has been created.</b><br><br>
<table>
    <tr>
        <td>
            <table>
                <tr>
                    <td><h2>{{ title_case($customer->firstname) }}  {{ title_case($customer->lastname) }}</h2></td>
                </tr>
                @if($customer->company)
                <tr>
                    <td>{{ $customer->company }}</td><br />
                </tr>
                @endif
                <tr>
                    <td>
                        <address>
                            @if($customer->street){{ $customer->street }}@endif<br>
                            @if($customer->postal){{ $customer->postal }}@endif {{ $customer->city }}<br>
                            {{ $customer->country }}
                        </address><br>
                    </td>
                </tr>
                <tr>
                    <td><label>E-Mail: <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a></label><br></td>
                </tr>
                <tr>
                    <td><label>Telefon: {{ $customer->phone }} </label></td>
                </tr>
                <hr>
                <tr>

                    <td>
                        <table>
                            <tr>
                                <td>
                                    <h3>Termine</h3>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {!! $events !!}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td style="vertical-align: top">
            <table>
                <tr>
                    <td>
                        <h2>Fahrzeuge</h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        {!! $vehicles !!}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>