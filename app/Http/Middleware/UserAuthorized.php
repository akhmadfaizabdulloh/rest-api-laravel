<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAuthorized
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // dd('ini middleware user authorized');

        $currentUser = Auth::user();
        // return response()->json($currentUser);

        $post = Post::findOrFail($request->id);
        // return response()->json($post->author);
        // return response()->json($currentUser->id);

        if($post->author !== $currentUser->id) {
            return response()->json(['message' => 'data not found'], 404);
            // return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
