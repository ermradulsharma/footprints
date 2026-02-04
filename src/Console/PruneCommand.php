<?php

namespace Ermradulsharma\Footprints\Console;

use Illuminate\Console\Command;
use Ermradulsharma\Footprints\Visit;

class PruneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'footprints:prune {--days= : The number of days to retain unassigned Footprints data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale (ie unassigned) entries from the Footprints database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) ($this->option('days') ?? config('footprints.attribution_duration') / (60 * 60 * 24));

        Visit::query()->prunable($days)->delete();

        $this->info("Successfully pruned unassigned footprints older than {$days} days.");

        return self::SUCCESS;
    }
}
