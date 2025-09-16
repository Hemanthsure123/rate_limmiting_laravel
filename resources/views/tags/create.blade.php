@extends('layouts.app') {{-- Assume you have a basic layout; if not, create one below --}}

@section('content')
<div class="container">
    <h1>Create Tag</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form method="POST" action="{{ route('tags.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Tag Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
            @error('name')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Create Tag</button>
    </form>
    <a href="{{ route('blogs.index') }}" class="btn btn-secondary mt-2">View Blogs</a>
</div>
@endsection