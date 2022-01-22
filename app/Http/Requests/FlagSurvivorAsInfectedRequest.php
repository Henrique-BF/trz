<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FlagSurvivorAsInfectedRequest extends FormRequest
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
            'survivor_id' => ['required', 'exists:survivors,id'],
            'flag_survivor_id' => ['required', 'exists:survivors,id'],
        ];
    }
}
