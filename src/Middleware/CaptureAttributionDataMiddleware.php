<?php

namespace Ermradulsharma\Footprints\Middleware;

use Closure;

use Illuminate\Http\Request;
use Ermradulsharma\Footprints\TrackingFilterInterface;
use Ermradulsharma\Footprints\TrackingLoggerInterface;

class CaptureAttributionDataMiddleware
{
    protected TrackingFilterInterface $filter;

    protected TrackingLoggerInterface $logger;

    public function __construct(TrackingFilterInterface $filter, TrackingLoggerInterface $logger)
    {
        $this->filter = $filter;
        $this->logger = $logger;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->filter->shouldTrack($request)) {
            $request = $this->logger->track($request);
        }

        return $next($request);
    }
}


