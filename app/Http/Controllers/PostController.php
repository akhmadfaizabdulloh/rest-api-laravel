<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::all();
        // $posts = Post::with('writer:id,username')->get();

        // return response()->json(['data' => $posts]);
        // return PostResource::collection($posts);
        return PostDetailResource::collection($posts->loadMissing(['writer:id,username', 'comments:id,post_id,user_id,comments_content']));
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username')->findOrFail($id);
        // return response()->json(['data' => $post]);
        // return new PostDetailResource($post);
        return new PostDetailResource($post->loadMissing(['writer:id,username', 'comments:id,post_id,user_id,comments_content']));
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

        // return $request->file;

        $request->validate([
            'title' => 'required|string|max:255',
            'news_content' => 'required|string',
        ]);

        // AI recomendation
        // if ($request->hasFile('file')) {
        //     $file = $request->file('file');
        //     $filename = $this->generateRandomString(10) . '_' . time() . '.' . $file->getClientOriginalExtension();
        //     $path = $file->storeAs('public/uploads', $filename);
        //     $request['file'] = $filename;
        // }

        $image = null;
        if ($request->file) {
            // file upload here
            $fileName = $this->generateRandomString();
            $extension = $request->file->extension();
            $image = $fileName.'.' .$extension;

            Storage::putFileAs('image', $request->file, $image);
        }

        $request['image'] = $image;

        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
        // return response()->json(['data' => $post], 201);
    }

    // public function updateExample(Request $request, $id)
    // {

    //     // dd('ini method update');
    //     $post = Post::findOrFail($id);

    //     // Check if the authenticated user is the author of the post
    //     if ($post->author !== Auth::user()->id) {
    //         return response()->json(['message' => 'Unauthorized'], 403);
    //     }

    //     $request->validate([
    //         'title' => 'sometimes|required|string|max:255',
    //         'news_content' => 'sometimes|required|string',
    //     ]);

    //     $post->update($request->all());

    //     return new PostDetailResource($post->loadMissing('writer:id,username'));
    // }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'news_content' => 'required|string',
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        // return response()->json(['message' => 'Post deleted successfully']);
        // return new PostDetailResource($post->loadMissing('writer:id,username'));

        return response()->json([
            'message' => 'Post deleted successfully',
            'data' => (new PostDetailResource($post->loadMissing('writer:id,username')))->resolve()
        ], 200);

    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
