<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\PostCategory;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = "Blog";

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->image()
                    ->required()
                    ->columnSpanFull()
                    ->directory('posts'),

                Section::make('Post Information')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image'),

                TextColumn::make('title')
                    ->searchable(),

                TextColumn::make('authors.name'),

                TextColumn::make('views_count')
                    ->counts('views')
                    ->icon('heroicon-o-eye')
                    ->sortable(),

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
                TernaryFilter::make('published_at')
                    ->label('Published')
                    ->nullable()
                    ->placeholder('All')
                    ->trueLabel('Yes')
                    ->falseLabel('No'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'posts' . '_' . Carbon::now()))
                            ->withColumns([
                                Column::make('id'),
                                Column::make('user.name'),
                                Column::make('title'),
                                Column::make('slug'),
                                Column::make('content')->formatStateUsing(fn($state) => strip_tags($state))->width(60),
                                Column::make('category.name')->heading('Category name'),
                                Column::make('published_at')->formatStateUsing(fn($state) => $state ?? 'Not Published'),
                                Column::make('updated_at')->heading('Last updated'),
                                Column::make('created_at')->heading('Creation date')
                            ])
                        ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'posts' . '_' . Carbon::now()))
                                ->withColumns([
                                    Column::make('id'),
                                    Column::make('user.name'),
                                    Column::make('title'),
                                    Column::make('slug'),
                                    Column::make('content')->formatStateUsing(fn($state) => strip_tags($state))->width(60),
                                    Column::make('category.name')->heading('Category name'),
                                    Column::make('published_at')->formatStateUsing(fn($state) => $state ?? 'Not Published'),
                                    Column::make('updated_at')->heading('Last updated'),
                                    Column::make('created_at')->heading('Creation date')
                                ])
                        ])
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
