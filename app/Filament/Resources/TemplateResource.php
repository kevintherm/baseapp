<?php

namespace App\Filament\Resources;

use Filament\Forms;
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
                FileUpload::make('image')
                    ->label('Thumbnail')
                    ->columnSpanFull(),
                TextInput::make('name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Thumbnail')
                    ->extraImgAttributes(fn (): array => [
                        'alt' => "Thumbnail not available",
                    ]),

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
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTemplates::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
