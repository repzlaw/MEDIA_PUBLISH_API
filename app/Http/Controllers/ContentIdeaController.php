<?php

namespace App\Http\Controllers;

use App\Models\ContentIdea;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\UserContentIdea;
use App\Http\Requests\StoreContentIdeaRequest;

class ContentIdeaController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->type === 'Admin') {
            $ideas= ContentIdea::where('user_id',$user->id)->latest()->paginate(10);
        } else {
            $ideas= UserContentIdea::where('user_id',$user->id)->latest()->paginate(10);
        }

        return response()->json([
            'ideas'=>$ideas,
            'message'=>'request success',
            'status'=>'success',
            'code'=>200
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreContentIdeaRequest $request)
    {
        $user = $request->user();
        
        if ($user->type === 'Admin') {
            $idea = ContentIdea::create([
                'topic'=>$request->topic,
                'reference_url'=>$request->reference_url,
                'description'=>$request->description,
                'website_id'=>$request->website_id,
                'external_website_id'=>$request->external_website_id,
                'user_id'=>$user->id,
            ]);
        } else {
            $idea = UserContentIdea::create([
                'topic'=>$request->topic,
                'reference_url'=>$request->reference_url,
                'description'=>$request->description,
                'website_id'=>$request->website_id,
                'external_website_id'=>$request->external_website_id,
                'user_id'=>$user->id,
            ]);
        }
        
        return response()->json([
            'idea'=>$idea,
            'message'=>'idea saved successfully',
            'status'=>'success',
            'code'=>200
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        
        if ($user->type === 'Admin') {
            $idea = ContentIdea::findOrFail($request->idea_id);
            $idea->update([
                'topic'=>$request->topic,
                'reference_url'=>$request->reference_url,
                'description'=>$request->description,
                'website_id'=>$request->website_id,
                'external_website_id'=>$request->external_website_id,
                'user_id'=>$user->id,
            ]);
        } else {
            $idea = UserContentIdea::findOrFail($request->idea_id);
            $idea->update([
                'topic'=>$request->topic,
                'reference_url'=>$request->reference_url,
                'description'=>$request->description,
                'website_id'=>$request->website_id,
                'external_website_id'=>$request->external_website_id,
                'user_id'=>$user->id,
            ]);
        }
        return response()->json([
            'idea'=>$idea,
            'message'=>'idea updated successfully',
            'status'=>'success',
            'code'=>200
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
            if ($request->user()->type ==='Admin') {
                $idea = ContentIdea::findOrFail($id);
                if ($idea->user_id === $request->user()->id) 
                    $idea->delete();
            }else{
                $idea = UserContentIdea::findOrFail($id);
                if ($idea->user_id === $request->user()->id) 
                    $idea->delete();
            }
            return $this->success( NULL,
                                'Idea deleted',
                                200
                            );
    
    }
    /**
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function share(Request $request)
    {
        $idea = UserContentIdea::findOrFail($request->idea_id);
        $idea->update([
            'status'=>'Shared',
            'shared_user_id'=>$request->assigned_to
        ]);
        $this->NotificationService->create(
            'A content idea been shared by  '.' '.$request->user()->name,
            'UserContentIdea',
            $idea->id,
            'content-ideas/'.$idea->id,
            $request->assigned_to,
        );

        return $this->success( NULL,
                                'Idea shared',
                                200
                            );
    }

    public function sharedIdeas(Request $request)
    {
        $ideas = UserContentIdea::where(['status'=>'Shared',
                    'shared_user_id'=>$request->user()->id])->orderBy('created_at','desc')->paginate(10);

        return response()->json([
                        'ideas'=>$ideas,
                        'message'=>'request success',
                        'status'=>'success',
                        'code'=>200
                    ]);
    }
}
