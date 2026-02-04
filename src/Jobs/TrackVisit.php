<?php

namespace Ermradulsharma\Footprints\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Ermradulsharma\Footprints\Visit;

class TrackVisit implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $attributionData,
        public mixed $trackableId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Visit::query()->create(array_merge([
            config('footprints.column_name') => $this->trackableId,
        ], $this->attributionData));
    }
}
