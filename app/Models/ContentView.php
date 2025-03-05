<?php

namespace App\Models;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentView extends Model
{
    protected $fillable = ['user_id', 'viewable_id', 'viewable_type', 'ip', 'count', 'watchtime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewable(): MorphTo
    {
        return $this->morphTo();
    }
}
