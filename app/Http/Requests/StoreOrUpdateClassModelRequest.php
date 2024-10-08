<?php

namespace App\Http\Requests;

use App\Models\ClassModel;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateClassModelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->classModel) {
            //to authorize the update method
            return $this->user()->can('update', $this->classModel);
        }

        //to authorize the create method
        return $this->user()->can('create', ClassModel::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'suffix' => 'required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'suffix.required' => 'A group is required',
        ];
    }
}
