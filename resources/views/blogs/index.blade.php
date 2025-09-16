@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Blogs</h1>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('blogs.search') }}" class="mb-3">
        <div class="input-group">
            <input type="text" class="form-control" name="search" placeholder="Search by tag name..." value="{{ request('search') }}">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
        @if(request('search'))
            <small>Showing results for: "{{ request('search') }}"</small>
        @endif
    </form>

    <!-- Blogs List -->
    @forelse($blogs as $blog)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $blog->name }}</h5>
                <p class="card-text">{{ $blog->description }}</p>
                <p><strong>Tags:</strong>
                    @forelse($blog->tags as $tag)
                        <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                    @empty
                        <span class="text-muted">No tags</span>
                    @endforelse
                </p>
            </div>
        </div>
    @empty
        <p>No blogs found{{ request('search') ? ' for this tag.' : '.' }}</p>
    @endforelse

    <a href="{{ route('blogs.create') }}" class="btn btn-primary">Create New Blog</a>
    <a href="{{ route('tags.create') }}" class="btn btn-secondary">Create Tag</a>
</div>
@endsection