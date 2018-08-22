<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class ManagerAddRequest extends FormRequest
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
            'nickname' => 'required|max:100',
            'account' => 'required|max:100',
            'password' => 'required|max:32',
            'role' => 'required|numeric',
            'note' => 'nullable',
        ];
    }
}
