<?php

namespace Skywalker\Footprints\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Skywalker\Footprints\Visit;

class TrackVisit implements ShouldQueue
{
    use Queueable;

    /**
     * @var array
     */
    protected $attributionData;

    /**
     * @var mixed
     */
    public $trackableId;

    /**
     * Create a new job instance.
     *
     * @param array $attributionData
     * @param mixed $trackableId
     */
    public function __construct(array $attributionData, $trackableId = null)
    {
        $this->attributionData = $attributionData;
        $this->trackableId = $trackableId;
    }

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


