<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;
use App\Http\Requests\StoreFeedbackRequest;

class FeedbackController extends Controller
{
    protected $NotificationService;
    
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(NotificationService $NotificationService)
    {
        $this->NotificationService = $NotificationService;
    }
        
    //save feedback
    public function store(StoreFeedbackRequest $request)
    {
        $feedback = Feedback::firstOrNew([
            'task_id' => $request->task_id,
        ]);
        $feedback->feedback = $request->feedback;
        $feedback->save();

        $task = Task::findOrFail($request->task_id);

        if ($feedback) {
            $task->update([
                'status'=>'Feedback'
            ]);
            $this->NotificationService->create(
                'A feedback has been given for '. ' '. $task->task.' '. ' task by '.' '.Auth::user()->name ,
                'Feedback',
                $feedback->id,
                'task/conversations/'.$task->id,
                $task->assigned_to,
            );
            $message = 'Task Feedback Sent!';

            return response()->json([
                'feedback'=>$feedback,
                'message'=>$message,
                'status'=>'success',
                'code'=>200
            ]);
        }

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
