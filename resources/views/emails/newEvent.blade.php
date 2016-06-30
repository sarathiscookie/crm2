<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Helvetica, sans-serif;

    }
    table {
        width: 100%;
        font-size: 11px;
    }
</style>
<page backtop="10mm" backbottom="10mm" backleft="0mm" backright="0mm">
<table style="width: 100%">
    <tr>
        <td style="width: 50%">
            <table>
                <tr>
                    <td><h2>{{ title_case($customer->firstname) }}  {{ title_case($customer->lastname) }} ( {{ $customer->erp_id }} )</h2></td>
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
                <tr>
                    <td><hr></td>
                </tr>
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
        <td style="vertical-align: top; width: 50%">
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
</page>