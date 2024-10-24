<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Notifications\PostPublished;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

    if (Auth::user()->hasRole('Author')) {
        $query->where('user_id', Auth::id());
    }
    // Paginate the results (10 posts per page)
    $posts = $query->paginate(10);
    // Get all authors for filtering in the view
    $authors = User::all();

    return view('posts.index', compact('posts', 'authors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create a new post
        $post = Post::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => Auth::id(),
        ]);
        if ($request->hasFile('image')) {
            $imagePath = public_path('post_images'); // Change this to your desired public path

        // Create the directory if it does not exist
        if (!File::exists($imagePath)) {
            File::makeDirectory($imagePath, 0755, true);
        }

        // Get the uploaded file
        $image = $request->file('image');

        // Define a unique file name for the image
        $imageName = time() . '_' . $image->getClientOriginalName();

        // Move the uploaded file to the public directory
        $image->move($imagePath, $imageName);
            // $imagePath = $request->file('image')->store('post_images', 'public'); // Store image in public/storage/post_images
            PostImage::create([
                'post_id' => $post->id,
                'image_path' => 'post_images/' . $imageName,
            ]);
        }
        $users = User::all(); // Modify this to target specific users
        foreach ($users as $user) {
            $user->notify(new PostPublished($post));
        }
        return redirect()->route('posts.index')->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Find the post by its ID
        $post = Post::findOrFail($id);

        // Check permissions (similar to the destroy method)
        if (Auth::id() !== $post->user_id && !Auth::user()->hasRole('Admin')) {
            return redirect()->route('posts.index')->with('error', 'You do not have permission to edit this post.');
        }

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image type and size
        ]);

        $post = Post::findOrFail($id);

        if (Auth::id() !== $post->user_id && !Auth::user()->hasRole('Admin')) {
            return redirect()->route('posts.index')->with('error', 'You do not have permission to update this post.');
        }

        $post->title = $request->title;
        $post->content = $request->content;

        if ($request->hasFile('image')) {
            $postImage = $post->image;
            if ($postImage) {
                $imagePath = public_path($postImage->image_path);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
                $postImage->delete();
            }
            $imagePath = public_path('post_images');

            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($imagePath, $imageName);
            PostImage::create([
                'post_id' => $post->id,
                'image_path' => 'post_images/' . $imageName, // Store the relative path
            ]);
        }

        $post->save();

        return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
