<?php

namespace App\Http\Controllers;

use App\Customervehicle;
use App\Vehicle;
use Illuminate\Http\Request;

use App\Http\Requests;

class VehicleController extends Controller
{
    /**
     * save Vehicle
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function saveVehicle(Request $request)
    {
        $vehicle = new Vehicle();
        $vehicle->execution_id = $request->vehicle;
        $vehicle->chassis_number = $request->chassis;
        $vehicle->license_plate = $request->license;
        $vehicle->freetext = $request->freetext;
        $vehicle->save();
        $id = $vehicle->id;
        if($id>0){
            $customer_vehicle = new Customervehicle();
            $customer_vehicle->customer_id = $request->customer;
            $customer_vehicle->vehicle_id = $id;
            $customer_vehicle->save();
        }

        return redirect('/customer/details/'.$request->customer);
    }

    /**
     * CHeck Vehicle status for customer - whether customer has a vehicle of matched @param - execution_id
     * @param $customer_id
     * @param $execution_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getStatus($customer_id, $execution_id)
    {
        $vehicles = Customervehicle::select('id')
            ->join('vehicles', 'vehicles.id', '=', 'customer_vehicles.vehicle_id')
            ->where('customer_id', $customer_id)
            ->where('execution_id', $execution_id)
            ->get();

        if(count($vehicles)==0){
            return response(0);
        }
        else {
            return response(1);
        }
    }
}
