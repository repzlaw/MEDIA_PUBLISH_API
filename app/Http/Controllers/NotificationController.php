<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    use ApiResponse;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $logs = Notification::orderBy('created_at','desc')
                ->where(['reciever_id'=> $request->user()->id])->paginate(50)->groupBy(function($item) {
            return $item->created_at->isoFormat('dddd MMMM D ');
       });

       return response()->json([
           'logs'=>$logs,
           'message'=>'request success',
           'status'=>'success',
           'code'=>200
       ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Notification $notification)
    {
        $notification->update([
            'status'=>'seen'
        ]);

        return $this->success( new NotificationResource(($notification)),
                                'notification fetch success',
                                200
                            );
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

    //mark read or unread
    public function changeStatus(Request $request)
    {
        $log = Notification::findOrFail($request->notification_id);
        if ($log->status === 'unseen') {
            $log->update([
                'status'=>'seen'
            ]);
        } else if($log->status === 'seen') {
            $log->update([
                'status'=>'unseen'
            ]);
        }

        return response()->json([
            'log'=>$log,
            'message'=>'request success',
            'status'=>'success',
            'code'=>200
        ]);
    }
}
