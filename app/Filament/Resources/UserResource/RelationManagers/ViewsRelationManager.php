<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\UserResource;
use pxlrbt\FilamentExcel\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use Filament\Resources\RelationManagers\RelationManager;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ViewsRelationManager extends RelationManager
{
    protected static string $relationship = 'views';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->url(fn($record) => UserResource::getUrl('edit', ['record' => $record->user])),

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
                                Column::make('watchtime'),
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
}
