<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    // Show form to create tag
    public function create()
    {
        return view('tags.create');
    }

    // Store new tag
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags'
        ]);

        Tag::create($request->only('name'));

        return redirect()->route('tags.create')->with('success', 'Tag created!');
    }
}