<?php

namespace App\Filament\Resources;

use App\Models\Post;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\PostCategory;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Resources\PostCategoryResource\Pages;
use App\Filament\Resources\PostCategoryResource\RelationManagers;
use App\Filament\Resources\PostCategoryResource\Pages\EditPostCategory;
use App\Filament\Resources\PostCategoryResource\Pages\CreatePostCategory;
use App\Filament\Resources\PostCategoryResource\Pages\ListPostCategories;
use App\Filament\Resources\PostCategoryResource\RelationManagers\PostsRelationManager;

class PostCategoryResource extends Resource
{
    protected static ?string $model = PostCategory::class;
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $label = "Categories";

    protected static ?string $navigationGroup = "Blog";

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),

                TextColumn::make('posts_count')
                    ->counts('posts')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'post_categories' . '_' . Carbon::now()))
                            ->withColumns([
                                Column::make('id'),
                                Column::make('name'),
                                Column::make('slug'),
                                Column::make('posts_count')->heading('Posts count')->getStateUsing(fn($record) => Post::where('category_id', $record->id)->count()),
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
                                ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'post_categories' . '_' . Carbon::now()))
                                ->withColumns([
                                    Column::make('id'),
                                    Column::make('name'),
                                    Column::make('slug'),
                                    Column::make('posts_count')->heading('Posts count')->getStateUsing(fn($record) => Post::where('category_id', $record->id)->count()),
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
            PostsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostCategories::route('/'),
            'create' => Pages\CreatePostCategory::route('/create'),
            'edit' => Pages\EditPostCategory::route('/{record}/edit'),
        ];
    }
}
