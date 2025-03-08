<?php

namespace App\Http\Controllers;

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

    public function home(): View
    {
        return view($this->template->getView('home'));
    }

    public function about()
    {

    }

    public function contact()
    {

    }
}
