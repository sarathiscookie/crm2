<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname'  => 'required|max:100',
            'lastname'   => 'required|max:100',
            'email'      => 'required|email|max:255|unique:customers,email,'.$this->id,
            'address'    => 'required',
            'phone'      => 'required|max:30',
            'license'    => 'required|max:50',
            'chassis'    => 'required|max:100',
            'mileage'    => 'required|numeric',
            'vehicle'    => 'required',
            'stage'      => 'required|numeric|min:1|max:5',
            'customerstatus' => 'required',
        ];
    }
}
