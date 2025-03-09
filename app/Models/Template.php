<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'features' => 'json'
    ];

    public static function createDefault()
    {
        try {
            Template::create([
                'name' => 'default',
                'path' => 'default',
                'is_active' => true,
                'features' => ['home', 'about', 'contact']
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
                static::where('is_active', true)->whereNot('id', $model->id)->update(['is_active' => false]);
            }
        });
    }

    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    public function getView(string $view, string $subfolder = 'public')
    {
        $viewExists = Template::getActive()->routes;

        if ($viewExists && in_array($view, $viewExists)) {
            return "{$this->path}/$subfolder/{$view}";
        }

        return null;
    }

    public function hasFeature($feature)
    {
        return in_array($feature, $this->features);
    }

    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }
}
