<?php

namespace App\Models;

use App\Role;
use Filament\Panel;
use App\Models\ContentView;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class
        ];
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_user')->withTimestamps();
    }

    public function post_comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function views()
    {
        return $this->hasMany(ContentView::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === Role::Admin || $this->role === Role::Editor;
    }

}
