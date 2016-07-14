<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CustomerEditRequest extends Request
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
    { //'address'    => 'required',
        return [
            'firstname'  => 'required|max:100',
            'lastname'   => 'required|max:100',
            'email'      => 'required|email|max:255|unique:customers,email,'.$this->id,
            'phone'      => 'required|max:30',
            'status'     => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'phone' => 'phone-1'
        ];
    }
}
