<?php
namespace App\Services;

use App\Models\Log;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    //create notification
    public function create($message, $model, $model_id, $url, $reciever_id)
    {
        $log = Notification::create([
            'message'=>$message,
            'model'=>$model,
            'url'=>$url,
            'reciever_id'=>$reciever_id,
            'causer_id'=>Auth::id(),
            'model_id'=>$model_id
        ]);

        return response()->json(['status'=>'ok','log'=> $log], 200);
    }

    public function count()
    {
        $log = Notification::where(['reciever_id'=>Auth::id(), 'status'=>'unseen'])->count();
        
        return $log;
    }
}
