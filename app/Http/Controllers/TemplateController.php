<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Template;
use App\Role;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class TemplateController extends Controller
{
    protected $template;

    public function __construct()
    {
        $this->template = Template::where('is_active', true)->first();
    }

    public function pageEditor(Template $template)
    {
        return view('filament.pages.page-editor', [
            'template' => $template,
            'view' => Blade::render($template->getView('home'))
        ]);
    }

    public function savePageEditor(Request $request, Template $template)
    {
        $request->validate([
            'html' => 'required|string',
            'css' => 'nullable|string',
            'js' => 'nullable|string',
        ]);

        $html = $request->html;
        $css = $request->css;
        $js = $request->js;
        $pageTitle = Setting::retrieve('app_name', config('app.name'));

        $formattedHtml = "<!DOCTYPE html>
        <html lang=\"en\">
        <head>
            <meta charset=\"UTF-8\">
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
            <title>$pageTitle</title>
            <style>
            $css
            </style>
        </head>
        <body>
            $html
            <script>
            $js
            </script>
        </body>
        </html>";

        $filePath = $template->getView('home.blade.php');
        $filePath = "./../resources/views/$filePath";
        file_put_contents($filePath, $formattedHtml);

        return route('filament.admin.resources.templates.index');
    }

    public function routes($path)
    {
        $view = $this->template->getView($path);
        if (!$view) abort(404);

        return view($view);
    }
}
