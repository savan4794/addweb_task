@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Posts</h2>

    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('posts.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by title or content" value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="author" class="form-control">
                    <option value="">Select Author</option>
                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}" {{ request('author') == $author->id ? 'selected' : '' }}>{{ $author->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" id="start_date" name="start_date" class="form-control" placeholder="Start Date (dd-mm-yyyy)" value="{{ request('start_date') }}">
                    <input type="text" id="end_date" name="end_date" class="form-control" placeholder="End Date (dd-mm-yyyy)" value="{{ request('end_date') }}">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Filter</button>
        <a href="{{ route('posts.index') }}" class="btn btn-secondary mt-3">Reset</a>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Posts</span>
            <a href="{{ route('posts.create') }}" class="btn btn-success btn-sm">Create Post</a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Author</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td>{{ $post->title }}</td>
                            <td>{{ Str::limit($post->content, 50) }}</td>
                            <td>{{ $post->user->name }}</td> <!-- Assuming you have a relation to User -->
                            <td>{{ $post->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('posts.edit', $post->id) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('posts.destroy', $post->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No posts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination Links -->
            {{ $posts->links() }}
        </div>
    </div>

</div>
@endsection
@push('js')
<script>
    $(document).ready(function() {
        $('#start_date, #end_date').datepicker({
            format: 'dd-mm-dd', // Set the desired date format
            autoclose: true, // Close the datepicker after selecting a date
            todayHighlight: true // Highlight today's date
        });
    });
</script>
</script>
@endpush
