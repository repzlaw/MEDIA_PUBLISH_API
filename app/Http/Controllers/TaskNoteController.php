<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskNote;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Http\Requests\StoreNoteRequest;

class TaskNoteController extends Controller
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
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Task $task)
    {
        $user = $request->user();
        if ($user->type === 'Admin') {
            $task_notes= TaskNote::latest()->get();
        } else {
            $task_notes = TaskNote::where('status','Public')->latest()->get();
        }

        return response()->json([
            'task_notes'=>$task_notes,
            'task'=>$task,
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
    public function store(StoreNoteRequest $request)
    {
        $user = $request->user();
        $task = Task::findOrFail($request->task_id);

        if ($user->type === 'Admin') {
            $task_note= TaskNote::create($request->validated());
        } else {
            $task_note = TaskNote::create([
                'task_id'=> $request->task_id,
                'note'=> $request->note,
                'status'=> 'public',
            ]);
            $this->NotificationService->create(
                'A note has been added for '. ' '. $task->task.' '. ' task by '.' '.$request->user()->name,
                'TaskNote',
                $task_note->id,
                'task/notes/'.$task->id,
                $task->admin_id,
            );
        }

        return response()->json([
            'task_note'=>$task_note,
            'message'=>'note added successfully',
            'status'=>'success',
            'code'=>200
        ]);

    }

    //share notes
    public function shareNotes(Request $request)
    {
        $note = TaskNote::findOrFail($request->note_id);
        $task = Task::findOrFail($note->task_id);

        if ($note->status === 'Private') {
            $note->update([
                'status'=>'Public'
            ]);
            $this->NotificationService->create(
                'A note has been shared for '. ' '. $task->task.' '. ' task by '.' '.$request->user()->name,
                'TaskNote',
                $note->id,
                'task/notes/'.$task->id,
                $task->assigned_to,
            );
        } else if($note->status === 'Public') {
            $note->update([
                'status'=>'Private'
            ]);
        }
        return response()->json([
            'note'=>$note,
            'message'=>'note shared successfully',
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
