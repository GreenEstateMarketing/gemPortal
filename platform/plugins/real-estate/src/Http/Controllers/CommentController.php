<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\RealEstate\Models\Comment;
use App\Http\Controllers\Controller;
use Botble\RealEstate\Models\Property;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $comment = new Comment;
        $comment->comment = $request->comment;
        $comment->user()->associate(auth('account')->user());

        $post = Property::find($request->property_id);

        $res=$post->comments()->save($comment);
        $res->agent_data=$res->user;
        $res->agent_url=$res->user->avatar_url;
        echo json_encode($res);
        //return back();
    }

    public function replyStore(Request $request)
    {
        $reply = new Comment();

        $reply->comment = $request->get('comment');

        $reply->user()->associate(auth('account')->user());

        $reply->parent_id = $request->get('comment_id');

        $post = Property::find($request->get('property_id'));

        $post->comments()->save($reply);

        return back();

    }
    public function adminStore(Request $request)
    {
        $comment = new Comment;

        $comment->comment = $request->comment;

        //$comment->user()->associate(auth()->id());
        $comment->admin_id=auth()->id();
        $post = Property::find($request->property_id);

        $res=$post->comments()->save($comment);
        $res->admin_data=$res->admin;
        $res->admin_url=$res->admin->avatar_url;
        echo json_encode($res);
    }
    public function adminReplyStore(Request $request)
    {
        $reply = new Comment();

        $reply->comment = $request->get('comment');

       // $reply->user()->associate(auth()->id());
        $reply->admin_id=auth()->id();
        $reply->parent_id = $request->get('comment_id');

        $post = Property::find($request->get('property_id'));

        $post->comments()->save($reply);

        return back();

    }

}
