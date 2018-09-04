<?php

namespace App\Http\Requests\Doctor;

use Illuminate\Foundation\Http\FormRequest;

class TeamSignUpRequest extends FormRequest
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
            'wanted_award' => 'required|numeric',
            'hospital_id' => 'required|numeric',
            'hospital_name' => 'required',
            'department' => 'required|max:100',
            'email' => 'required|max:100',
            'full_face_photo' => 'required',
            'doctor_other_info' => 'nullable',
        ];
    }
}
