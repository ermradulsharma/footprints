<?php

namespace Ermradulsharma\Footprints\Tests\Unit\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Ermradulsharma\Footprints\Events\RegistrationTracked;
use Ermradulsharma\Footprints\Jobs\AssignPreviousVisits;
use Ermradulsharma\Footprints\Tests\TestCase;
use Ermradulsharma\Footprints\TrackableInterface;
use Mockery\MockInterface;

class AssignPreviousVisitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_emits_registration_tracked_event()
    {
        $trackable = $this->mock(TrackableInterface::class, function (MockInterface $mock) {
            $mock->id = 123;
        });

        Event::fake();

        $job = new AssignPreviousVisits('test-footprint', $trackable);
        $job->handle(); // We are not checking the "queue" part of the job, only that it does actually dispatch the event

        Event::assertDispatched(RegistrationTracked::class, function ($event) use ($trackable) {
            return $event->trackable === $trackable
                && $event->footprint = 'test-footprint';
        });
    }
}


