<?php

namespace Ermradulsharma\Footprints;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class TrackingFilter implements TrackingFilterInterface
{
    /**
     * Create a new TrackingFilter instance.
     */
    public function __construct() {}

    /**
     * Determine whether or not the request should be tracked.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function shouldTrack(Request $request): bool
    {
        // Only track GET requests
        if (!$request->isMethod('get')) {
            return false;
        }

        if ($this->disableOnAuthentication()) {
            return false;
        }

        if ($this->disableInternalLinks($request)) {
            return false;
        }

        if ($this->disabledLandingPages($request->path())) {
            return false;
        }

        if ($this->disableRobotsTracking($request)) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function disableOnAuthentication(): bool
    {
        if (Auth::guard(config('footprints.guard'))->check() && config('footprints.disable_on_authentication')) {
            return true;
        }

        return false;
    }

    protected function disableInternalLinks(Request $request): bool
    {
        if (!config('footprints.disable_internal_links')) {
            return false;
        }

        $referer = (string) $request->headers->get('referer');

        if (!$referer) {
            return false;
        }

        $parsedUrl = parse_url($referer);
        $referrer_domain = $parsedUrl['host'] ?? null;
        $request_domain = $request->getHost();

        if (!$referrer_domain) {
            return false;
        }

        // Normalize domains for comparison (simple check)
        return strtolower($referrer_domain) === strtolower($request_domain);
    }

    /**
     *
     * @param   string|null  $landing_page
     * @return  bool|array
     */
    protected function disabledLandingPages(?string $landing_page = null)
    {
        $blacklist = (array) config('footprints.landing_page_blacklist');

        if ($landing_page) {
            return in_array($landing_page, $blacklist);
        } else {
            return $blacklist;
        }
    }

    /**
     * @return string
     */
    protected function captureLandingPage(Request $request): string
    {
        return $request->path();
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function disableRobotsTracking(Request $request): bool
    {
        if (! config('footprints.disable_robots_tracking')) {
            return false;
        }

        return (new CrawlerDetect())->isCrawler($request->header('User-Agent'));
    }
}
