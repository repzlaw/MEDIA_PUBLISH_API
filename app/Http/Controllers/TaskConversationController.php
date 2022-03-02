<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\TaskConversation;
use App\Http\Requests\StoreTaskConversationRequest;

class TaskConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //index
    public function index($task_id)
    {
        $task = Task::findOrFail($task_id);
        $conversations = TaskConversation::where('task_id',$task_id)->get();
        return response()->json([
            'task'=>$task,
            'conversations'=>$conversations,
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
    public function store(StoreTaskConversationRequest $request)
    {
        $fileNameToStore = NULL;
        if ($request->hasFile('attachment')) {
            //process doc
            $fileNameToStore = process_image($request->file('attachment'));

            //store doc
            $path = $request->file('attachment')->storeAs('public/tasks/attachments', $fileNameToStore);

        }
        $conversation = TaskConversation::create([
            'task_id'=>$request->task_id,
            'message'=>$request->message,
            'sender_id'=>$request->user()->id,
            'file_path'=>$fileNameToStore,
        ]);

        return response()->json([
            'conversation'=>$conversation,
            'message'=>'conversation saved',
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
