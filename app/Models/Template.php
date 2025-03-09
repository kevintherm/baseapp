<?php

namespace App\Models;

use View;
use App\Models\TemplatePage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

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
            self::create([
                'name' => 'default',
                'path' => 'default',
                'is_active' => true,
                'features' => ['home', 'about', 'contact']
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->is_active) {
                // Ensure only one active template
                static::where('is_active', true)
                    ->where('id', '!=', $model->id)
                    ->update(['is_active' => false]);
            }
        });
    }

    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    public function getFileContent(string $page, string $view, string $extension, string $subfolder)
    {
        $fullPath = $this->getFullPath($page, $view, $extension, $subfolder);

        if (file_exists($fullPath)) {
            return file_get_contents($fullPath);
        }

        throw new FileNotFoundException("$fullPath is not found.");
    }

    public function setFileContent(string $page, string $view, string $content, string $extension, string $subfolder)
    {
        $fullPath = $this->getFullPath($page, $view, $extension, $subfolder);
        file_put_contents($fullPath, $content);
    }

    public function getHtml(string $page, string $view = 'index', string $subfolder = 'public')
    {
        return $this->getFileContent($page, $view, 'html', $subfolder);
    }

    public function getCss(string $page, string $view = 'style', string $subfolder = 'public')
    {
        return $this->getFileContent($page, $view, 'css', $subfolder);
    }

    public function getJs(string $page, string $view = 'script', string $subfolder = 'public')
    {
        return $this->getFileContent($page, $view, 'js', $subfolder);
    }

    public function setHtml(string $page, string $view = 'index', string $content = '', string $subfolder = 'public')
    {
        $this->setFileContent($page, $view, $content, 'html', $subfolder);
    }

    public function setCss(string $page, string $view = 'style', string $content = '', string $subfolder = 'public')
    {
        $this->setFileContent($page, $view, $content, 'css', $subfolder);
    }

    public function setJs(string $page, string $view = 'script', string $content = '', string $subfolder = 'public')
    {
        $this->setFileContent($page, $view, $content, 'js', $subfolder);
    }

    private function getFullPath(string $page, string $view, string $extension, string $subfolder)
    {
        return resource_path("views/{$this->path}/$subfolder/{$page}/{$view}.{$extension}");
    }

    public function isCompilable(string $page)
    {
        $htmlExists = file_exists($this->getFullPath($page, 'index', 'html', 'public'));
        $cssExists = file_exists($this->getFullPath($page, 'style', 'css', 'public'));
        $jsExists = file_exists($this->getFullPath($page, 'script', 'js', 'public'));

        return $htmlExists && $cssExists && $jsExists;
    }

    public function hasPage($page)
    {
        return in_array($page, $this->features ?? []) && $this->isCompilable($page);
    }

    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

    public function getViewData($path)
    {
        return ['default.layouts.app', [
            'slot' => $this->getHtml('home'),
            'css' => $this->getCss('home'),
            'js' => $this->getJs('home'),
            'title' => $path !== 'home' ? ucwords($path) : null,
        ]];
    }

    public function pages()
    {
        return $this->hasMany(TemplatePage::class);
    }
}
