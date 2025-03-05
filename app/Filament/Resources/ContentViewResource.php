<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentViewResource\Pages;
use App\Filament\Resources\UserResource;
use App\Models\ContentView;
use Carbon\Carbon;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class ContentViewResource extends Resource
{
    protected static ?string $model = ContentView::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationGroup = 'User';

    protected static ?string $label = 'Views';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) =>  $record->user ? UserResource::getUrl('edit', ['record' => $record->user]) : '#'),

                TextColumn::make('count')
                    ->sortable(),

                TextColumn::make('viewable_type')
                    ->label('Content')
                    ->formatStateUsing(fn($state, $record) => $record->viewable?->title ?? $record->viewable?->name)
                    ->url(function ($record, $state){
                        $resourceClass = "\App\Filament\Resources\\" . ucfirst(str_replace('App\Models\\', '', $state)) . 'Resource';
                        return $resourceClass::getUrl('edit', ['record' => $record->viewable]);
                    }),

                TextColumn::make('watchtime')->label('Watchtime (minutes)')
                    ->sortable()
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'posts' . '_' . Carbon::now()))
                            ->withColumns([
                                Column::make('id'),
                                Column::make('user.name')->heading('User'),
                                Column::make('user.email')->heading('User email'),
                                Column::make('viewable_type')->heading('Content type')->formatStateUsing(fn($state) => str_replace('App\Models\\', '', $state)),
                                Column::make('viewable')->heading('Content title')->formatStateUsing(fn($record) => $record->viewable?->title ?? $record->viewable?->name),
                                Column::make('count'),
                                Column::make('watchtime')->heading('Watchtime (minutes)'),
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
                                    Column::make('user.name')->heading('User'),
                                    Column::make('user.email')->heading('User email'),
                                    Column::make('viewable_type')->heading('Content type')->formatStateUsing(fn($state) => str_replace('App\Models\\', '', $state)),
                                    Column::make('viewable')->heading('Content title')->formatStateUsing(fn($record) => $record->viewable?->title ?? $record->viewable?->name),
                                    Column::make('count'),
                                    Column::make('watchtime')->heading('Watchtime (minutes)'),
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
            'index' => Pages\ListContentViews::route('/'),
        ];
    }
}
