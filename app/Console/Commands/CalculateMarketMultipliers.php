<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MarketMultiplierService;

class CalculateMarketMultipliers extends Command
{
    protected $signature = 'market:calculate-multipliers';
    protected $description = 'Recalculates target multipliers and applies hourly smoothing.';

    public function handle(MarketMultiplierService $service)
    {
        $this->info('Recalculating target multipliers...');
        $service->recalculateAllTargetMultipliers();
        
        $this->info('Applying hourly smoothing...');
        $service->applyHourlySmoothing();
        
        $this->info('Done!');
    }
}
