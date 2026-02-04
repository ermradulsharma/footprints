<?php

namespace Ermradulsharma\Footprints\Tests\Unit\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Ermradulsharma\Footprints\Middleware\CaptureAttributionDataMiddleware;
use Ermradulsharma\Footprints\Tests\TestCase;
use Ermradulsharma\Footprints\TrackingFilterInterface;
use Ermradulsharma\Footprints\TrackingLoggerInterface;


class CaptureAttributionDataMiddlewareTest extends TestCase
{
    public function test_logs_when_filter_returns_true()
    {
        $request = new Request;
        $response = new Response;

        $trackingFilter = \Mockery::mock(TrackingFilterInterface::class);
        $trackingFilter->shouldReceive('shouldTrack')
            ->once()
            ->andReturnTrue();

        $trackingLogger = \Mockery::mock(TrackingLoggerInterface::class);
        $trackingLogger->shouldReceive('track')
            ->with($request)
            ->andReturn($request);

        $middleware = new CaptureAttributionDataMiddleware($trackingFilter, $trackingLogger);

        $this->assertEquals($response, $middleware->handle($request, function ($req) use ($response) {
            return $response;
        }));
    }

    public function test_do_not_log_when_filter_returns_false()
    {
        $request = new Request;
        $response = new Response;

        $trackingFilter = \Mockery::mock(TrackingFilterInterface::class);
        $trackingFilter->shouldReceive('shouldTrack')
            ->once()
            ->andReturnFalse();

        $trackingLogger = \Mockery::mock(TrackingLoggerInterface::class);
        $trackingLogger->shouldNotReceive('track');

        $middleware = new CaptureAttributionDataMiddleware($trackingFilter, $trackingLogger);

        $this->assertEquals($response, $middleware->handle($request, function ($req) use ($response) {
            return $response;
        }));
    }
}


