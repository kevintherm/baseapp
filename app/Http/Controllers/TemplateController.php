<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Template;
use App\Models\TemplatePage;
use App\Role;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use View;

class TemplateController extends Controller
{
    protected $template;

    public function __construct()
    {
        $this->template = Template::where('is_active', true)->first();
    }

    public function pageEditor(Template $template, TemplatePage $page)
    {
        return view('filament.pages.page-editor', [
            'template' => $template,
            'page' => $page,
            'view' => $template->getHtml($page->name)
        ]);
    }

    public function savePageEditor(Request $request, Template $template, TemplatePage $page)
    {
        $request->validate([
            'html' => ['required', 'string'],  // Limit size
            'css' => ['nullable', 'string'],
            'js' => ['nullable', 'string'],
        ]);

        // Sanitize inputs
        $html = strip_tags($request->html, [
            'div', 'p', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'a', 'img', 'table', 'tr', 'td', 'th',
            'strong', 'em', 'b', 'i', 'br', 'blockquote', 'pre', 'code',
            'iframe', 'section', 'article', 'header', 'footer', 'nav',
            'form', 'input', 'textarea', 'button', 'select', 'option',
            'label', 'fieldset', 'legend', 'datalist', 'optgroup',
            'video', 'audio', 'source', 'track', 'canvas', 'svg', 'path',
            'g', 'line', 'rect', 'circle', 'ellipse', 'polygon', 'polyline',
            'text'
        ]);

        // Only allow safe CSS properties
        $css = preg_replace('/expression|import|behavior|javascript|eval/i', '', $request->css);

        // Basic JS validation - you may want to use a JS sanitizer library
        $js = preg_replace('/(eval|exec|system|passthru|shell_exec)/i', '', $request->js);

        $template->setHtml(
            page: $page->name,
            content: $html
        );

        $template->setCss(
            page: $page->name,
            content: $css
        );

        return route('filament.admin.resources.templates.index');
    }

    public function assetManager(Request $request)
    {
        if ($request->hasFile('files')) {
            $file = $request->file('files')[0];
            $path = $file->store('uploads', 'public');

            return response()->json([
                'data' => [
                    'src' => asset("storage/{$path}"),
                ],
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function routes($path)
    {
        $page = $this->template->hasPage($path);
        if (!$page) abort(404);

        return View::make(...$this->template->getViewData($path));
    }
}
