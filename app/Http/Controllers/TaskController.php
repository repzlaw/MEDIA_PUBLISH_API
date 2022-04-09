<?php

namespace App\Http\Controllers;

use ZipArchive;
use DOMDocument;
use HTMLPurifier;
use Carbon\Carbon;
use App\Models\Link;
use App\Models\Task;
use App\Models\User;
use App\Models\Payout;
use App\Models\Region;
use App\Models\Website;
use HTMLPurifier_Config;
use App\Mail\TaskCreated;
use App\Models\Publisher;
use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;
use App\Http\Requests\StoreTaskRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CancelTaskRequest;
use App\Http\Requests\ReviewTaskRequest;
use App\Http\Requests\SubmitTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
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

    //return task page
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->type === 'Admin') {
            $tasks= Task::latest()->paginate(10);
        } else {
            $tasks = Task::where('assigned_to',$user->id)->latest()->paginate(10);
        }

        return response()->json([
            'tasks'=>$tasks,
            'message'=>'request success',
            'status'=>'success',
            'code'=>200
        ]);
    }

    //create task
    public function store(StoreTaskRequest $request)
    {
        $fileNameToStore ='';
        if ($request->hasFile('attachment')) {
            $fileNameToStore = process_image($request->file('attachment'));
            //store file
            $path = $request->file('attachment')->storeAs('public/tasks/attachments', $fileNameToStore);
        }
        
        $task = Task::create([
            'task'=>$request->task,
            'topic'=>$request->topic,
            'attachment'=>$fileNameToStore,
            'references'=>$request->references,
            'region_target'=>$request->region_target,
            'website_id'=>$request->website_id,
            'assigned_to'=>$request->assigned_to,
            'instructions'=>$request->instructions,
            'task_type'=>$request->task_type,
            'task_given_on'=>date("Y-m-d H:i:s"),
            'admin_id'=>$request->user()->id,
            'word_limit'=>$request->word_limit,
            'time_limit'=>$request->time_limit
        ]);

        if ($task) {
            $this->NotificationService->create(
                'A task was assigned to you by '. ' ' .$request->user()->name,
                'Task',
                $task->id,
                'tasks/conversations/'.$task->id,
                $request->assigned_to,
            );
            $admin = $request->user();
            $user = User::findOrFail($request->assigned_to);
            $config = Configuration::where('key','new_task_email')->first();

            if ($config) {
                $config->value ? Mail::to($user->email, $user->name)->queue(new TaskCreated($user,$admin)): '';
            }

        }

        return response()->json([
            'task'=>$task,
            'message'=>'request success',
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
    public function show(Task $task)
    {
        return response()->json([
            'task'=>$task,
            'message'=>'request success',
            'status'=>'success',
            'code'=>200
        ]);
    }


    //update task
    public function update(UpdateTaskRequest $request)
    {
        $task = Task::findOrFail($request->task_id);
        $task->update([
            'task'=>$request->task,
            'topic'=>$request->topic,
            'status'=>$request->status,
            'region_target'=>$request->region_target,
            'website_id'=>$request->website_id,
            'assigned_to'=>$request->assigned_to,
            'instructions'=>$request->instructions,
            'task_type'=>$request->task_type,
            'word_limit'=>$request->word_limit,
            'time_limit'=>$request->time_limit
        ]);

        return response()->json([
            'task'=>$task,
            'message'=>'task update success',
            'status'=>'success',
            'code'=>200
        ]);
    }

    //submit task
    public function submitTask(SubmitTaskRequest $request)
    { 
        // return $request->file('document');
        if ($request->hasFile('document')) {
            //get document word count
            $doc_string = docx2text($request->file('document'));
            $doc_count = str_word_count($doc_string);

            //process doc
            $fileNameToStore = process_image($request->file('document'));

            //store doc
            $path = $request->file('document')->storeAs('public/tasks', $fileNameToStore);

            //get old task doc if exist
            $task = Task::where('id',$request->task_id)->firstOrFail();
            if ($task) {
                $task_document = $task->file_path;
                if ($task_document) {
                    unlink(storage_path("app/public/tasks/".$task_document));
                }
            }

            $tasks = $task->update([
                'word_count'=>$doc_count,
                'file_path'=>$fileNameToStore,
                'status'=>'Submitted',
                'task_submitted_on'=>date("Y-m-d H:i:s"),
            ]);

            if ($tasks) {
                $this->NotificationService->create(
                    'A task has been submitted',
                    'Task',
                    $task->id,
                    'tasks/conversations/'.$task->id,
                    $task->admin_id,
                );
            }
    
        }
        return response()->json([
            'task'=>$task,
            'message'=>'Task Document Uploaded!',
            'status'=>'success',
            'code'=>200
        ]);
    }

    //submit review
    public function submitReview(ReviewTaskRequest $request)
    {
        $task = Task::findOrFail($request->task_id);
        $task->update($request->only('status','feedback'));

        if ($task) {
            $message = 'Task Reviewed Successfully!';
        }

        return response()->json([
            'task'=>$message,
            'message'=>'request success',
            'status'=>'success',
            'code'=>200
        ]);
    }

    //search tasks
    public function searchUser(Request $request)
    {
        $searchData = $request->input('query');
        $searchColumn = $request->input('search_column');
        $tasks= '';

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $searchData = $purifier->purify($searchData);
        $searchColumn = $purifier->purify($searchColumn);

        if (!is_null($searchData)) {
            if ($searchColumn==='name') {
                $tasks = DB::table('tasks as t')
                            ->join('users as u','u.id','t.assigned_to')
                            ->where('u.name','like', "%$searchData%")
                            ->select('t.*')
                            ->paginate(50);
            }elseif ($searchColumn==='date') {
                $tasks = Task::where('task_given_on', 'like', "%$searchData%")->paginate(50);
            }elseif ($searchColumn==='desc') {
                $tasks = Task::where('task', 'like', "%$searchData%")->paginate(50);
            }elseif ($searchColumn==='type') {
                $tasks = Task::where('task_type', 'like', "%$searchData%")->paginate(50);
            }elseif ($searchColumn==='region') {
                $tasks = DB::table('tasks as t')
                            ->join('regions as r','r.id','t.region_target')
                            ->where('r.name','like', "%$searchData%")
                            ->select('t.*')
                            ->paginate(50);
            }elseif ($searchColumn==='website') {
                $tasks = DB::table('tasks as t')
                            ->join('websites as w','w.id','t.region_target')
                            ->where('w.website_code','like', "%$searchData%")
                            ->select('t.*')
                            ->paginate(50);
            }
        }

        return response()->json([
            'tasks'=>$tasks,
            'message'=>'request success',
            'status'=>'success',
            'code'=>200
        ]);

    }

    //cancel task
    public function cancelTask(CancelTaskRequest $request)
    {
        $user =$request->user();
        $task = Task::findOrFail($request->task_id);

        if ($user->type === 'Admin') {
            $task->update([
                'admin_notes'=> $request->reason,
                'status'=> 'Cancelled'
            ]);
        } else if ($user->type === 'Writer') {
            $task->update([
                'writer_notes'=> $request->reason,
                'status'=> 'Cancelled'
            ]);
        }
        else {
            $task->update([
                'editor_notes'=> $request->reason,
                'status'=> 'Cancelled'
            ]);
        }

        $message = 'Task Cancelled Successfully!';

        return response()->json([
            'task'=>$task,
            'message'=>$message,
            'status'=>'success',
            'code'=>200
        ]);

    }

    //acknowlegde task
    public function acknowledgeTask(Request $request, Task $task)
    {
        $user = $request->user();
        $assigned_user = User::findOrFail($task->assigned_to);

        if ($user->type !== 'Admin') {
            $task->update([
                'status'=> 'Acknowledged'
            ]);
        }
        $this->NotificationService->create(
           $task->task. ' '. ' task has been acknowlegded by '. ' '.$assigned_user->name ,
            'Task',
            $task->id,
            'tasks/conversations/'.$task->id,
            $task->admin_id,
        );
        $message = 'Task Acknowlegded Successfully!';

        return response()->json([
            'message'=>$message,
            'status'=>'success',
            'code'=>200
        ]);
    }

    //copy task
    public function copyTask(Task $task)
    {
        $current_date_time = Carbon::now()->toDateTimeString();
        
        $newTask = $task->replicate();
        $newTask->status = 'Pending';
        $newTask->task_given_on = $current_date_time;
        $newTask->created_at = $current_date_time;
        $newTask->updated_at = $current_date_time;
        $newTask->save();

        return response()->json([
            'newTask'=>$newTask,
            'message'=>'task copied successfully',
            'status'=>'success',
            'code'=>200
        ]);
        
    }
}