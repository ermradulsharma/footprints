<?php

namespace Ermradulsharma\Footprints;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class TrackingFilter implements TrackingFilterInterface
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * Determine whether or not the request should be tracked.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function shouldTrack(Request $request): bool
    {
        $this->request = $request;

        //Only track get requests
        if (! $this->request->isMethod('get')) {
            return false;
        }

        if ($this->disableOnAuthentication()) {
            return false;
        }

        if ($this->disableInternalLinks()) {
            return false;
        }

        if ($this->disabledLandingPages($this->captureLandingPage())) {
            return false;
        }

        if ($this->disableRobotsTracking()) {
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

    /**
     * @return bool
     */
    protected function disableInternalLinks(): bool
    {
        if (! config('footprints.disable_internal_links')) {
            return false;
        }

        $referer = $this->request->headers->get('referer');

        if (! $referer) {
            return false;
        }

        $parsedUrl = parse_url($referer);
        $referrer_domain = $parsedUrl['host'] ?? null;
        $request_domain = $this->request->getHost();

        if (! $referrer_domain) {
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
    protected function captureLandingPage(): string
    {
        return $this->request->path();
    }

    /**
     * @return bool
     */
    protected function disableRobotsTracking(): bool
    {
        if (! config('footprints.disable_robots_tracking')) {
            return false;
        }

        return (new CrawlerDetect)->isCrawler();
    }
}


