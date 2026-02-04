<?php

namespace Ermradulsharma\Footprints\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ermradulsharma\Footprints\Events\RegistrationTracked;
use Ermradulsharma\Footprints\TrackableInterface;
use Ermradulsharma\Footprints\Visit;

class AssignPreviousVisits implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $footprint,
        public TrackableInterface $trackable
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Visit::unassignedPreviousVisits($this->footprint)->update(
            [
                config('footprints.column_name') => $this->trackable->id,
            ]
        );

        event(new RegistrationTracked($this->trackable));
    }
}
