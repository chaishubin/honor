<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class SignUpListRequest extends FormRequest
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
            'name' => 'nullable|max:100',
            'job_title' => 'nullable|numeric',
            'hospital_name' => 'nullable',
            'department' => 'nullable|max:100',
            'status' => 'nullable|numeric',
            'cur_page' => 'nullable|numeric',
            'length' => 'nullable|numeric',
        ];
    }
}
