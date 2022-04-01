<?php

namespace App\Http\Controllers;

use App\Models\Messaging;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\MessagingResource;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessagingRequest;

class MessagingController extends Controller
{
    use ApiResponse;

    /**
     * Only auth for "admin" guard are allowed
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('admin')->only('destroy');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $messages = Messaging::where(['from_user_id'=> Auth::id()])
                               ->orWhere(['to_user_id'=>Auth::id()])
                               ->get();

        return $this->success( MessagingResource::collection(($messages)),
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
    public function store(StoreMessageRequest $request)
    {
        $message = Messaging::create([
            'message'=> $request->message,
            'from_user_id'=> Auth::id(),
            'to_user_id'=> $request->to_user_id,
        ]);

        return $this->success( new MessagingResource(($message)),
                                'message saved successfully',
                                201
                            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id)
    {
        $messages = Messaging::where(['from_user_id'=> Auth::id(),'to_user_id'=>$user_id])
                               ->orWhere(['from_user_id'=> $user_id,'to_user_id'=>Auth::id()])
                               ->get();

        return $this->success( MessagingResource::collection(($messages)),
                               'request success',
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
    public function update(UpdateMessagingRequest $request, Messaging $messaging)
    {
        $messaging->update($request->validated());

        return $this->success( new MessagingResource(($messaging)),
                                'message updated successfully',
                                200
                            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Messaging $messaging)
    {
        $messaging->delete();
        return $this->success( NULL,
                                'message deleted',
                                200
                            );
    }
}
