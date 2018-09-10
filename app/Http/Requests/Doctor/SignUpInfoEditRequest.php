<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class SignUpInfoEditRequest extends FormRequest
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
            'id' => 'required|numeric',
            'name' => 'nullable|max:100',
            'sex' => 'nullable|numeric',
            'age' => 'nullable|numeric',
            'wanted_award' => 'nullable|numeric',
            'working_year' => 'nullable|numeric',
            'hospital_id' => 'nullable|numeric',
            'hospital_name' => 'nullable',
            'department' => 'nullable|max:100',
            'job_title' => 'nullable|json',
            'phone_number' => 'nullable|numeric',
            'medical_certificate_no' => 'nullable|max:50',
            'email' => 'nullable|max:100',
            'full_face_photo' => 'nullable',
            'doctor_other_info' => 'nullable|json',
        ];
    }
}
