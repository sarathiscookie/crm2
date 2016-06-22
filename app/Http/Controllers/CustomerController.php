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
        return view('dashboard');
    }

    public function create()
    {
        return view('createCustomer');
    }

    public function save(CustomerRequest $request)
    {
        $address = explode("|", $request->address_places);
        $street ='';
        $city ='';
        $country ='';
        $postal ='';
        foreach ($address as $fields)
        {
            $field = explode(":", $fields);
            switch ($field[0]){
                case 'street_number':
                    $street = $field[1];
                    break;
                case 'locality':
                    $city = $field[1];
                    break;
                case 'country':
                    $country = $field[1];
                    break;
                case 'postal_code':
                    $postal = $field[1];
                    break;
            }
        }
        $customer = new Customer();
        $customer->customer_id = rand(1,99);
        $customer->firstname = $request->firstname;
        $customer->lastname = $request->lastname;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->street = $street;
        $customer->city = $city;
        $customer->country = $country;
        $customer->postal = $postal;
        $customer->payment = $request->payment;
        $customer->license_plate = $request->license;
        $customer->chassis_number = $request->chassis;
        $customer->mileage = $request->mileage;
        $customer->tuning = $request->tuning;
        $customer->dyno = $request->dyno;
        $customer->freetext = $request->freetext;
        $customer->save();

        return redirect(url('/customer/create'))->with('status','Created successfully');
    }

    public function show()
    {
        return Customer::select('firstname', 'lastname', 'email', 'phone', 'created_at')
            ->orderBy('id', 'desc')
            ->get();
    }

}
