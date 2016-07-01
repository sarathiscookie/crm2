<?php

namespace App\Http\Controllers;

use App\Customervehicle;
use App\Vehicle;
use Illuminate\Http\Request;

use App\Http\Requests;
use Storage, File;

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
        $vehicle->execution_id   = $request->vehicle;
        $vehicle->chassis_number = $request->chassis;
        $vehicle->license_plate  = $request->license;
        $vehicle->gearbox        = $request->gearbox;
        $vehicle->freetext       = $request->freetext;
        $vehicle->save();
        $id = $vehicle->id;
        if($id>0){
            $customer_vehicle = new Customervehicle();
            $customer_vehicle->customer_id = $request->customer;
            $customer_vehicle->vehicle_id  = $id;
            $customer_vehicle->save();
        }

        return redirect('/customer/details/'.$request->customer);
    }

    /**
     * Check Vehicle status for customer - whether customer already have a vehicle of matched @param - execution_id
     * @param $customer_id
     * @param $execution_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function checkVehicle(Request $request)
    {
        if(!$request->ajax()) {
            return response('bad request');
            exit;
        }
        $vehicles = Vehicle::select('id')
            ->join('customer_vehicles AS CV', 'vehicles.id', '=', 'CV.vehicle_id')
            ->where('CV.customer_id', $request->customer)
            ->where('execution_id', $request->execution_id)
            ->get();

        if(count($vehicles)==0){
            return response(0);
        }
        else {
            return response('Vehicle already exists');
        }
    }


    public function uploadDocuments(Request $request)
    {
        $customerObj = new CustomerController();
        $error = 0;
        $response ='';
        $files = $request->file('vehicle_docs');
        $vehicleID = $request->vehicleNr;

        foreach($files as $file) {
            $path = 'documents/' . $vehicleID;
            $orgName = $file->getClientOriginalName();
            if($orgName!='') {
                Storage::disk('local')->makeDirectory($path, 0777, true);
                Storage::disk('local')->put($path . '/' . $orgName, File::get($file));                
            }
            else{
                $error++;
            }
        }
        if($error==0) {
            $response = $customerObj->getDocuments($vehicleID);
            return response($response);
        }
        else
            return response('<div class="alert alert-danger">Error uploading '.$error.' file(s)</div>');
    }
}
