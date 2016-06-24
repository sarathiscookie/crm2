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
            $message->to('iamsarath1986@gmail.com')->subject('New customer created');
        });

        $vehicle_id = $this->saveVehicle($request);
        $this->saveEvent($customer_id, $vehicle_id, $request);
        $this->saveCustomerVehicle($customer_id, $vehicle_id);

        /* Save data in to hardware table and vehicle_hardwares */
        $tags = explode(',', $request->hardwares);

        foreach($tags as $key) {
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
            $event->title       = 'Terminvereinbarung';
            $event->freetext_external = $request->freetext;
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
        $cars     = $this->getCustomerVehicles($id);



        return view('customerDetails', ['customer' => $customer, 'events' => $events]);
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
                             <div>Vechicle: '.$event->vehicle_id.'</div>
                             <div>Mileage: '.$event->mileage.'</div>
                             <div>Tuning: '.$event->tuning.'</div>
                             <div>Dyno: '.$event->dyno.'</div>
                             <div>Payment: '.$event->payment.'</div>
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
        $customer_events = Customervehicle::select('vehicles.*')
            ->where('customer_id', $customer_id)
            ->join('vehicles', 'vehicles.id', '=', 'customer_vehicles.vehicle_id')
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
                             <div>Vechicle: '.$event->vehicle_id.'</div>
                             <div>Mileage: '.$event->mileage.'</div>
                             <div>Tuning: '.$event->tuning.'</div>
                             <div>Dyno: '.$event->dyno.'</div>
                             <div>Payment: '.$event->payment.'</div>
                            ' . $event->freetext_external . '
                        </div>
                    </div>
                </div>';
            $i++;
        }

        return $events;
    }

}
