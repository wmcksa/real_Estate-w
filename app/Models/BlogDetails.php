<?php

namespace App\Models;

use App\Http\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogDetails extends Model
{
    use HasFactory, Translatable;

    protected $fillable = ['blog_id', 'language_id', 'author', 'title', 'details'];

    protected $casts = [
        'details' => 'object'
    ];

    public function blog(){
        return $this->belongsTo(Blog::class, 'blog_id');
    }

}
