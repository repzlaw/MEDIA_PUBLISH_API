<?php

namespace App\Http\Controllers\Admin;

use App\Models\Website;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteResource;
use App\Http\Requests\StoreWebsiteRequest;

class WebsiteController extends Controller
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
        $websites = Website::paginate(50);
        // ::where('parent_id',NULL)->
        return $this->success( WebsiteResource::collection(($websites)),
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
    public function store(StoreWebsiteRequest $request)
    {
        $website = Website::create($request->only('website_code','url','region_id','parent_id'));

        return $this->success( new WebsiteResource(($website)),
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
        $website = Website::findOrFail($id);
        return $this->success( new WebsiteResource(($website)),
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
    public function update(StoreWebsiteRequest $request, $id)
    {
        $website = Website::findOrFail($id);

        $website->update($request->only('website_code','url','region_id','parent_id'));

        return $this->success( new WebsiteResource(($website)),
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
        $website = Website::where('id',$id)->delete();
        return $this->success( NULL,
                                'website deleted',
                                200
                            );
    }
}
