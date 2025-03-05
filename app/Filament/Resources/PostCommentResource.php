<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostCommentResource\Pages;
use App\Filament\Resources\PostCommentResource\RelationManagers;
use App\Filament\Resources\PostCommentResource\RelationManagers\ParentRelationManager;
use App\Filament\Resources\PostCommentResource\RelationManagers\PostRelationManager;
use App\Filament\Resources\PostCommentResource\RelationManagers\UserRelationManager;
use App\Models\PostComment;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PostCommentResource extends Resource
{
    protected static ?string $model = PostComment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $label = "Comments";

    protected static ?string $navigationGroup = "Blog";

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
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
                    ->action(function($record, $column) {
                        $name = $column->getName();
                        $record->update([
                            $name => !$record->$name
                        ]);
                    }),
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'post_comments' . '_' . Carbon::now()))
                            ->withColumns([
                                Column::make('id'),
                                Column::make('user.email'),
                                Column::make('content')->width(60),
                                Column::make('approved')->formatStateUsing(fn($state) => $state ? 'Yes' : 'No'),
                                Column::make('parent.content')->heading('Replying To')->width(60),
                                Column::make('parent.id')->heading('Replying To (id)'),
                                Column::make('updated_at')->heading('Last updated'),
                                Column::make('created_at')->heading('Creation date')
                            ])
                    ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('Approve')
                        ->icon('heroicon-o-check-badge')
                        ->color(\Filament\Support\Colors\Color::Lime)
                        ->action(fn($records) => $records->each->approve())
                        ->requiresConfirmation(),
                    BulkAction::make('Unapprove')
                        ->icon('heroicon-o-percent-badge')
                        ->color(\Filament\Support\Colors\Color::Gray)
                        ->action(fn($records) => $records->each->unapprove())
                        ->requiresConfirmation(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'post_comments' . '_' . Carbon::now()))
                                ->withColumns([
                                    Column::make('id'),
                                    Column::make('user.email'),
                                    Column::make('content')->width(60),
                                    Column::make('approved')->formatStateUsing(fn($state) => $state ? 'Yes' : 'No'),
                                    Column::make('parent.content')->heading('Replying To')->width(60),
                                    Column::make('parent.id')->heading('Replying To (id)'),
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
            UserRelationManager::class,
            PostRelationManager::class,
            ParentRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPostComments::route('/'),
            'create' => Pages\CreatePostComment::route('/create'),
            'edit' => Pages\EditPostComment::route('/{record}/edit'),
        ];
    }
}
