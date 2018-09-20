<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class ExpertListRequest extends FormRequest
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
            'phone_number' => 'nullable|numeric',
            'name' => 'nullable|max:100',
            'cur_page' => 'nullable|numeric',
            'length' => 'nullable|numeric'
        ];
    }
}
