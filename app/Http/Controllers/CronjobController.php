<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Event;
use Illuminate\Http\Request;

use App\Http\Requests;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use DB;

use SoapClient;
use Exception;

class CronjobController extends Controller
{
    /**
     * Event reminder Cron
     */
    public function sendEventReminder()
    {
        $customerObj = new CustomerController();
        $events = Event::select('events.id', 'vehicles.execution_id', 'events.title', 'begin_at', 'CUS.title as salutation', 'CUS.firstname', 'CUS.lastname', 'CUS.email', 'vehicles.license_plate')
            ->join('vehicles', 'vehicles.id', '=', 'events.vehicle_id')
            ->join('customers AS CUS', 'CUS.id', '=', 'events.customer_id')
            ->whereNull('reminded_at')
            ->where('begin_at', '<=', Carbon::now()->addDay())
            ->get();
        if(count($events)>0)
        {
            foreach ($events as $event) {
                $title ='';
                if($event->salutation>0)
                    $title = $customerObj->customerTitle[$event->salutation];
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
                $i=0;
                if ($event->email != '') {
                    try {                        
                        Mail::send('emails.eventReminder', ['event' => $event, 'vehicle' =>$vehicle_title, 'title' =>$title], function ($message) use ($event) {
                            $message->to($event->email, $event->firstname . ' ' . $event->lastname)
                                ->subject('Event Reminder');
                        });
                    } catch (Exception $e) {
                        $i++;
                    }
                    if($i==0){
                        Event::where('id', $event->id)->update(['reminded_at' => Carbon::now()]);
                    }
                }
            }
        }
    }

    /**
     * Cron- send feedback URL to customer for the last event
     */
    public function sendFeedbackLink()
    {
        $customerObj = new CustomerController();
        $events = Event::select('events.id', 'vehicles.execution_id', 'events.title', 'begin_at', 'CUS.title as salutation', 'CUS.firstname', 'CUS.lastname', 'CUS.email', 'vehicles.license_plate')
            ->join('vehicles', 'vehicles.id', '=', 'events.vehicle_id')
            ->join('customers AS CUS', 'CUS.id', '=', 'events.customer_id')
            ->whereNull('feedback_at')
            ->where('begin_at', '<=', Carbon::now()->subDays(7))
            ->get();
        if(count($events)>0)
        {
            foreach ($events as $event) {
                $title ='';
                if($event->salutation>0)
                    $title = $customerObj->customerTitle[$event->salutation];
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
                $i=0;
                if ($event->email != '') {
                    try {                        
                        Mail::send('emails.eventFeedback', ['event' => $event, 'vehicle' =>$vehicle_title, 'title' =>$title], function ($message) use ($event) {
                            $message->to($event->email, $event->firstname . ' ' . $event->lastname)
                                ->subject('Event Feedback');
                        });
                    } catch (Exception $e) {
                        $i++;
                    }
                    if($i==0){
                        Event::where('id', $event->id)->update(['feedback_at' => Carbon::now()]);
                    }
                }
            }
        }
    }

    /**
     * Cron - Insert new customers from Actindo into db
     */
    public function syncCustomer()
    {

        $erp_max = Customer::max('erp_id');
        $start = $erp_max + 1;
        $end = $start + 5;



        $soap = new SoapClient('https://www.actindo.biz/actindo/soap.php?WSDL', ['encoding' => 'utf-8']);
        try {
            //$sid = $soap->auth__login('shdevelopment', 'W.H*dhtj*w', 38372, 'NOID', 'NOSERIAL');
            $sid = $soap->auth__login('ep_dev', 'lib2016', 41781, 'NOID', 'NOSERIAL');

            //for($i=$start; $i<= $end; $i++){
                $result = $soap->dk__get_content( $sid, 12551, NULL );
                //$result = $soap->dk__search( $sid, 'deb', ['deb_kred_id'=>12551, 'vorname' => 'Christian'], 0, true );

                print_r($result);
            //}

            $soap->auth__logout($sid);
        } catch (Exception $e) {
            var_dump( $e );
        }
            

    }



}
