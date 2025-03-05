<?php

namespace App\Filament\Resources\PostCommentResource\RelationManagers;

use App\Filament\Resources\PostCommentResource;
use App\Models\PostComment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ParentRelationManager extends RelationManager
{
    protected static string $relationship = 'parent';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('content')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                Tables\Columns\TextColumn::make('content'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->url(fn (PostComment $record): string => PostCommentResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
               //
            ])
            ->emptyStateHeading( 'No parent comment found.')
            ->emptyStateDescription('This is the first comment in this thread.');
    }
}
