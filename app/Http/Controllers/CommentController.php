<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'comments_content' => 'required|string',
        ]);

        $request['user_id'] = auth()->id();
        $comment = Comment::create($request->all());

        return new CommentResource($comment->loadMissing(['commentator:id,username']), 201);
    }

    // duplicate method example
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'post_id' => 'required|exists:posts,id',
    //         'comment_content' => 'required|string',
    //     ]);

    //     $comment = new \App\Models\Comment();
    //     $comment->post_id = $request->post_id;
    //     $comment->user_id = auth()->id();
    //     $comment->content = $request->content;
    //     $comment->save();

    //     return response()->json($comment, 201);
    // }

    public function update(Request $request, $id)
    {
        // $request->validate([
        //     'comments_content' => 'required|string',
        // ]);

        // $comment = Comment::findOrFail($id);
        // $comment->update($request->only('comments_content'));

        // return new CommentResource($comment->loadMissing(['commentator:id,username']));
    }
}
