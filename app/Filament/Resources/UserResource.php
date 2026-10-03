<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('trust_score')
                    ->numeric()
                    ->default(100),
                Forms\Components\Select::make('platform_role')
                    ->options([
                        'superadmin' => 'Superadmin',
                        'moderator' => 'Moderator',
                        'member' => 'Member',
                    ])
                    ->required()
                    ->default('member'),
                Forms\Components\Toggle::make('is_banned')
                    ->required(),
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
                Tables\Columns\BadgeColumn::make('platform_role')
                    ->colors([
                        'danger' => 'superadmin',
                        'warning' => 'moderator',
                        'success' => 'member',
                    ]),
                Tables\Columns\TextColumn::make('trust_score')
                    ->numeric()
                    ->sortable()
                    ->color(fn (User $record): string => $record->trust_score < 60 ? 'danger' : 'success'),
                Tables\Columns\IconColumn::make('is_banned')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_banned'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('Ban')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => !$record->is_banned)
                    ->action(fn (User $record) => $record->update(['is_banned' => true])),
                Action::make('Unban')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record) => $record->is_banned)
                    ->action(fn (User $record) => $record->update(['is_banned' => false])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
