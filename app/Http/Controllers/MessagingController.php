<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Messaging;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AuthResource;
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
        $id = Auth::id();
        $users = User::where('id', '!=', $id)->get(['id','name']);

        $unreadIds = Messaging::select(DB::raw(' `from_user_id` as sender_id, count(`from_user_id`) as messages_count'))
                                ->where('to_user_id', $id)
                                ->where('read', 'false')
                                ->groupBy('from_user_id')
                                ->get();

        $users = $users->map(function($user) use ($unreadIds) {
                    $contactUnread = $unreadIds->where('sender_id', $user->id)->first();
                    $user->unread = $contactUnread ? $contactUnread->messages_count : 0;
                    return $user;
                });

        $users = $users->toArray();
            usort($users, function($a, $b) {
                
                if ($a['unread'] == $b['unread']) {
                return 0;
            }
            return ($a['unread'] > $b['unread']) ? -1 : 1;
        });

        return $this->success( $users,
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
        
        $message->load(['sender:id,name','recipient:id,name']);

        return $this->success( new MessagingResource($message),
                                'message created successfully',
                                201
                            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($from_id)
    {
        $id = Auth::id();

        Messaging::where('from_user_id',$from_id)->where('to_user_id', $id)->update(['read'=>true]);

        $messages = Messaging::where(function ($query) use ($id,$from_id) {
                        $query->where('from_user_id', '=', $id)
                            ->where('to_user_id', '=', $from_id);
                    })->orWhere(function ($query) use ($id,$from_id) {
                        $query->where('from_user_id', '=', $from_id)
                            ->where('to_user_id', '=', $id);
                    })->orderBy('created_at','asc')->get();

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

        $messaging->load(['sender:id,name','recipient:id,name']);

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
