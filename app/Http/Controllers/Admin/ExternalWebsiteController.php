<?php

namespace App\Http\Controllers\Admin;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ExternalWebsite;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExternalWebsiteResource;
use App\Http\Requests\StoreExternalWebsiteRequest;

class ExternalWebsiteController extends Controller
{
    use ApiResponse;

    /**
     * Only auth for "admin" guard are allowed
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin')->except('index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $websites = ExternalWebsite::paginate(50);
        // where('network_id',NULL)->
        
        return $this->success( ExternalWebsiteResource::collection(($websites)),
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
    public function store(StoreExternalWebsiteRequest $request)
    {
        $website = ExternalWebsite::create($request->only('url','region_id','network_id'));

        return $this->success( new ExternalWebsiteResource(($website)),
                                'website added successfully',
                                200
                            );

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $website = ExternalWebsite::findOrFail($id);
        return $this->success( new ExternalWebsiteResource(($website)),
                                'website fetch success',
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
    public function update(StoreExternalWebsiteRequest $request, $id)
    {
        $website = ExternalWebsite::findOrFail($id);
        $website->update($request->only('url','region_id','network_id'));

        return $this->success( new ExternalWebsiteResource(($website)),
                                'website updated successfully',
                                200
                            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $website = ExternalWebsite::where('id',$id)->delete();
        return $this->success( NULL,
                                'website deleted',
                                200
                            );
    }
}
