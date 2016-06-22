<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Customervehicle;
use App\Event;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use Mail;

class CustomerController extends Controller
{
    /**
     * view dashboard
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $listCustomers    = Customer::select('id', 'firstname', 'lastname', 'email', 'phone', 'created_at')
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
        return redirect(url('/'))/*->with('status','Created successfully')*/;
    }

    public function show()
    {

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
    
    protected function saveEvent($customer_id, $vehicle_id, $request)
    {
        if($customer_id>0 && $vehicle_id>0) {
            $event = new Event();
            $event->customer_id = $customer_id;
            $event->vehicle_id  = $vehicle_id;
            $event->partner_id  = 1149;
            $event->title       = 'Terminvereinbarung';
            $event->freetext_external = $request->freetext;
            $event->mileage = $request->mileage;
            $event->tuning  = $request->tuning;
            $event->dyno    = $request->dyno;
            $event->payment = $request->payment;
            $event->begin_at = Carbon::now();
            $event->save();
        }


    }

    protected function saveCustomerVehicle($customer_id, $vehicle_id)
    {
        $cust_vehicle = new Customervehicle();
        $cust_vehicle->customer_id =$customer_id;
        $cust_vehicle->vehicle_id =$vehicle_id;
        $cust_vehicle->save();
    }

}
