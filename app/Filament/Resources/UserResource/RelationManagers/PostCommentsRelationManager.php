<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PostComment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PostCommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'post_comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('post_id')
                    ->relationship('post', 'title')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->live(true),

                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->native(false)
                    ->searchable()
                    ->preload(),

                Select::make('parent_id')
                    ->label('Replying To')
                    ->relationship(
                        'parent',
                        'content',
                        modifyQueryUsing: fn (Builder $query, $record) =>
                            $query->when($record, fn($q) => $q->where('id', '!=', $record->id))
                    )
                    ->nullable()
                    ->columnSpanFull()
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->live(true)
                    ->afterStateUpdated(function ($set, $get, PostComment $postComment) {
                        $parentPostId = $postComment->find($get('parent_id'))->post_id;
                        $set('post_id', $parentPostId);
                    }),

                Checkbox::make('approved')
                    ->default(false),

                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->columns([
                TextColumn::make('content')
                ->label('Comment')
                ->searchable()
                ->sortable(),

            TextColumn::make('parent_id')
                ->label('Replying To')
                ->formatStateUsing(fn ($state, PostComment $postComment) => $postComment->parent?->content)
                ->limit(20)
                ->searchable()
                ->sortable(),

            BooleanColumn::make('approved')
                ->label('Approved')
                ->sortable(),
            ])
            ->filters([
                SelectFilter::make('approved')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->label('Approved'),
                TernaryFilter::make('approved')
                    ->label('Approved'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
