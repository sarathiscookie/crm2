<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Customerhistory;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CustomerHistoryRequest;

class CustomerHistoryController extends Controller
{
    /**
     * Show create notice form for customer
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create($id)
    {
        $customer = Customer::select('id', 'firstname', 'lastname')->where('id', $id)->first();

        return view('createCustomerNotice', ['customer' => $customer]);

    }

    /**
     * Save history as notices
     * @param CustomerHistoryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function save(CustomerHistoryRequest $request)
    {
        $notice = new Customerhistory();
        $notice->customer_id = $request->customer_id;
        $notice->freetext   = $request->freetext;
        $notice->status     = 'online';
        $notice->save();

        return redirect('/customer/details/'.$request->customer_id);
    }
}
