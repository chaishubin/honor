<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class SignUpRequest extends FormRequest
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
            'name' => 'required|max:100',
            'sex' => 'required|numeric',
            'age' => 'required|numeric',
            'wanted_award' => 'required|numeric',
            'working_year' => 'required|numeric',
            'hospital_id' => 'required|numeric',
            'hospital_name' => 'required',
            'department' => 'required|max:100',
            'job_title' => 'required|json',
            'medical_certificate_no' => 'required|max:50',
            'email' => 'required|max:100',
            'full_face_photo' => 'required',
            'doctor_other_info' => 'nullable|json',
        ];
    }
}
