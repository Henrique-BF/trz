<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSurvivorRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'age' => ['required', 'integer'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'latitude' => ['required', 'numeric', 'between:-90,90'], 
            'longitude' => ['required', 'numeric', 'between:-90,90'],
            'items.*.id' => ['required', 'exists:items,id'],
            'items.*.qty' => ['required', 'integer'],
        ];
    }
}
