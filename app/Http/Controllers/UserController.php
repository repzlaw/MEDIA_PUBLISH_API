<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponse;

    public function onlineStatus(Request $request)
    {
        $users = User::orderBy('last_seen', 'DESC')
                     ->paginate(30);
          
        return $this->success( UserResource::collection(($users)),
                            'request success',
                            200
                );
    }
    
}
