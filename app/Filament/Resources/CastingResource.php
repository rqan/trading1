<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CastingResource\Pages;
use App\Filament\Resources\CastingResource\RelationManagers;
use App\Models\Casting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CastingResource extends Resource
{
    protected static ?string $model = Casting::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('series'),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('rarity_tier')
                    ->options([
                        'mainline' => 'Mainline',
                        'th' => 'Treasure Hunt',
                        'sth' => 'Super Treasure Hunt',
                        'premium' => 'Premium',
                        'rlc' => 'RLC',
                    ])
                    ->required()
                    ->default('mainline'),
                Forms\Components\TextInput::make('base_value')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('series')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rarity_tier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('base_value')
                    ->numeric()
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
            'index' => Pages\ListCastings::route('/'),
            'create' => Pages\CreateCasting::route('/create'),
            'edit' => Pages\EditCasting::route('/{record}/edit'),
        ];
    }
}
