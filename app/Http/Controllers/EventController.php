<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Event;
use App\Hardware;
use App\Vehicle;
use App\Vehiclehardware;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\EventRequest;
use DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use HTML2PDF;
use HTML2PDF_exception;

class EventController extends Controller
{
    /**
     * view dashboard
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('events');
    }

    /**
     * view dashboard
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function view()
    {
        $result = "";
        $listEvents    = Event::select('title', 'begin_at', 'end_at')
            ->orderBy('id', 'desc')
            ->get();
        foreach ($listEvents as $listEvent){
            $dt = Carbon::createFromFormat('Y-m-d H:i:s', $listEvent->begin_at); //2016-06-28 05:00:00
            $dt->addHours(2);
            $resultSet = array (
                'title' => $listEvent->title,
                'start' => $listEvent->begin_at,
                'end' => $dt->format('Y-m-d H:i:s'),
                'class' => "bg-complete-lighter",
                "other" => array()
            );
            $result[] = $resultSet;
        }
        return $result;
    }


    /**
     * Create an event - show form
     * @param $customer_id
     * @param $car_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($customer_id, $car_id)
    {
        $customer = Customer::select('id', 'firstname', 'lastname')->find($customer_id);
        $car = Vehicle::select('id', 'execution_id')->find($car_id);
        
        $customer_name = title_case($customer->firstname). ' ' .title_case($customer->lastname);

        $vehicle_informations = DB::connection('fes')
            ->select("SELECT av.id, av.tuning_id, av.tpbezeichnung, av.marke_name, av.modell_name, av.marke_alias, av.modell_alias, av.kraftstoff, av.vehicletype_title, CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int) as dimsport_kw, CAST(SUBSTRING(substring(tpleistung from (position('/' in tpleistung)+1)), 'm*([0-9]{1,})') as int) as dimsport_ps, round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) as ps_from_dimsport_kw,
										(select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) as motor_id,
										(select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) as motor_power,
										(SELECT CASE WHEN (select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) <> NULL THEN (select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) ELSE round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) END ) as sort_leistung
									FROM mainpage.ausfuehrung_view_neu av
									WHERE av.id = '$car->execution_id'");

        foreach($vehicle_informations as $vehicle_information) {
            if ($vehicle_information->motor_power)
                $power = $vehicle_information->motor_power;
            else
                $power = $vehicle_information->ps_from_dimsport_kw;
        }
        $car_name = $vehicle_information->marke_name. " " .$vehicle_information->modell_name. " ". $vehicle_information->tpbezeichnung. " " . "mit " . $power."PS";

        $assignedTags   = array();
        $tags = Hardware::select('hardwares.id','title')
            ->join('vehicle_hardwares AS VH', 'VH.hardware_id', '=', 'hardwares.id')
            ->where('VH.vehicle_id', $car_id)
            ->get();
        foreach ($tags as $tag){                       
            $assignedTags[] = $tag->title;
        }


        return view('createEvent', ['customer_name' =>$customer_name, 'car_name' => $car_name, 'customer_id' =>$customer_id, 'vehicle_id' => $car_id, 'assignedTags' =>json_encode($assignedTags)]);
    }

    /**
     * Save event
     * @param EventRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(EventRequest $request)
    {
        $customerObj = new CustomerController();
        $date_split = explode(" To ",$request->eventrange);
        $begin_at   = date('Y-m-d H:i', strtotime($date_split[0]));
        $end_at = date('Y-m-d H:i', strtotime($date_split[1]));

        $event = new Event();
        $event->customer_id = $request->customer_id;
        $event->vehicle_id = $request->vehicle_id;
        $event->partner_id = 1149;
        $event->title = $request->title;
        $event->freetext_external = $request->freetext_external;
        $event->freetext_internal = $request->freetext_internal;
        $event->stage    = $request->stage;
        $event->mileage  = $request->mileage;
        $event->tuning   = $request->tuning;
        $event->dyno     = $request->dyno;
        $event->payment  = $request->payment;
        $event->begin_at = $begin_at;
        $event->end_at   = $end_at;
        $event->price   = $request->price;
        $event->save();
        $this->saveHardwares($request);

        $events   = $customerObj->getEventData($event->id);
        $vehicles = $customerObj->getVehicleData($request->vehicle_id);


        $eventHtml = view('emails.newEvent', [ 'customer' => Customer::find($request->customer_id), 'events' => $events, 'vehicles' => $vehicles])->render();
        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'de', TRUE, 'UTF-8', [10,0,10,0]);
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->setDefaultFont("Helvetica");
            $html2pdf->writeHTML($eventHtml);
            $newEventData = $html2pdf->Output('', true);

            Mail::send('emails.newEventNotification', [], function ($message) use($newEventData) {
                $message->to(env('NOTIFY_MAIL', ''))
                    ->subject('New Event created')
                    ->attachData($newEventData, 'newEvent.pdf');
            });
        } catch (HTML2PDF_exception $e) {            
            exit;
        }


        return redirect(url('/customer/details/'.$request->customer_id));
    }

    /**
     * Save hardwares and vehicle hardwares - Tag module
     * @param $request
     */
    protected function saveHardwares($request)
    {
        /* Save data in to hardware table and vehicle_hardwares */
        Vehiclehardware::where('vehicle_id', $request->vehicle_id)->delete();
        $tags = explode(',', $request->hardwares);

        foreach($tags as $key) {

            $hardware_row = Hardware::where('title', $key)
                ->select('id', 'title')
                ->first();

            if(count($hardware_row) > 0){
                if($hardware_row->title == $key){
                    $vehicleHardware              = new Vehiclehardware();
                    $vehicleHardware->vehicle_id  = $request->vehicle_id;
                    $vehicleHardware->hardware_id = $hardware_row->id;
                    $vehicleHardware->save();
                }
            }
            else{
                $hardware          = new Hardware();
                $hardware->user_id = 0;
                $hardware->title   = $key;
                $hardware->status  = 'online';
                $hardware->save();

                $vehicleHardware              = new Vehiclehardware();
                $vehicleHardware->vehicle_id  = $request->vehicle_id;
                $vehicleHardware->hardware_id = $hardware->id;
                $vehicleHardware->save();
            }
        }
    }
}
