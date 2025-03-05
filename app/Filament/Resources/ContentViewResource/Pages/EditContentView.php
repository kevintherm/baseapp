<?php

namespace App\Filament\Resources\ContentViewResource\Pages;

use App\Filament\Resources\ContentViewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentView extends EditRecord
{
    protected static string $resource = ContentViewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
