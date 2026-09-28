<?php

namespace App\Filament\Resources\CastingResource\Pages;

use App\Filament\Resources\CastingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCasting extends EditRecord
{
    protected static string $resource = CastingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
