<?php

namespace App\Http\Requests\Vote;

use Illuminate\Foundation\Http\FormRequest;

class CandidateVoteListRequest extends FormRequest
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
            'award_id' => 'required|numeric',
            'province' => 'nullable|numeric',
            'doctor_name' => 'nullable',
            'cur_page' => 'nullable|numeric',
            'length' => 'nullable|numeric'
        ];
    }
}
