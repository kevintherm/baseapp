<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'slug', 'category_id', 'content', 'image', 'published_at'];

    public function category()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function authors()
    {
        return $this->belongsToMany(User::class, 'post_user')->withTimestamps();
    }

    public function views()
    {
        return $this->morphMany(ContentView::class, 'viewable');
    }
}
