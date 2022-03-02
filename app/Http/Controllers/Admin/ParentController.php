<?php

namespace App\Http\Controllers\Admin;

use App\Models\Parents;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ParentResource;
use App\Http\Requests\StoreParentRequest;

class ParentController extends Controller
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
        $parents = Parents::paginate(50);

        return $this->success( ParentResource::collection(($parents)),
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
    public function store(StoreParentRequest $request)
    {
        $parent = Parents::create([
            'name'=> $request->name,
            'url'=> $request->url,
        ]);

        return $this->success( new ParentResource(($parent)),
                                'parent added successfully',
                                200
                            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Parents $parent)
    {
        return $this->success( new ParentResource(($parent)),
                                'parent fetch success',
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
    public function update(StoreParentRequest $request, Parents $parent)
    {
        $parent->update([
            'name'=> $request->name,
            'url'=> $request->url,
        ]);

        return $this->success( new ParentResource(($parent)),
                                'parent updated successfully',
                                200
                            );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Parents $parent)
    {
        $parent->delete();
        return $this->success( NULL,
                                'parent deleted',
                                200
                            );
    }
}
