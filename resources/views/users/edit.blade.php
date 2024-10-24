<!-- resources/views/users/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h5>Edit User</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password (leave blank to keep current password):</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update</button>
            </form>
            <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Back to Users</a>
        </div>
    </div>
</div>
@endsection
