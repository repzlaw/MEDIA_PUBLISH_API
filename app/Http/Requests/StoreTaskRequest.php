<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
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
            'task'=>'required',
            'topic'=>'required',
            'instructions'=>'required',
            'region_target'=>'required|exists:regions,id',
            'website_id'=>'required|exists:websites,id',
            'assigned_to'=>'required|exists:users,id',
            'task_type'=>'required',
            'word_limit'=>'integer',
            'time_limit'=>'integer'
        ];
    }
}
