<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    // Show form to create blog (load all tags for checkboxes)
    public function create()
    {
        $tags = Tag::all();
        return view('blogs.create', compact('tags'));
    }

    // Store new blog with selected tags
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'tags' => 'array' // Array of tag IDs from checkboxes
        ]);

        $blog = Blog::create($request->only('name', 'description'));

        // Attach selected tags to blog
        if ($request->has('tags')) {
            $blog->tags()->attach($request->tags);
        }

        return redirect()->route('blogs.index')->with('success', 'Blog created!');
    }

    // List all blogs with their tags
    public function index()
    {
        $blogs = Blog::with('tags')->get(); // Eager load tags to avoid N+1 query
        return view('blogs.index', compact('blogs'));
    }

    // Search blogs by tag name
    public function search(Request $request)
    {
        $query = $request->input('search');
        $blogs = Blog::whereHas('tags', function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })->with('tags')->get();

        return view('blogs.index', compact('blogs'))->with('searchQuery', $query);
    }
}