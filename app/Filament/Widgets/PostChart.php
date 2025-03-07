<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\PostComment;
use Filament\Support\Colors\Color;

class PostChart extends BaseModelChart
{
    protected static ?string $heading = 'Post & Comment Activity';

    protected function getQueries(): array
    {
        return [
            [
                'query' => Post::query(),
                'label' => 'Posts'
            ],
            [
                'query' => PostComment::query(),
                'label' => 'Comments'
            ],
        ];
    }

    protected function getColors(): array
    {
        return [
            [
                'border' => Color::Pink[500],
                'background' => Color::Pink[500],
                'point' => Color::Pink[500],
                'fill' => Color::Pink[500],
            ],
            [
                'border' => Color::Amber[500],
                'background' => Color::Amber[500],
                'point' => Color::Amber[500],
                'fill' => Color::Amber[500],
            ],
        ];
    }
}
