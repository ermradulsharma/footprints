<?php

namespace Skywalker\Footprints\Tests\Unit\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Skywalker\Footprints\Events\RegistrationTracked;
use Skywalker\Footprints\Jobs\AssignPreviousVisits;
use Skywalker\Footprints\Tests\TestCase;
use Skywalker\Footprints\TrackableInterface;
use Mockery\MockInterface;

class AssignPreviousVisitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_emits_registration_tracked_event()
    {
        /** @var TrackableInterface|\Mockery\MockInterface $trackable */
        $trackable = $this->mock(TrackableInterface::class, function (MockInterface $mock) {
            $mock->allows('getAttribute')->with('id')->andReturn(123);
            $mock->id = 123;
        });

        Event::fake();

        $job = new AssignPreviousVisits('test-footprint', $trackable);
        $job->handle(); // We are not checking the "queue" part of the job, only that it does actually dispatch the event

        Event::assertDispatched(RegistrationTracked::class, function ($event) use ($trackable) {
            return $event->trackable === $trackable;
        });
    }
}


