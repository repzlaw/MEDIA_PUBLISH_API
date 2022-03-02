<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
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
            'website_id'=>'required',
            'publisher_id'=>'required',
            'url'=>'required',
            'title'=>'required',
            'content'=>'required',
            'published_date'=>'required',
            'cost'=>'required',
            'currency'=>'required',
            'usd_cost'=>'required',
            'status'=>'required',
        ];
    }
}
