<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TradeResource\Pages;
use App\Models\Trade;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class TradeResource extends Resource
{
    protected static ?string $model = Trade::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereIn('status', ['proposed', 'accepted'])->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pihak Trade')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('initiator_id')
                            ->relationship('initiator', 'name')
                            ->label('Initiator')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('receiver_id')
                            ->relationship('receiver', 'name')
                            ->label('Receiver')
                            ->required()
                            ->searchable(),
                    ]),

                Forms\Components\Section::make('Status & Tracking')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'proposed' => 'Proposed',
                                'accepted' => 'Accepted',
                                'shipped' => 'Shipped',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('accepted_initiator')
                            ->label('Initiator Accept'),
                        Forms\Components\Toggle::make('accepted_receiver')
                            ->label('Receiver Accept'),
                        Forms\Components\TextInput::make('tracking_initiator')
                            ->label('Resi Initiator')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('tracking_receiver')
                            ->label('Resi Receiver')
                            ->maxLength(100),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('initiator.name')
                    ->label('Initiator')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receiver.name')
                    ->label('Receiver')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'proposed' => 'warning',
                        'accepted' => 'info',
                        'shipped' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('accepted_initiator')
                    ->label('Init ✓')
                    ->boolean(),
                Tables\Columns\IconColumn::make('accepted_receiver')
                    ->label('Recv ✓')
                    ->boolean(),
                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'proposed' => 'Proposed',
                        'accepted' => 'Accepted',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('force_cancel')
                    ->label('Force Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Trade $record) => ! in_array($record->status, ['completed', 'cancelled']))
                    ->action(function (Trade $record) {
                        $record->update([
                            'status' => 'cancelled',
                            'accepted_initiator' => false,
                            'accepted_receiver' => false,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrades::route('/'),
            'create' => Pages\CreateTrade::route('/create'),
            'view' => Pages\ViewTrade::route('/{record}'),
            'edit' => Pages\EditTrade::route('/{record}/edit'),
        ];
    }
}
