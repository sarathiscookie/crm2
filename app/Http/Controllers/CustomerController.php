<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Hardware;
use App\Vehiclehardware;
use App\Customervehicle;
use App\Event;
use App\Vehicle;
use App\Formgroup;
use App\Formfield;
use App\Formvalue;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use Mail;
use DB;
use Storage;

use HTML2PDF;
use HTML2PDF_exception;

use SoapClient;
use Exception;


class CustomerController extends Controller
{
    public $gearbox = array();
    public $customerTitle = array();

    /**
     * CustomerController constructor.
     */
    public function __construct()
    {
        $this->gearbox = [
            1 => trans("messages.customerCreateFormGearboxManualLabel"),
            2 => trans("messages.customerCreateFormGearboxAutomaticLabel")
        ];
        $this->customerTitle =[
            1 => trans("messages.customerCreateFormTitleLabelMr"),
            2 => trans("messages.customerCreateFormTitleLabelMrs"),
            3 => trans("messages.customerCreateFormTitleLabelCompany")
        ];
    }

    /**
     * Listing gearbox types
     * For select box
     */
    public function gearbox()
    {
        $gearboxeTypes = $this->gearbox;
        return $gearboxeTypes;
    }

    /**
     * Listing customer types
     * For select box
     */

    public function customerStatus()
    {
        $customerStatus = array('customer' => trans("messages.customerCreateFormStatusLabelCustomer"), 'prospect' => trans("messages.customerCreateFormStatusLabelProspect"), 'vip' => trans("messages.customerCreateFormStatusLabelVip"), 'reseller' => trans("messages.customerCreateFormStatusLabelReseller"), 'blocked' => trans("messages.customerCreateFormStatusLabelBlocked"), 'deleted' => trans("messages.customerCreateFormStatusLabelDeleted"));
        return $customerStatus;
    }



    /**
     * view customer list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        /*$listCustomers    = Customer::select('id', 'erp_id', 'firstname', 'lastname', 'email', 'phone_1', 'status', DB::raw("DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') AS created_on"))
            ->orderBy('id', 'desc')
            ->get();
        return view('customers', compact('listCustomers'));*/
        return view('customers');
    }

    /**
     * Create customer - show form
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $begin_at = date('d-m-Y H:i', strtotime(Carbon::now()));
        $end_at   = date('d-m-Y H:i', strtotime(Carbon::now()->addHours(3)));
        return view('createCustomer', ['begin_at' => $begin_at, 'end_at' => $end_at]);
    }

    /**
     * Save customer details
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
        $customer->title = $request->title;
        $customer->firstname = $request->firstname;
        $customer->lastname = $request->lastname;
        $customer->email = $request->email;
        $customer->phone_1 = $request->phone;
        $customer->phone_2 = $request->phone_2;
        $customer->phone_mobile = $request->phone_mobile;
        $customer->additional_address = $request->additional_address;
        $customer->street = $street;
        $customer->postal = $request->postal;
        $customer->city = $request->city;
        $customer->state = $request->state;
        $customer->country_long = $request->country;
        $customer->status = $request->customerstatus;
        $customer->save();
        $customer_id = $customer->id;

        if($request->fieldID != ''){
            if($customer_id > 0){
                // Storing form values begin
                foreach($request->fieldID as $values){
                    $fieldsIdResult[]   = $values;
                }

                $j = 0;
                while($j < count($request->fieldID)) {
                    $IdResult                 = $fieldsIdResult[$j];
                    $ValField                 = 'dynField_'.$IdResult;
                    $formValue                = new Formvalue;
                    $formValue->form_field_id = $IdResult;
                    $formValue->value         = $request->$ValField;
                    $formValue->parent_id     = $customer_id;
                    $formValue->save();
                    $j++;
                }
                // Storing form values end
            }
        }


        $vehicle_id = $this->saveVehicle($request);
        $events     = $this->saveEvent($customer_id, $vehicle_id, $request);
        $vehicles   = $this->saveCustomerVehicle($customer_id, $vehicle_id);

        /* Save data in to hardware table and vehicle_hardwares */
        if($request->hardwares != ""){
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
        }
        /*save customer in actindo warehouse*/
        $this->createCustomerActindo($customer_id);

        $eventHtml = view('emails.newEvent', [ 'customer' => Customer::find($customer_id), 'events' => $events, 'vehicles' => $vehicles])->render();
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

        return redirect(url('/'));
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
        $vehicle->license_plate  = $request->license;
        $vehicle->execution_id   = $request->vehicle;
        $vehicle->gearbox        = $request->gearbox;
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
        $date_split = explode(" To ",$request->eventrange);
        $begin_at   = date('Y-m-d H:i', strtotime($date_split[0]));
        $end_at     = date('Y-m-d H:i', strtotime($date_split[1]));

        if($customer_id>0 && $vehicle_id>0) {
            $event = new Event();
            $event->customer_id = $customer_id;
            $event->vehicle_id  = $vehicle_id;
            $event->partner_id  = 1149;
            $event->title       = 'Erst-Termin';
            $event->freetext_external = $request->freetext;
            $event->freetext_internal = $request->freetext_internal;
            $event->stage    = $request->stage;
            $event->mileage  = str_replace(".", "", $request->mileage);
            $event->tuning   = $request->tuning;
            $event->dyno     = $request->dyno;
            $event->payment  = $request->payment;
            $event->begin_at = $begin_at;
            $event->end_at   = $end_at;
            $event->save();
        }

        return $this->getEventData($event->id);
    }

    /**
     * Save to customer_vehicle
     * @param $customer_id
     * @param $vehicle_id
     * @return string
     */
    protected function saveCustomerVehicle($customer_id, $vehicle_id)
    {
        $cust_vehicle = new Customervehicle();
        $cust_vehicle->customer_id =$customer_id;
        $cust_vehicle->vehicle_id =$vehicle_id;
        $cust_vehicle->save();

        return $this->getVehicleData($vehicle_id);
    }

    /**
     * Show customer Details page
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDetails($id)
    {
        $customer           = Customer::find($id);
        $events             = $this->getCustomerEvents($id);
        $vehicles           = $this->getCustomerVehicles($id);
        $gears              = $this->gearbox;
        $customerFormValues = $this->customerFormDetails($id);

        return view('customerDetails', ['customer' => $customer, 'events' => $events, 'vehicles'=>$vehicles, 'gears' =>$gears, 'customerFormValues' => $customerFormValues]);
    }

    /**
     * get list of customer's events
     * @param $customer_id
     * @return string
     */
    protected function getCustomerEvents($customer_id)
    {
        $customer_events = Event::select('events.id', 'vehicles.execution_id', 'title', 'freetext_external', 'stage', 'mileage', 'tuning', 'dyno', 'payment', 'begin_at', 'price')
            ->join('vehicles', 'vehicles.id', '=', 'events.vehicle_id')
            ->where('customer_id', $customer_id)
            ->orderBy('events.created_at', 'DESC')
            ->get();

        $events = '';
        $i=1;
        foreach($customer_events as $event) {
            $vehicle_title = '';
            $vehicle_informations = DB::connection('fes')
                ->select("SELECT av.id, av.tuning_id, av.tpbezeichnung, av.marke_name, av.modell_name, av.marke_alias, av.modell_alias, av.kraftstoff, av.vehicletype_title, CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int) as dimsport_kw, CAST(SUBSTRING(substring(tpleistung from (position('/' in tpleistung)+1)), 'm*([0-9]{1,})') as int) as dimsport_ps, round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) as ps_from_dimsport_kw,
										(select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) as motor_id,
										(select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) as motor_power,
										(SELECT CASE WHEN (select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) <> NULL THEN (select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) ELSE round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) END ) as sort_leistung
									FROM mainpage.ausfuehrung_view_neu av
									WHERE av.id = '$event->execution_id'");

            foreach ($vehicle_informations as $vehicle_information) {
                if ($vehicle_information->motor_power)
                    $power = $vehicle_information->motor_power;
                else
                    $power = $vehicle_information->ps_from_dimsport_kw;
                $vehicle_title = $vehicle_information->marke_name . " " . $vehicle_information->modell_name . " " . $vehicle_information->tpbezeichnung . " " . "mit " . $power . "PS";
            }
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
                                <h4>' . $event->title . ' ( ' . $event->id . ' )</h4>
                                <p><small>' . date('d.m.Y H:i', strtotime($event->begin_at)) . '</small></p>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse' . $event->id . '" class="panel-collapse collapse ' . $collapse . '" role="tabpanel" aria-labelledby="heading' . $event->id . '">
                        <div class="panel-body">
                             <div>Fahrzeug: ' . $vehicle_title . '</div>
                             <div>Tuning-Stufe: ' . $event->stage . '</div>
                             <div>Kilometerstand: ' . number_format($event->mileage, 0, ',', '.') . ' km</div>
                             <div>Bereits getunt: ' . $event->tuning . '</div>
                             <div>Prüfstandslauf: ' . $event->dyno . '</div>
                             <div>Zahlungsart: ' . $event->payment . '</div><br>
                             <strong>Weitere Details:</strong><br>
                            ' . $event->freetext_external . '
                            <br>
                            <button type="button" class="btn btn-primary get-info" id="' . $event->id . '" data-toggle="modal" data-target="#infoModal">Hidden Info</button>
                        </div>
                    </div>
                </div>';
            $i++;

        }

        return $events;
    }

    /**
     * get list of customer's vehicles
     * @param $customer_id
     * @return string
     */
    protected function getCustomerVehicles($customer_id)
    {
        $customer_vehicle = Customervehicle::select('VC.id', 'VC.execution_id', 'VC.chassis_number', 'VC.license_plate', 'VC.gearbox', 'VC.created_at')
            ->where('customer_id', $customer_id)
            ->join('vehicles AS VC', 'VC.id', '=', 'customer_vehicles.vehicle_id')
            ->orderBy('created_at', 'DESC')
            ->get();

        $vehicleList ='';
        $i=1;
        foreach($customer_vehicle as $vehicle) {

            $documents = $this->getDocuments($vehicle->id);

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
                $vehicleList .= '<div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingV' . $vehicle->id . '">
                        <h3 class="panel-title">
                            <a ' . $a_class . ' role="button" data-toggle="collapse" data-parent="#accordionVehicle" href="#collapseV' . $vehicle->id . '" area-expanded="' . $expanded . '" aria-controls="collapseV' . $vehicle->id . '" style="outline: none; text-decoration: none">
                                ' . $vehicle_information->marke_name. " " .$vehicle_information->modell_name. " ". $vehicle_information->tpbezeichnung. " " . "mit " . $power."PS" . '
                            </a> 
                            <a role="button" class="btn btn-primary pull-right" href="'.url('/event/create/'.$customer_id.'/'.$vehicle->id).'">Add event</a>   
                            <div class="clearfix"></div>
                        </h3>
                        
                    </div>
                    <div id="collapseV' . $vehicle->id . '" class="panel-collapse collapse ' . $collapse . '" role="tabpanel" aria-labelledby="headingV' . $vehicle->id . '">
                        <div class="panel-body">
                             <div>Kennzeichen: '.$vehicle->license_plate.'</div>
                             <div>Fahrgestellnummer: '.$vehicle->chassis_number.'</div>
                             <div>Gearbox: '.$this->gearbox[$vehicle->gearbox].'</div><br>
                             <div><small>Hinzugefügt am ' . date('d.m.Y H:i', strtotime($vehicle->created_at)).'</small></div>
                             <div>
                                 <h4>Documents</h4>
                                 <div class="list-group" id="fileList_' . $vehicle->id . '">'. $documents .'</div>
                                 <div class="row col-md-10">
                                 <form id="uploadFrm_' . $vehicle->id . '" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="vehicleNr" value="' . $vehicle->id . '">
                                    <input type="file" class="upload-input" id="uploadInput_' . $vehicle->id . '" name="vehicle_docs[]" multiple>
                                 </form>
                                 </div>
                                 <img src="/assets/img/loading.gif" class="media-middle file-loader invisible" width="24px" alt="loading" >
                             </div>
                        </div>
                    </div>                    
                </div>';
            }
            $i++;
        }

        return $vehicleList;
    }

    /**
     * Get to hardware
     */
    public function getHardwareTag(Request $request)
    {
        $hardwareTagstitles = Hardware::where('status', 'online')
            ->select('title')
            ->get();
        $hardwareTagsresult   = array();
        foreach ($hardwareTagstitles as $hardwareTagstitle)
        {
            $hardwareTagsresult[] = $hardwareTagstitle->title;
        }

        $assignedTagsresult = array();
        if(isset($request->vehicleid)) {
            $vechicleHardwares = Hardware::select('title')
                ->join('vehicle_hardwares as VH', 'VH.hardware_id', '=', 'hardwares.id')
                ->where('VH.vehicle_id', $request->vehicleid)
                ->get();

            foreach ($vechicleHardwares as $assigned)
            {
                $assignedTagsresult[] = $assigned->title;
            }
        }
        return response()->json(['availableTags' => $hardwareTagsresult, 'assignedTags' => $assignedTagsresult]);
    }

    /**
     * Search Vehicle
     * @param Request $request
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


    /**
     * Get event details to Notification email
     * @param $event_id
     * @return string
     */
    public function getEventData($event_id)
    {
        $event = Event::select('events.id', 'vehicles.execution_id', 'title', 'freetext_external', 'stage', 'mileage', 'tuning', 'dyno', 'payment', 'begin_at', 'price')
            ->join('vehicles', 'vehicles.id', '=', 'events.vehicle_id')
            ->where('events.id', $event_id)
            ->orderBy('events.created_at', 'DESC')
            ->first();

        $vehicle_title ='';
        $vehicle_informations = DB::connection('fes')
            ->select("SELECT av.id, av.tuning_id, av.tpbezeichnung, av.marke_name, av.modell_name, av.marke_alias, av.modell_alias, av.kraftstoff, av.vehicletype_title, CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int) as dimsport_kw, CAST(SUBSTRING(substring(tpleistung from (position('/' in tpleistung)+1)), 'm*([0-9]{1,})') as int) as dimsport_ps, round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) as ps_from_dimsport_kw,
                                    (select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) as motor_id,
                                    (select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) as motor_power,
                                    (SELECT CASE WHEN (select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) <> NULL THEN (select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) ELSE round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) END ) as sort_leistung
                                FROM mainpage.ausfuehrung_view_neu av
                                WHERE av.id = '$event->execution_id'");

        foreach($vehicle_informations as $vehicle_information) {
            if ($vehicle_information->motor_power)
                $power = $vehicle_information->motor_power;
            else
                $power = $vehicle_information->ps_from_dimsport_kw;
            $vehicle_title = $vehicle_information->marke_name. " " .$vehicle_information->modell_name. " ". $vehicle_information->tpbezeichnung. " " . "mit " . $power."PS";
        }
        $eventHtml = '<div class="panel panel-default">
                <div class="panel-heading" role="tab" id="heading' . $event->id . '">
                    <h4 class="panel-title">
                        <a role="button" style="outline: none; text-decoration: none">
                            <h4>' . $event->title . ' ( '.$event->id.' )</h4>
                            <p><small>' . date('d.m.Y H:i', strtotime($event->begin_at)) . '</small></p>
                        </a>
                    </h4>
                </div>
                <div id="collapse' . $event->id . '" class="panel-collapse" role="tabpanel" aria-labelledby="heading' . $event->id . '">
                    <div class="panel-body">
                         <div>Fahrzeug: '.$vehicle_title.'</div>
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

        return $eventHtml;
    }

    /**
     * Get Vehicle details to Notification email
     * @param $vehicle_id
     * @return string
     */
    public function getVehicleData($vehicle_id)
    {
        $vehicle = Customervehicle::select('VC.id', 'VC.execution_id', 'VC.chassis_number', 'VC.license_plate', 'VC.gearbox', 'VC.created_at')
            ->where('VC.id', $vehicle_id)
            ->join('vehicles AS VC', 'VC.id', '=', 'customer_vehicles.vehicle_id')
            ->orderBy('created_at', 'DESC')
            ->first();

        $vehicleList ='';

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

            $vehicleList = '<div class="panel panel-default">
                <div class="panel-heading" role="tab" id="headingV' . $vehicle->id . '">
                    <h3 class="panel-title">
                        <a  role="button" style="outline: none; text-decoration: none">
                            ' . $vehicle_information->marke_name. " " .$vehicle_information->modell_name. " ". $vehicle_information->tpbezeichnung. " " . "mit " . $power."PS" . '
                        </a>                         
                    </h3>                    
                </div>
                <div class="panel-collapse" role="tabpanel" aria-labelledby="headingV' . $vehicle->id . '">
                    <div class="panel-body">
                         <div>Kennzeichen: '.$vehicle->license_plate.'</div>
                         <div>Fahrgestellnummer: '.$vehicle->chassis_number.'</div>
                         <div>Gearbox: '.$this->gearbox[$vehicle->gearbox].'</div>
                         <br><div><small>Hinzugefügt am ' . date('d.m.Y H:i', strtotime($vehicle->created_at)).'</small></div>
                    </div>
                </div>
            </div>';
        }
        return $vehicleList;
    }

    /**
     * Get list of document names
     * @param $vehicle
     * @return string
     */
    public function getDocuments($vehicle)
    {
        $documents ='';
        $dir      = storage_path('app').'/documents/'.$vehicle;
        if(Storage::exists('/documents/'.$vehicle)) {
            $contents = preg_grep('/^([^.])/', scandir($dir));
            if (count($contents) > 0) {
                foreach ($contents as $file) {
                    $documents .= '<a href="'.url('/document/download/'.$vehicle.'/'.urlencode($file)).'" class="list-group-item">'.$file.'</a>';
                }
            }
        }
        return $documents;
    }

    public function listCustomers()
    {
        $results =  Customer::select('id', 'erp_id', 'firstname', 'lastname', 'email', 'phone_1', 'status', DB::raw("DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') AS created_on"))
            ->orderBy('id', 'desc')
            ->paginate(25);

        $response = [
            'pagination' => [
                'total' => $results->total(),
                'per_page' => $results->perPage(),
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem()
            ],
            'data' => $results
        ];

        return $response;
    }

    /**
     * creating customer in warehouse.
     * @param $customer_id
     */
    protected function createCustomerActindo($customer_id)
    {
        $customer = Customer::select('company', 'title', 'firstname', 'lastname', 'street', 'additional_address', 'city', 'country_long', 'postal', 'email', 'phone_1', 'phone_mobile')
            ->where('id',$customer_id)
            ->first();

        $title ='';
        if (!empty($customer)) {
            if ($customer->title == 1) {
                $title = 'Herr';
            } elseif ($customer->title == 2) {
                $title = 'Frau';
            } elseif ($customer->title == 3) {
                $title = 'Firma';
            }

            $params = [
                'deb_kred_id' => NULL,
                'anrede'      => $title,
                'firma'       => $customer->company,
                'kurzname'    => $customer->firstname . ' ' . $customer->lastname,
                'name'        => $customer->lastname,
                'vorname'     => $customer->firstname,
                'land '       => 'D',
            ];
            if ($customer->street != '')
                $params['adresse'] = $customer->street;

            if ($customer->additional_address != '')
                $params['adresse2'] = $customer->additional_address;

            if ($customer->postal != '')
                $params['plz'] = $customer->postal;

            if ($customer->city != '')
                $params['ort'] = $customer->city;

            if ($customer->phone_mobile != '')
                $params['mobiltel'] = $customer->phone_mobile;

            $params['email'] = $customer->email;
            $params['tel'] = $customer->phone_1;

            $soap = new SoapClient('https://www.actindo.biz/actindo/soap.php?WSDL', ['encoding' => 'utf-8']);
            try {
                $sid = $soap->auth__login('shdevelopment', 'W.H*dhtj*w', 38372, 'NOID', 'NOSERIAL');

                $insert = $soap->dk__create($sid, 'deb', $params);

                $erp_id = $insert["deb_kred_id"];
                if ($erp_id > 0)
                    Customer::where('id', $customer_id)->update(['erp_id' => $erp_id]);

                $soap->auth__logout($sid);
            } catch (Exception $e) {

            }
        }
    }
    

    /**
     * List form fields
     */
    public function showFormFields($groupId)
    {
        $formFields = Formfield::select('id' , 'title', 'description', 'placeholder', 'type', 'options' , 'form_group_id', 'validation')
            ->where('form_group_id', $groupId)
            ->get();
        return $formFields;
    }


    /**
     * List form group
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showFormGroup()
    {
        $formGroups = Formgroup::select('form_groups.id', 'form_groups.title')
            ->join('form_fields', 'form_groups.id', '=', 'form_fields.form_group_id')
            ->groupBy('form_groups.title')
            ->get();
        return $formGroups;
    }

    /**
     * Get customer form values
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function customerFormDetails($id)
    {
        $customerDynamicFormDetails = Formvalue::select('form_values.value', 'form_fields.title')
            ->join('form_fields', 'form_values.form_field_id', '=', 'form_fields.id')
            ->where('form_values.parent_id', $id)
            ->where('form_fields.relation', 'customer')
            ->get();
        return $customerDynamicFormDetails;
    }
}
