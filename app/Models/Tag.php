<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Many-to-many relationship: A tag belongs to many blogs
    public function blogs()
    {
        return $this->belongsToMany(Blog::class);
    }
}