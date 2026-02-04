<?php

namespace Ermradulsharma\Footprints\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Ermradulsharma\Footprints\TrackableInterface;

class RegistrationTracked
{
    use Dispatchable, SerializesModels;

    /**
     * @var \Ermradulsharma\Footprints\TrackableInterface
     */
    public $trackable;

    /**
     * Create a new event instance.
     *
     * @param \Ermradulsharma\Footprints\TrackableInterface $trackable
     */
    public function __construct(TrackableInterface $trackable)
    {
        $this->trackable = $trackable;
    }
}
