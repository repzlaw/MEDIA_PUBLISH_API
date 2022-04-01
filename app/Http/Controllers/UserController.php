<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Resources\OnlineUsersResource;

class UserController extends Controller
{
    use ApiResponse;

    public function onlineStatus(Request $request)
    {
        $users = User::orderBy('last_seen', 'DESC')
                     ->paginate(30);
          
        return $this->success( OnlineUsersResource::collection(($users)),
                            'request success',
                            200
                );
    }
    
}
