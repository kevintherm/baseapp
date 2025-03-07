<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public static function createDefault()
    {
        try {
            Template::create([
                'name' => 'default',
                'path' => Storage::disk('local')->path(config('themes.prefix') . '/default'),
                'is_active' => true,
            ]);

            return true;

        } catch(\Exception $e) {
            return false;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->is_active) {
                // Set all other rows' is_active to false
                static::where('is_active', true)->update(['is_active' => false]);
            }
        });
    }
}
