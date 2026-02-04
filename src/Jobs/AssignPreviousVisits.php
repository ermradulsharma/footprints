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
     * @var string
     */
    public $footprint;

    /**
     * @var \Ermradulsharma\Footprints\TrackableInterface
     */
    public $trackable;

    /**
     * Create a new job instance.
     *
     * @param string $footprint
     * @param \Ermradulsharma\Footprints\TrackableInterface $trackable
     */
    public function __construct($footprint, TrackableInterface $trackable)
    {
        $this->footprint = $footprint;
        $this->trackable = $trackable;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Visit::unassignedPreviousVisits($this->footprint)->update(
            [
                config('footprints.column_name') => $this->trackable->id,
            ]
        );

        event(new RegistrationTracked($this->trackable));
    }
}
