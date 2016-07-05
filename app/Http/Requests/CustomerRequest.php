<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Formfield;

class CustomerRequest extends Request
{
    public $formFields;
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
        $rules = [
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
        $j = 0;
        while($j < count($this->request->get('fieldID'))) {
            $id         = $this->request->get('fieldID')[$j];
            $form_field = Formfield::where('id', $id)->select('title', 'validation')->first();
            if($form_field->validation != null && $form_field->validation != '')
            {
                $rules['dynField_'.$id]            = $form_field->validation;
                $this->formFields['dynField_'.$id] = $form_field->title;
            }
            $j++;
        }
        return $rules;
    }

    public function attributes(){
        $label =[];
        if(count($this->formFields)>0)
        {
            foreach($this->formFields as $field =>$alias)
            {
                $label[$field] = $alias;
            }
        }
        return $label;
    }
}
