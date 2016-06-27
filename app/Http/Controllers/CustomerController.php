<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Hardware;
use App\Vehiclehardware;
use App\Customervehicle;
use App\Event;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use Mail;
use DB;

class CustomerController extends Controller
{
    /**
     * view dashboard
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $listCustomers    = Customer::select('id', 'erp_id', 'firstname', 'lastname', 'email', 'phone',  DB::raw("DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') AS created_on"))
            ->orderBy('id', 'desc')
            ->get();
        return view('dashboard', compact('listCustomers'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('createCustomer');
    }

    /**
     * @param CustomerRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(CustomerRequest $request)
    {

        $route         = $request->route;
        $street_number = $request->street_number;
        $street ='';
        if($route!='')
            $street  = $route;
        if($street !='' && $street_number!='')
            $street .= ' '.$street_number;
        elseif ($street_number!='')
            $street = $street_number;

        $customer = new Customer();
        $customer->erp_id = rand(1,999);
        $customer->company = $request->company;
        $customer->firstname = $request->firstname;
        $customer->lastname = $request->lastname;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->street = $street;
        $customer->postal = $request->postal;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->country = $request->country;
        $customer->save();
        $customer_id = $customer->id;

        Mail::send('emails.newCustomerNotification', ['firstname' => $customer->firstname, 'lastname' => $customer->lastname, 'email' => $customer->email, 'phone' => $customer->phone, 'created_at' => $customer->created_at ], function ($message) use ($customer) {
            $message->to(env('NOTIFY_MAIL', ''))->subject('New customer created');
        });

        $vehicle_id = $this->saveVehicle($request);
        $this->saveEvent($customer_id, $vehicle_id, $request);
        $this->saveCustomerVehicle($customer_id, $vehicle_id);

        /* Save data in to hardware table and vehicle_hardwares */
        $tags = explode(',', $request->hardwares);

        foreach($tags as $key) {

            $selectHardwareTitle = Hardware::where('title', $key)
                ->select('id', 'title')
                ->first();

            if(count($selectHardwareTitle) > 0){
                if($selectHardwareTitle->title == $key){
                    $vehicleHardware              = new Vehiclehardware();
                    $vehicleHardware->vehicle_id  = $vehicle_id;
                    $vehicleHardware->hardware_id = $selectHardwareTitle->id;
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
                $vehicleHardware->vehicle_id  = $vehicle_id;
                $vehicleHardware->hardware_id = $hardware->id;
                $vehicleHardware->save();
            }
        }

        return redirect(url('/'))/*->with('status','Created successfully')*/;
    }


    /**
     * save Vehicle
     * @param $request
     * @return mixed
     */
    protected function saveVehicle($request)
    {
        $vehicle = new Vehicle();
        $vehicle->chassis_number = $request->chassis;
        $vehicle->license_plate = $request->license;
        $vehicle->execution_id = $request->vehicle;
        $vehicle->save();
        return $vehicle->id;
    }

    /**
     * Save an event based on customer_id and vehicle
     * @param $customer_id
     * @param $vehicle_id
     * @param $request
     */
    protected function saveEvent($customer_id, $vehicle_id, $request)
    {
        if($customer_id>0 && $vehicle_id>0) {
            $event = new Event();
            $event->customer_id = $customer_id;
            $event->vehicle_id  = $vehicle_id;
            $event->partner_id  = 1149;
            $event->title       = 'Erst-Termin';
            $event->freetext_external = $request->freetext;
            $event->stage    = $request->stage;
            $event->mileage  = $request->mileage;
            $event->tuning   = $request->tuning;
            $event->dyno     = $request->dyno;
            $event->payment  = $request->payment;
            $event->begin_at = Carbon::now();
            $event->save();
        }
    }

    /**
     * Save to customer_vehicle
     * @param $customer_id
     * @param $vehicle_id
     */
    protected function saveCustomerVehicle($customer_id, $vehicle_id)
    {
        $cust_vehicle = new Customervehicle();
        $cust_vehicle->customer_id =$customer_id;
        $cust_vehicle->vehicle_id =$vehicle_id;
        $cust_vehicle->save();
    }


    public function showDetails($id)
    {
        $customer = Customer::find($id);
        $events   = $this->getCustomerEvents($id);
        $vehicles     = $this->getCustomerVehicles($id);



        return view('customerDetails', ['customer' => $customer, 'events' => $events, 'vehicles'=>$vehicles]);
    }


    protected function getCustomerEvents($customer_id)
    {
        $customer_events = Event::where('customer_id', $customer_id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $events ='';
        $i=1;
        foreach($customer_events as $event) {
            if ($i == 1) {
                $collapse = "in";
                $a_class = '';
                $expanded = "true";
            } else {
                $collapse = "";
                $a_class = 'class="collapsed"';
                $expanded = "false";
            }
            $events .= '<div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="heading' . $event->id . '">
                        <h4 class="panel-title">
                            <a ' . $a_class . ' role="button" data-toggle="collapse" data-parent="#accordionEvent" href="#collapse' . $event->id . '" area-expanded="' . $expanded . '" aria-controls="collapse' . $event->id . '" style="outline: none; text-decoration: none">
                                <h4>' . $event->title . '</h4>
                                <p><small>' . date('m.d.Y H:i', strtotime($event->begin_at)) . '</small></p>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse' . $event->id . '" class="panel-collapse collapse ' . $collapse . '" role="tabpanel" aria-labelledby="heading' . $event->id . '">
                        <div class="panel-body">
                             <div>Fahrzeug: '.$event->vehicle_id.'</div>
                             <div>Tuning-Stufe: '.$event->stage.'</div>
                             <div>Kilometerstand: '. number_format($event->mileage, 0, ',', '.')  .' km</div>
                             <div>Bereits getunt: '.$event->tuning.'</div>
                             <div>Prüfstandslauf: '.$event->dyno.'</div>
                             <div>Zahlungsart: '.$event->payment.'</div><br>
                             <strong>Weitere Details:</strong><br>
                            ' . $event->freetext_external . '
                        </div>
                    </div>
                </div>';
            $i++;
        }

        return $events;
    }

    protected function getCustomerVehicles($customer_id)
    {
        $customer_vehicle = Customervehicle::select('vehicles.*')
            ->where('customer_id', $customer_id)
            ->join('vehicles', 'vehicles.id', '=', 'customer_vehicles.vehicle_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        $events ='';
        $i=1;
        foreach($customer_vehicle as $vehicle) {

            $vehicle_informations = DB::connection('fes')
                ->select("SELECT av.id, av.tuning_id, av.tpbezeichnung, av.marke_name, av.modell_name, av.marke_alias, av.modell_alias, av.kraftstoff, av.vehicletype_title, CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int) as dimsport_kw, CAST(SUBSTRING(substring(tpleistung from (position('/' in tpleistung)+1)), 'm*([0-9]{1,})') as int) as dimsport_ps, round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) as ps_from_dimsport_kw,
										(select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) as motor_id,
										(select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) as motor_power,
										(SELECT CASE WHEN (select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) <> NULL THEN (select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) ELSE round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) END ) as sort_leistung
									FROM mainpage.ausfuehrung_view_neu av
									WHERE av.id = '$vehicle->execution_id'");

            foreach($vehicle_informations as $vehicle_information){
                if ($vehicle_information->motor_power)
                    $power = $vehicle_information->motor_power;
                else
                    $power = $vehicle_information->ps_from_dimsport_kw;

                if ($i == 1) {
                    $collapse = "in";
                    $a_class = '';
                    $expanded = "true";
                } else {
                    $collapse = "";
                    $a_class = 'class="collapsed"';
                    $expanded = "false";
                }
                $events .= '<div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingV' . $vehicle->id . '">
                        <h4 class="panel-title">
                            <a ' . $a_class . ' role="button" data-toggle="collapse" data-parent="#accordionVehicle" href="#collapseV' . $vehicle->id . '" area-expanded="' . $expanded . '" aria-controls="collapseV' . $vehicle->id . '" style="outline: none; text-decoration: none">
                                <h4>' . $vehicle_information->marke_name. " " .$vehicle_information->modell_name. " ". $vehicle_information->tpbezeichnung. " " . "mit " . $power."PS" . '</h4>
                            </a>
                        </h4>
                    </div>
                    <div id="collapseV' . $vehicle->id . '" class="panel-collapse collapse ' . $collapse . '" role="tabpanel" aria-labelledby="headingV' . $vehicle->id . '">
                        <div class="panel-body">
                             <div>Kennzeichen: '.$vehicle->license_plate.'</div>
                             <div>Fahrgestellnummer: '.$vehicle->chassis_number.'</div>
                             <br><div><small>Hinzugefügt am ' . date('m.d.Y H:i', strtotime($vehicle->created_at)).'</small></div>
                        </div>
                    </div>
                </div>';
            }
            $i++;
        }

        return $events;
    }

    /**
     * Get to hardware
     */
    public function getHardwareTag()
    {
        $hardwareTagstitles = Hardware::where('status', 'online')
            ->select('title')
            ->get();
        $hardwareTagsresult   = array();
        foreach ($hardwareTagstitles as $hardwareTagstitle)
        {
            $hardwareTagsresult[] = $hardwareTagstitle->title;
        }
        return response()->json(['availableTags' => $hardwareTagsresult, 'assignedTags' => $hardwareTagsresult]);
    }

    /**
     * Search Vehicle
     */
    public function searchVehicle(Request $request)
    {
      $string = '';
      echo '<div class="insearch">';
      if (isset($request->keywords) && strlen(str_replace(" ", "", $request->keywords)) >= 2) {
          $help = str_replace("vw", "volkswagen", $request->keywords);
          $keywords = explode(" ", $help);

          foreach ($keywords as $key => $keyword) {
              $string .= " AND search ILIKE '%" . $keyword . "%'";
          }
        $select_new = DB::connection('fes')
            ->select("SELECT av.id, av.tuning_id, av.tpbezeichnung, av.marke_name, av.modell_name, av.marke_alias, av.modell_alias, av.kraftstoff, av.vehicletype_title, CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int) as dimsport_kw, CAST(SUBSTRING(substring(tpleistung from (position('/' in tpleistung)+1)), 'm*([0-9]{1,})') as int) as dimsport_ps, round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) as ps_from_dimsport_kw,
										(select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) as motor_id,
										(select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) as motor_power,
										(SELECT CASE WHEN (select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) <> NULL THEN (select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) ELSE round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) END ) as sort_leistung
									FROM mainpage.ausfuehrung_view_neu av
									WHERE " . substr($string, 4) . " AND visible = 't' 
									ORDER BY vehicletype_sorting, marke_name, modell_name, kraftstoff, sort_leistung
									LIMIT 20
					 ");
         $numrows = count($select_new);

            if ($numrows == 0)
                echo '<div class="alert alert-danger" style="margin-bottom:0;" role="alert"><strong>Ihr Fahrzeug wurde nicht gefunden!</strong> Bitte prüfen Sie Ihren Suchbegriff oder durchsuchen Sie die Fahrzeug Datenbank <a class="alert-link" href="/chiptuning">hier</a> manuell.</div>';

            if ($numrows != 0) {
                echo '<div class="list-group">';
                $count = 0;
                $tuning_id = "";
                foreach($select_new as $fetch) {
                    // besser lösung suchen
                    if ($tuning_id == $fetch->tuning_id)
                        continue;
                    $tuning_id = $fetch->tuning_id;

                    $text = "<small>" . $fetch->marke_name . " " . $fetch->modell_name . "</small><br>" . $fetch->tpbezeichnung;

                    if ($fetch->motor_power)
                        $power = $fetch->motor_power;
                    else
                        $power = $fetch->ps_from_dimsport_kw;

                    echo '<a class="list-group-item listgroup_'.$fetch->id.'" data-id="'.$fetch->id.'" data-model="'.$fetch->marke_name. " " .$fetch->modell_name. " ".$fetch->tpbezeichnung. " " . "mit " . $power.'PS">' . substr(utf8_encode($text), 0, 55) . '<br><small>mit ' . $power . 'PS</small></a>';
                    $count++;
                }
                echo '</div>';
            }
          echo '<span style="clear: both"></span>';
        }
      echo '</div>';
    }
}
