<?php

namespace App\Http\Controllers;

use App\Customervehicle;
use App\Vehicle;
use Illuminate\Http\Request;
use App\Http\Requests\VehicleRequest;

use App\Http\Requests;
use Storage, File;

class VehicleController extends Controller
{
    /**
     * Save Vehicle
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

    /**
     * Upload documents
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
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

    /**
     * Download document
     * @param $vehicle_id
     * @param $path
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getDocumentDownload($vehicle_id, $path)
    {
        $customer = Customervehicle::select('customer_id')->where('vehicle_id', $vehicle_id)->first();
        $path = urldecode($path);
        $file = storage_path('app'). '/documents/'.$vehicle_id.'/' .$path;

        if(Storage::disk('local')->exists('documents/'.$vehicle_id.'/' .$path)) {
            return response()->download($file, $path);
        }
        else
            return redirect('/customer/details/'.$customer->customer_id);
    }

    /**
     * Show form - vehicle edit
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEdit($id)
    {
        $customerObj = new CustomerController();
        $vehicle_title='';
        $vehicle = Vehicle::where('id', $id)->first();
        if($vehicle) {
            $vehicleInfo = $customerObj->vehicleDetails($vehicle->execution_id);
            foreach($vehicleInfo as $vehicle_information) {
                if ($vehicle_information->motor_power)
                    $power = $vehicle_information->motor_power;
                else
                    $power = $vehicle_information->ps_from_dimsport_kw;
                $vehicle_title = $vehicle_information->marke_name. " " .$vehicle_information->modell_name. " ". $vehicle_information->tpbezeichnung. " " . "mit " . $power."PS";
            }
        }
        $gears = $customerObj->gearbox;

        return view('editVehicle', ['vehicle'=> $vehicle, 'gears' =>$gears, 'vehicle_title' =>$vehicle_title]);
    }

    /**
     * Update vehicle
     * @param VehicleRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(VehicleRequest $request, $id)
    {
        $vehicle  = Vehicle::find($id);
        $vehicle->chassis_number = $request->chassis;
        $vehicle->license_plate = $request->license;
        $vehicle->gearbox = $request->gearbox;
        $vehicle->freetext = $request->freetext;
        $vehicle->save();

        $related = Customervehicle::select('customer_id')->where('vehicle_id', $id)->first();
        return redirect('/customer/details/'.$related->customer_id);
    }

    /**
     * Delete - mark vehicle status as offline
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if(!$request->ajax())
            return response()->json(['mes' => 'bad request']);
        $id = $request->id;
        $affected = Vehicle::where('id', $id)->update(['status' => 'offline']);
        if($affected>=0){
            $related = Customervehicle::select('customer_id')->where('vehicle_id', $id)->first();
            return response()->json(['mes' => 'done', 'ref' => $related->customer_id]);
        }
        else
            return response()->json(['mes' => '<strong>Error! </strong>Could not be deleted']);

    }
}
