<?php

namespace App\Http\Requests;

use App\Models\AcademicYear;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateAcademicYearRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->academicYear) {
            //to authorize the update method
            return $this->user()->can('update', $this->academicYear);
        }

        //to authorize the create method
        return $this->user()->can('create', AcademicYear::class);
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
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ];
    }
}
