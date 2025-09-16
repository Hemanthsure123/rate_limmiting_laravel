<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Many-to-many relationship: A blog belongs to many tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}