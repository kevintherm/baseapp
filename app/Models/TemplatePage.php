<?php

namespace App\Models;

use App\Models\Template;
use Illuminate\Database\Eloquent\Model;

class TemplatePage extends Model
{
    protected $guarded = ['id'];

    public function template()
    {
        return $this->belongsTo(Template::class);
    }
}
