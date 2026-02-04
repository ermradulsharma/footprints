<?php

namespace Ermradulsharma\Footprints\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Ermradulsharma\Footprints\TrackableInterface;

class RegistrationTracked
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public TrackableInterface $trackable
    ) {}
}
