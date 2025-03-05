<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Filters\MultiSelectFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            FileUpload::make('image')
                ->image()
                ->required()
                ->columnSpanFull()
                ->directory('posts'),

            Section::make('Post information')
                ->schema([
                    Select::make('category_id')
                        ->relationship('category', 'name')
                        ->required()
                        ->native(false)
                        ->preload()
                        ->createOptionForm([
                            TextInput::make('name')
                                ->label('Category Name')
                                ->required()
                                ->live(true)
                                ->afterStateUpdated(function($get, $set, PostCategory $postCategory) {
                                    $slug = str($get('name'))->slug();
                                    $iteration = 0;
                                    $exists = $postCategory->where('slug', 'LIKE', "$slug%")->count();
                                    $set('slug', $slug . ($exists ? '-' . ++$iteration : ''));
                                }),
                            TextInput::make('slug')
                                ->required()
                                ->unique(),
                        ]),
                    Select::make('Authors')
                        ->multiple()
                        ->relationship('authors', 'name')
                        ->preload()
                        ->required(),
                    TextInput::make('title')
                        ->required()
                        ->live(true)
                        ->afterStateUpdated(function($get, $set, Post $post) {
                            $slug = str($get('title'))->slug();
                            $iteration = 1;
                            $exists = $post->where('slug', 'LIKE', "$slug%")->count();
                            $set('slug', $slug . ($exists ? '-' . ++$iteration : ''));
                        }),
                    TextInput::make('slug')
                        ->required(),
                    DateTimePicker::make('published_at')
                        ->native(false)
                        ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible(),

           Section::make('Post Content')
                ->schema([
                    RichEditor::make('content')
                        ->required()
                        ->columnSpanFull(),
            ])
            ->columns(2)
            ->collapsible(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('image'),

                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('authors.name'),

                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                MultiSelectFilter::make('Authors')
                    ->relationship('authors', 'name')
                    ->searchable()
                    ->preload(),
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
