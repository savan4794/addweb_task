@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Edit Post
        </div>
        <div class="card-body">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $post->title) }}" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="5" required>{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" class="form-control-file @error('image') is-invalid @enderror" id="image" name="image">
                    @error('image')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                    @if ($post->image)
                        <img src="{{ asset($post->image->image_path) }}" alt="Post Image" width="100" class="mt-2">
                    @endif
                </div>
                <div class="form-group mt-2">
                    <button type="submit" class="btn btn-primary">Update Post</button>
                    <a href="{{ route('posts.index') }}" class="btn btn-secondary">Back to Posts</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
