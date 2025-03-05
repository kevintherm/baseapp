<?php

namespace App\Filament\Widgets;

use App\Role;
use App\Models\Post;
use App\Models\User;
use App\Models\PostComment;

class StatsOverview extends BaseModelStats
{
    private function getUserStats(): array
    {
        return $this->getModelStats(
            User::class,
            [
                ['whereNotIn' => [
                    'column' => 'role',
                    'values' => [Role::Admin, Role::Editor]
                ]]
            ],
            empty($this->filters['interval']) ? '30 days' : $this->filters['interval'],
            'heroicon-o-users'
        );
    }

    private function getPostStats(): array
    {
        return $this->getModelStats(
            Post::class,
            [],
            empty($this->filters['interval']) ? '30 days' : $this->filters['interval'],
            'heroicon-o-document-text'
        );
    }

    private function getCommentsStats(): array
    {
        return $this->getModelStats(
            PostComment::class,
            [],
            empty($this->filters['interval']) ? '30 days' : $this->filters['interval'],
            'heroicon-o-chat-bubble-bottom-center-text'
        );
    }

    protected function getStatsData(): array
    {
        return [
            'users' => $this->getUserStats(),
            'posts' => $this->getPostStats(),
            'comments' => $this->getCommentsStats(),
        ];
    }
}
