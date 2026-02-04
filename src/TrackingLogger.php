<?php

namespace Ermradulsharma\Footprints;

use Illuminate\Http\Request;
use Ermradulsharma\Footprints\Jobs\TrackVisit;
use Illuminate\Support\Facades\Auth;

class TrackingLogger implements TrackingLoggerInterface
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new TrackingLogger instance.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Track the request.
     */
    public function track(Request $request): Request
    {
        $job = new TrackVisit($this->captureAttributionData(), Auth::user() ? Auth::user()->id : null);

        if (config('footprints.async') == true) {
            dispatch($job);
        } else {
            $job->handle(); // @phpstan-ignore-line
        }

        return $request;
    }

    /**
     * @return array
     */
    protected function captureAttributionData(): array
    {
        $attributes = array_merge(
            [
                'footprint'         => $this->request->footprint(),
                'ip'                => $this->captureIp(),
                'landing_domain'    => $this->captureLandingDomain(),
                'landing_page'      => $this->captureLandingPage(),
                'landing_params'    => $this->captureLandingParams(),
                'referral'          => $this->captureReferral(),
                'gclid'             => $this->captureGCLID(),
            ],
            $this->captureUTM(),
            $this->captureReferrer(),
            $this->getCustomParameter()
        );

        return array_map(function ($item) {
            return is_string($item) ? substr($item, 0, 255) : $item;
        }, $attributes);
    }

    /**
     * @return array
     */
    protected function getCustomParameter(): array
    {
        $arr = [];
        $parameters = config('footprints.custom_parameters');

        if ($parameters && is_array($parameters)) {
            foreach ($parameters as $parameter) {
                $arr[$parameter] = $this->request->input($parameter);
            }
        }

        return $arr;
    }

    /**
     * @return string|null
     */
    protected function captureIp(): ?string
    {
        if (! config('footprints.attribution_ip')) {
            return null;
        }

        return $this->request->ip();
    }

    /**
     * @return string
     */
    protected function captureLandingDomain(): string
    {
        return $this->request->getHost();
    }

    /**
     * @return string
     */
    protected function captureLandingPage(): string
    {
        return $this->request->path();
    }

    /**
     * @return string|null
     */
    protected function captureLandingParams(): ?string
    {
        return $this->request->getQueryString();
    }

    /**
     * @return array
     */
    protected function captureUTM(): array
    {
        $parameters = ['utm_source', 'utm_campaign', 'utm_medium', 'utm_term', 'utm_content'];

        $utm = [];

        foreach ($parameters as $parameter) {
            $utm[$parameter] = $this->request->input($parameter);
        }

        return $utm;
    }

    /**
     * @return array
     */
    protected function captureReferrer(): array
    {
        $referrer = [];

        $referrer['referrer_url'] = $this->request->headers->get('referer');

        if ($referrer['referrer_url']) {
            $parsedUrl = parse_url($referrer['referrer_url']);
            $referrer['referrer_domain'] = $parsedUrl['host'] ?? null;
        } else {
            $referrer['referrer_domain'] = null;
        }

        return $referrer;
    }

    /**
     * @return string|null
     */
    protected function captureGCLID(): ?string
    {
        return $this->request->input('gclid');
    }

    /**
     * @return string|null
     */
    protected function captureReferral(): ?string
    {
        return $this->request->input('ref');
    }
}
