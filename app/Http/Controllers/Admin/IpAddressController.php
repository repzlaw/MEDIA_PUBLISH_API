<?php

namespace App\Http\Controllers\Admin;

use App\Models\IpAddress;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\IpAddressResource;
use App\Http\Requests\StoreIpAddressRequest;

class IpAddressController extends Controller
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
        $ipAddress = IpAddress::all();

        return $this->success( IpAddressResource::collection(($ipAddress)),
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
    public function store(StoreIpAddressRequest $request)
    {
        $ipAddress = IpAddress::create([
            'ip_address'=> $request->ip_address,
        ]);

        return $this->success( new IpAddressResource(($ipAddress)),
                                'ipAddress added successfully',
                                200
                            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(IpAddress $IpAddress)
    {
        return $this->success( new IpAddressResource(($IpAddress)),
                                'ipAddress fetch success',
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
    public function update(StoreIpAddressRequest $request, IpAddress $ipAddress)
    {
        $ipAddress->update([
            'ip_address'=> $request->ip_address,
        ]);

        return $this->success( new IpAddressResource(($ipAddress)),
                                'ipAddress updated successfully',
                                200
                            );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(IpAddress $ipAddress)
    {
        $ipAddress->delete();
        return $this->success( NULL,
                                'ipAddress deleted',
                                200
                            );
    }
}
