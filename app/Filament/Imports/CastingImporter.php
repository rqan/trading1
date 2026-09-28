<?php

namespace App\Filament\Imports;

use App\Models\Casting;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class CastingImporter extends Importer
{
    protected static ?string $model = Casting::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')->requiredMapping()->rules(['required', 'max:255']),
            ImportColumn::make('series')->rules(['max:255']),
            ImportColumn::make('year')->requiredMapping()->rules(['required', 'integer']),
            ImportColumn::make('rarity_tier')->rules(['in:mainline,th,sth,premium,rlc']),
            ImportColumn::make('base_value')->requiredMapping()->rules(['required', 'numeric']),
        ];
    }

    public function resolveRecord(): ?Casting
    {
        return Casting::firstOrNew([
            'name' => $this->data['name'],
            'year' => $this->data['year'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your casting import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
