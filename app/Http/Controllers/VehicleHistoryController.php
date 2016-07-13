<?php

namespace App\Http\Controllers;

use App\Customervehicle;
use App\Vehicle;
use App\Vehiclehistory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\VehicleHistoryRequest;

class VehicleHistoryController extends Controller
{

    /**
     * Show create notice form for vehicle
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($id)
    {
        $vehicle_title ='';
        $vehicle = Vehicle::select('execution_id')->where('id', $id)->first();
        if($vehicle) {
            $customerObj = new CustomerController();
            $vehicleInfo = $customerObj->vehicleDetails($vehicle->execution_id);

            foreach($vehicleInfo as $vehicle_information) {
                if ($vehicle_information->motor_power)
                    $power = $vehicle_information->motor_power;
                else
                    $power = $vehicle_information->ps_from_dimsport_kw;
                $vehicle_title = $vehicle_information->marke_name. " " .$vehicle_information->modell_name. " ". $vehicle_information->tpbezeichnung. " " . "mit " . $power."PS";
            }

        }
        return view('createNotice', ['vehicle' => $vehicle_title, 'vehicle_id' => $id]);

    }

    /**
     * Save vehicle history as notice
     * @param VehicleHistoryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(VehicleHistoryRequest $request)
    {
        $notice = new Vehiclehistory();
        $notice->vehicle_id = $request->vehicle_id;
        $notice->freetext   = $request->freetext;
        $notice->status     = 'online';
        $notice->save();

        $related = Customervehicle::select('customer_id')->where('vehicle_id', $request->vehicle_id)->first();
        return redirect('/customer/details/'.$related->customer_id);
    }
}
