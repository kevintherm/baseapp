<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TemplateResource\Pages\PageEditor;
use App\Filament\Resources\TemplateResource\RelationManagers\PagesRelationManager;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Tables;
use App\Models\Template;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns\CheckboxColumn;
use App\Filament\Resources\TemplateResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TemplateResource\RelationManagers;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static ?string $navigationGroup = 'Other';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->disabled(true),

                Checkbox::make('is_active')
                    ->disabled(true)

            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->formatStateUsing(fn($state) => ucwords($state))
                    ->searchable(),

                CheckboxColumn::make('is_active')
                    ->label('Active')
                    ->disabled(fn($record) => $record->is_active)
            ])
            ->filters([
                TernaryFilter::make('is_active')
            ])
            ->actions([
                Tables\Actions\EditAction::make()
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplates::route('/'),
            'edit' => Pages\EditTemplate::route('{record}/edit')
        ];
    }

    public static function getRelations(): array
    {
        return [
            PagesRelationManager::class
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
