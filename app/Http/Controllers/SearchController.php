<?php

namespace App\Http\Controllers;

use App\Customer;
use Illuminate\Http\Request;

use App\Http\Requests;

class SearchController extends Controller
{
    /**
     * Backend - Search
     * @param Request $request
     * @return mixed
     */
    public function search(Request $request)
    {
        if(!$request->ajax()) {
            return response()->json(['result'=>'bad request']);
        }

        $keyword =  $request->key;
        $result_customers = $this->searchCustomers($keyword);


        return response()->json(['result'=>$result_customers]);
    }


    /**
     * search customers
     * @param $keyword
     * @return string
     */
    protected function searchCustomers($keyword)
    {
        $result_customer ='';
        $customers             = Customer::select('customers.id', 'firstname', 'lastname')
            ->leftjoin('customer_vehicles AS CV', 'CV.customer_id','=','customers.id' )
            ->leftjoin('vehicles AS V', 'V.id','=','CV.vehicle_id' )
            ->where(function ($query) use($keyword) {
                $query->where('firstname', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('lastname', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('email', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('erp_id', 'LIKE', '%'.$keyword.'%')
                    ->orWhere('V.license_plate', 'LIKE', '%'.$keyword.'%');
            })
            ->orderBy('firstname')
            ->groupBy('customers.id')
            ->get();
        if(count($customers)>0) {
            $result_customer = '<div class="list-group"><h5 class="list-group-item-heading">CUSTOMERS ('.count($customers).')</h5>';
            foreach ($customers as $customer) {
                $result_customer .= '<a href="'.url('/customer/details/'.$customer->id).'" class="list-group-item">
'.title_case($customer->firstname).' '.title_case($customer->lastname).'
</a>';
            }
            $result_customer .='</div>';
        }
        return  $result_customer;
    }
}
