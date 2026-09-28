<?php

namespace App\Filament\Resources\CastingResource\Pages;

use App\Filament\Resources\CastingResource;
use App\Filament\Imports\CastingImporter;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCastings extends ListRecords
{
    protected static string $resource = CastingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(CastingImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
