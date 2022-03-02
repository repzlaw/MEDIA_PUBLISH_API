<?php

namespace App\Http\Controllers\Admin;

use App\Models\Network;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\NetworkResource;
use App\Http\Requests\StoreNetworkRequest;

class NetworkController extends Controller
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
        $networks = Network::paginate(50);

        return $this->success( NetworkResource::collection(($networks)),
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
    public function store(StoreNetworkRequest $request)
    {
        $network = Network::create([
            'name'=> $request->name,
            'region_id'=> $request->region_id,
        ]);

        return $this->success( new NetworkResource(($network)),
                                'network added successfully',
                                200
                            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Network $network)
    {
        return $this->success( new NetworkResource(($network)),
                                'network fetch success',
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
    public function update(StoreNetworkRequest $request, Network $network)
    {
        $network->update([
            'name'=> $request->name,
            'region_id'=> $request->region_id,
        ]);

        return $this->success( new NetworkResource(($network)),
                                'network updated successfully',
                                200
                            );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Network $network)
    {
        $network->delete();
        return $this->success( NULL,
                                'network deleted',
                                200
                            );
    }
}
