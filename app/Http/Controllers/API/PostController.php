<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use App\Notifications\PostPublished;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request)
    {
        // Base query to retrieve posts
        $query = Post::with('image');

        // Search functionality for title or content
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('content', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filtering by author
        if ($request->has('author') && $request->author) {
            $query->where('user_id', $request->author);
        }

        // Filtering by date range
        if ($request->has('start_date') && $request->has('end_date') && $request->start_date != null &&  $request->end_date != null) {
            $startDate = Carbon::createFromFormat('d-m-Y', $request->start_date)->startOfDay();
            $endDate = Carbon::createFromFormat('d-m-Y', $request->end_date)->endOfDay();
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Check if the user has the 'Author' role
        if (Auth::user()->hasRole('Author')) {
            $query->where('user_id', Auth::id());
        }

        // Paginate the results (10 posts per page)
        $posts = $query->paginate(10);

        // Get all authors for filtering
        $authors = User::all();

        // Return JSON response
        return response()->json([
            'posts' => $posts,
            'authors' => $authors,
        ]);
    }
    public function show($id){
        try {
            $post = Post::with('image')->findOrFail($id);

            return new PostResource($post);
        } catch (\Throwable $th) {
            return response()->json(['error'=>"post not found"],404);
        }

    }

    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new post
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = public_path('post_images');

            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            PostImage::create([
                'post_id' => $post->id,
                'image_path' => 'post_images/' . $imageName,
            ]);
        }

        // Notify users
        $users = User::all();
        foreach ($users as $user) {
            $user->notify(new PostPublished($post));
        }

        return new PostResource($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update post details
        if ($request->has('title')) {
            $post->title = $request->title;
        }

        if ($request->has('content')) {
            $post->content = $request->content;
        }

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            $postImage = $post->image;
            if ($postImage) {
                $oldImagePath = public_path($postImage->image_path);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Upload new image
            $imagePath = public_path('post_images');

            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);

            // Update or create post image
            if ($postImage) {
                $postImage->image_path = 'post_images/' . $imageName;
                $postImage->save();
            } else {
                PostImage::create([
                    'post_id' => $post->id,
                    'image_path' => 'post_images/' . $imageName,
                ]);
            }
        }

        $post->save();

        return new PostResource($post);
    }

    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json(null, 204);
    }

}
