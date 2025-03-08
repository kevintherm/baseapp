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
            'app_name', 'app_description', 'app_logo', 'app_favicon'
        ];

        foreach ($attributes as $attribute) {
            if (empty($this->$attribute)) {
                return false;
            }
        }

        return true;
    }

        private static $instance;

        public static function getInstance()
        {
            if (is_null(self::$instance)) {
                self::$instance = self::first() ?: self::createDefault();
            }

            return self::$instance;
        }

        public static function retrieve(string $name, $fallback = null, bool $storage_path = false)
        {
            $st = self::getInstance();

            if (!empty($st->$name)) return $storage_path ? '/storage/' . $st->$name : $st->$name;

            return $fallback;
        }
}
