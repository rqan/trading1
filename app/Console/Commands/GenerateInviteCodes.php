<?php

namespace App\Console\Commands;

use App\Models\InviteCode;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateInviteCodes extends Command
{
    protected $signature = 'wflf:generate-invites {count=10}';
    protected $description = 'Generate closed beta invite codes';

    public function handle()
    {
        $count = (int) $this->argument('count');

        for ($i = 0; $i < $count; $i++) {
            InviteCode::create([
                'code' => strtoupper(Str::random(8)),
            ]);
        }

        $this->info("Successfully generated {$count} invite codes.");
    }
}
