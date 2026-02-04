<?php

namespace Ermradulsharma\Footprints\Middleware;

use Closure;

use Illuminate\Http\Request;
use Ermradulsharma\Footprints\TrackingFilterInterface;
use Ermradulsharma\Footprints\TrackingLoggerInterface;

class CaptureAttributionDataMiddleware
{
    /**
     * Create a new CaptureAttributionDataMiddleware instance.
     */
    public function __construct(
        protected TrackingFilterInterface $filter,
        protected TrackingLoggerInterface $logger
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->filter->shouldTrack($request)) {
            $request = $this->logger->track($request);
        }

        return $next($request);
    }
}
