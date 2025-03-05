<?php

namespace App\Filament\Resources;

use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\PostCommentsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\PostsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\ViewsRelationManager;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                Select::make('role')
                    ->options(\App\Role::array())
                    ->required()
                    ->default(\App\Role::User->value)
                    ->native(false),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(fn ($state) => filled($state)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(function($state) {
                        return match ($state) {
                            \App\Role::Admin => 'primary',
                            \App\Role::User => 'gray',
                            \App\Role::Editor => 'info',
                            default => 'gray',
                        };
                    }),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('email_verified_at')
                    ->label('Email verification')
                    ->nullable()
                    ->placeholder('All users')
                    ->trueLabel('Verified users')
                    ->falseLabel('Not verified users'),
                SelectFilter::make('role')
                    ->options(\App\Role::array())
                    ->native(false)
                    ->multiple()
            ])
            ->headerActions([
                ImportAction::make()
                    ->importer(UserImporter::class)
                    ->icon('heroicon-o-arrow-down-on-square'),
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'users' . '_' . Carbon::now()))
                            ->withColumns([
                                Column::make('id'),
                                Column::make('name')->heading('Full Name'),
                                Column::make('email')->heading('Email Address'),
                                Column::make('role.value')->heading('Role'),
                                Column::make('email_verified_at')
                                    ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Not Verified')
                                    ->heading('Email Verified'),
                                Column::make('created_at')->heading('Creation date'),
                            ])
                    ]),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->withFilename(fn() => strtoupper(config('app.name') . '_' . 'export' . '_' . 'users' . '_' . Carbon::now()))
                                ->withColumns([
                                    Column::make('id'),
                                    Column::make('name')->heading('Full Name'),
                                    Column::make('email')->heading('Email Address'),
                                    Column::make('role.value')->heading('Role'),
                                    Column::make('email_verified_at')
                                        ->formatStateUsing(fn($state) => $state ? 'Verified' : 'Not Verified')
                                        ->heading('Email Verified'),
                                ])
                        ])
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            PostsRelationManager::class,
            PostCommentsRelationManager::class,
            ViewsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

}
