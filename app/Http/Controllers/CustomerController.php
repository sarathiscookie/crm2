<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerRequest;
use Mail;

class CustomerController extends Controller
{
    public function index()
    {
        $listCustomers    = Customer::select('id', 'firstname', 'lastname', 'email', 'phone', 'created_at')
            ->orderBy('id', 'desc')
            ->get();
        return view('dashboard', compact('listCustomers'));
    }

    public function create()
    {
        return view('createCustomer');
    }

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
        $customer->customer_id = rand(1,999);
        $customer->firstname = $request->firstname;
        $customer->lastname = $request->lastname;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->street = $street;
        $customer->city = $request->city;
        $customer->country = $request->country;
        $customer->postal = $request->postal;
        $customer->payment = $request->payment;
        $customer->license_plate = $request->license;
        $customer->chassis_number = $request->chassis;
        $customer->mileage = $request->mileage;
        $customer->tuning = $request->tuning;
        $customer->dyno = $request->dyno;
        $customer->freetext = $request->freetext;
        $customer->save();

        Mail::send('emails.newCustomerNotification', ['firstname' => $customer->firstname, 'lastname' => $customer->lastname, 'email' => $customer->email, 'phone' => $customer->phone, 'created_at' => $customer->created_at ], function ($message) use ($customer) {
            $message->to('iamsarath1986@gmail.com')->subject('New customer created');
        });

        return redirect(url('/'))/*->with('status','Created successfully')*/;
    }

    public function show()
    {

    }

}
