<?php

namespace App\Http\Requests\Manager;

use Illuminate\Foundation\Http\FormRequest;

class ManagerEditRequest extends FormRequest
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
            'nickname' => 'nullable|max:100',
            'account' => 'nullable|max:100',
            'password' => 'nullable|max:32',
            'role' => 'nullable|numeric',
            'note' => 'nullable',
        ];
    }
}
