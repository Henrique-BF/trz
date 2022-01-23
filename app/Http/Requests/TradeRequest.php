<?php

namespace App\Http\Requests;

use App\Models\Survivor;
use Illuminate\Foundation\Http\FormRequest;

class TradeRequest extends FormRequest
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
            'survivor_1_id' => ['required', 'exists:survivors,id'],
            'survivor_2_id' => ['required', 'exists:survivors,id'],
            'items_survivor_1.*.id' => ['required', 'exists:items,id'],
            'items_survivor_1.*.qty' => ['required', 'numeric'],
            'items_survivor_2.*.id' => ['required', 'exists:items,id'],
            'items_survivor_2.*.qty' => ['required', 'numeric'],
        ];
    }
}
