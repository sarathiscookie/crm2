<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Customer;
use App\Event;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listCustomers    = Customer::select('id', 'erp_id', 'firstname', 'lastname', 'company', 'phone_1', 'created_at')
            ->orderBy('id', 'desc')
            ->get()
            ->take(10);
        return view('dashboard', compact('listCustomers'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTodayEvents()
    {
        $dateToday          = Carbon::now();
        $listTodayEvents    = Event::select('events.id', 'customers.firstname', 'customers.lastname', 'customers.company', 'customers.erp_id', 'vehicles.execution_id', DB::raw("DATE_FORMAT(events.end_at, '%H:%i') AS end_at"), DB::raw("DATE_FORMAT(events.begin_at, '%H:%i') AS begin_at"))
            ->join('customers', 'events.customer_id', '=', 'customers.id')
            ->join('vehicles', 'events.vehicle_id', '=', 'vehicles.id')
            ->where(DB::raw("DATE(events.begin_at)"), $dateToday->toDateString())
            ->get();
        return $listTodayEvents;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTomorrowEvents()
    {
        $dateToday             = Carbon::now()->tomorrow();
        $listTomorrowEvents    = Event::select('events.id', 'customers.firstname', 'customers.lastname', 'customers.company', 'customers.erp_id', 'vehicles.execution_id', DB::raw("DATE_FORMAT(events.end_at, '%H:%i') AS end_at"), DB::raw("DATE_FORMAT(events.begin_at, '%H:%i') AS begin_at"))
            ->join('customers', 'events.customer_id', '=', 'customers.id')
            ->join('vehicles', 'events.vehicle_id', '=', 'vehicles.id')
            ->where(DB::raw("DATE(events.begin_at)"), $dateToday->toDateString())
            ->get();
        return $listTomorrowEvents;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCarName($execution_id)
    {
        $carName = DB::connection('fes')
            ->select("SELECT av.id, av.tuning_id, av.tpbezeichnung, av.marke_name, av.modell_name, av.marke_alias, av.modell_alias, av.kraftstoff, av.vehicletype_title, CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int) as dimsport_kw, CAST(SUBSTRING(substring(tpleistung from (position('/' in tpleistung)+1)), 'm*([0-9]{1,})') as int) as dimsport_ps, round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) as ps_from_dimsport_kw,
										(select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) as motor_id,
										(select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) as motor_power,
										(SELECT CASE WHEN (select t.motor_id from mainpage.tuning t where av.tuning_id = t.id) <> NULL THEN (select m.power from mainpage.motor m, mainpage.tuning t where av.tuning_id = t.id and t.motor_id = m.id) ELSE round((CAST(SUBSTRING(av.tpleistung, 'm*([0-9]{1,})') as int)) * 1.359622) END ) as sort_leistung
									FROM mainpage.ausfuehrung_view_neu av
									WHERE av.id = '$execution_id'");
        return $carName;
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
