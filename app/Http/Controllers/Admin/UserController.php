<?php

namespace App\Http\Controllers\Admin;

use HTMLPurifier;
use App\Models\User;
use HTMLPurifier_Config;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * Only auth for "admin" guard are allowed
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except('searchUser');
    }

    //users page
    public function index()
    {
        $users = User::paginate(50);

        return $this->success( UserResource::collection(($users)),
                                'request success',
                                200
                            );
    }

    //store user
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name'=> $request->name,
            'email'=> $request->email,
            'type'=> $request->type,
            'country'=> $request->country,
            'bank_details'=> $request->bank_details,
            'payout_per_word'=> $request->payout_per_word,
            'fixed_monthly_payout'=> $request->fixed_monthly_payout,
            'total_payout'=> $request->total_payout,
            'currency'=> $request->currency,
            'password'=> Hash::make($request->password),
        ]);

        return $this->success( new UserResource(($user)),
                                'user added successfully',
                                200
                            );
    }

    //single user
    public function show(User $user)
    {
        return $this->success( new UserResource(($user)),
                                'user fetch success',
                                200
                            );
    }

    //update users
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name'=> $request->name,
            'email'=> $request->email,
            'type'=> $request->type,
            'country'=> $request->country,
            'bank_details'=> $request->bank_details,
            'payout_per_word'=> $request->payout_per_word,
            'fixed_monthly_payout'=> $request->fixed_monthly_payout,
            'total_payout'=> $request->total_payout,
            'currency'=> $request->currency,
            'password'=> $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return $this->success( new UserResource(($user)),
                                        'user updated successfully',
                                        200
                            );

    }

    //search users
    public function searchUser(Request $request)
    {
        $searchData = $request->input('query');
        $searchColumn = $request->input('search_column');
        $users= '';
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $searchData = $purifier->purify($searchData);
        $searchColumn = $purifier->purify($searchColumn);

        if (!is_null($searchData)) {
            if ($searchColumn==='id') {
                $users = User::where('id', 'like', "%$searchData%")->paginate(50);
            }elseif ($searchColumn==='name') {
                $users = User::where('name', 'like', "%$searchData%")->paginate(50);
            }elseif ($searchColumn==='email') {
                $users = User::where('email', 'like', "%$searchData%")->paginate(50);
            }
        }

        return $this->success( UserResource::collection(($users)),
                                        'request successful',
                                        200
                            );

    }

    //delete user
    public function destroy(User $user)
    {
        $user->delete();
        return $this->success( NULL,
                                'user deleted',
                                200
                            );
    }

}
