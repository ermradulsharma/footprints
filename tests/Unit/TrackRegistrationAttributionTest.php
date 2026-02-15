<?php

namespace Skywalker\Footprints\Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Skywalker\Footprints\Jobs\AssignPreviousVisits;
use Skywalker\Footprints\Tests\TestCase;
use Skywalker\Footprints\TrackableInterface;
use Skywalker\Footprints\TrackRegistrationAttribution;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;

class TrackRegistrationAttributionTest extends TestCase
{
    #[Test]
    public function test_dispatches_assign_previous_visits_job_when_configured_as_async()
    {
        Config::set('footprints.async', true);

        Bus::fake();

        $request = $this->mock(Request::class, function (MockInterface $mock) {
            $mock->shouldReceive('footprint')->andReturn('ABC123');
        });

        $trackable = new User;

        $trackable->trackRegistration($request);

        Bus::assertDispatched(AssignPreviousVisits::class, function ($job) use ($trackable) {
            return $job->footprint == 'ABC123' && $job->trackable == $trackable;
        });
    }

    #[Test]
    public function test_does_not_dispatch_assign_previous_visits_job_when_configured_as_sync()
    {
        Config::set('footprints.async', false);

        Bus::fake();

        (new User)->trackRegistration(new Request());

        Bus::assertNotDispatched(AssignPreviousVisits::class);
    }
}

class User implements TrackableInterface
{
    use TrackRegistrationAttribution;

    public $id = 123;
}
