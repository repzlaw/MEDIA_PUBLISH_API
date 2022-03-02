<?php

namespace App\Http\Controllers\Admin;

use App\Models\Link;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LinkResource;
use App\Http\Requests\StoreLinkRequest;
use App\Models\Task;

class LinkController extends Controller
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
        $links = Link::paginate(50);

        return $this->success( LinkResource::collection(($links)),
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
    public function store(StoreLinkRequest $request)
    {
        $link = Link::create($request->validated());

        return $this->success( new LinkResource(($link)),
                                'link added successfully',
                                200
                            );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Link $link)
    {
        return $this->success( new LinkResource(($link)),
                                'link fetch success',
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
    public function update(StoreLinkRequest $request, Link $link)
    {
        $link->update($request->validated());

        return $this->success( new LinkResource(($link)),
                                'link updated successfully',
                                200
                            );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Link $link)
    {
        $link->delete();
        return $this->success( NULL,
                                'link deleted',
                                200
                            );
    }

    //map link to task
    public function mapToTask(Request $request)
    {
        $task  = Task::findOrFail($request->task_id);
        $link  = Link::findOrFail($request->link_id);

        $task->update([
            'link_id'=>$request->link_id,
            'published_url'=>$request->url,
            'published_date'=>$request->published_date
        ]);
        
        return $this->success( new LinkResource(($link)),
                                'link mapped to task successfully',
                                200
                            );
    }
}
