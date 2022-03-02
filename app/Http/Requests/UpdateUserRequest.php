<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required',
            'type'=> 'required',
            'country'=> 'required',
            'bank_details'=> 'required',
            'payout_per_word'=> 'required',
            'fixed_monthly_payout'=> 'required',
            'total_payout'=> 'required',
            'currency'=> 'required',
        ];
    }
}
