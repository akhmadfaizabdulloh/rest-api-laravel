<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        // $posts = Post::with('writer:id,username')->get();

        // return response()->json(['data' => $posts]);
        // return PostResource::collection($posts);
        return PostDetailResource::collection($posts->loadMissing('writer:id,username'));
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username')->findOrFail($id);
        // return response()->json(['data' => $post]);
        return new PostDetailResource($post);
    }

    // Eager Loading Example
    // public function show2($id)
    // {
    //     $post = Post::findOrFail($id);
    //     return new PostDetailResource($post);
    // }


    public function store(Request $request)
    {
        // return response()->json('oke bisa diakses method store');
        // dd(Auth::user());

        $request->validate([
            'title' => 'required|string|max:255',
            'news_content' => 'required|string',
        ]);

        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
        // return response()->json(['data' => $post], 201);
    }

    public function update(Request $request, $id)
    {

        // dd('ini method update');
        $post = Post::findOrFail($id);

        // Check if the authenticated user is the author of the post
        if ($post->author !== Auth::user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'news_content' => 'sometimes|required|string',
        ]);

        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }
}
