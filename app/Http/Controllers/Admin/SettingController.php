<?php

namespace App\Http\Controllers\Admin;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;

class SettingController extends Controller
{
    use ApiResponse;

    /**
     * Only auth for "admin" guard are allowed
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Configuration::all();

        return $this->success( SettingResource::collection(($settings)),
                                'request success',
                                200
                            );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $setting = '';
        if ($request->has('login_email') && $request->has('new_task_email') && $request->has('task_coversation_email')) {
            $set = Configuration::where('key','login_email')->first();
            $setting = $set->update([
                'value'=> $request->login_email,
            ]);

            $set = Configuration::where('key','new_task_email')->first();
            $setting = $set->update([
                'value'=> $request->new_task_email,
            ]);

            $set = Configuration::where('key','task_coversation_email')->first();
            $setting = $set->update([
                'value'=> $request->task_coversation_email,
            ]);
        }

        return response()->json([
            'setting'=>$setting,
            'message'=>'setting saved successfully',
            'status'=>'success',
            'code'=>200
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
