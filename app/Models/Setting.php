<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'app_settings';

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // can only have 1 rows
            static::where('id', '!=', $model->id)->forceDelete();
        });
    }

    public static function createDefault()
    {
        return static::create([
            'app_name' => config('app.name'),
            'app_description' => '',
        ]);
    }

    public function getIsCompleteAttribute()
    {
        $attributes = [
            'app_name', 'app_description', 'app_logo'
        ];

        foreach ($attributes as $attribute) {
            if (empty($this->$attribute)) {
                return false;
            }
        }

        return true;
    }

    public static function retrieve(string $name, $fallback = null)
    {
        $st = self::first();

        if (!empty($st->$name)) return $st->$name;

        return $fallback;
    }
}
