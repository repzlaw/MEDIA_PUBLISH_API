<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payout;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayoutResource;
use App\Http\Requests\StorePayoutRequest;
use App\Models\Task;

class PayoutController extends Controller
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
        $payouts = Payout::paginate(50);

        return $this->success( PayoutResource::collection(($payouts)),
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
    public function store(StorePayoutRequest $request)
    {
        if ($request->has('task_id')) {
            $payout = Payout::firstOrCreate([
                        'task_id'=>$request->task_id,
                        'user_id'=>$request->user_id,
            ]);
            $payout->status = $request->status;
            $payout->amount = $request->amount;
            $payout->save();

            return $this->success( new PayoutResource(($payout)),
                                'payout added successfully',
                                200
                            );
        }

        $payout = Payout::create($request->validated());


        return $this->success( new PayoutResource(($payout)),
                                'payout added successfully',
                                200
                            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Payout $payout)
    {
        return $this->success( new PayoutResource(($payout)),
                                'payout fetch success',
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
    public function update(StorePayoutRequest $request, Payout $payout)
    {
        $payout->update($request->validated());

        return $this->success( new PayoutResource(($payout)),
                                'payout updated successfully',
                                200
                            );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payout $payout)
    {
        $payout->delete();
        return $this->success( NULL,
                                'payout deleted',
                                200
                            );
    }

    //map payout to task
    public function mapToTask(Request $request)
    {
        $task  = Task::findOrFail($request->task_id);
        $payout  = Payout::findOrFail($request->payout_id);

        $task->update([
            'payout_id'=>$request->payout_id
        ]);

        $payout->update([
            'task_id'=>$request->task_id
        ]);
        
        return $this->success( new PayoutResource(($payout)),
                                'payout mapped to task successfully',
                                200
                            );
    }
}
