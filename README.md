# :feet: Footprints for (UTM and Referrer Tracking)

![Footprints for Laravel (UTM and Referrer Tracking)](readme-header.jpg)

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Total Downloads][ico-downloads]][link-downloads]

Footprints is a simple registration attribution tracking solution for Laravel 7.0 to 12.0+.

> “I know I waste half of my advertising dollars...I just wish I knew which half.” ~ _Henry Procter_.

By tracking where user signups (or any other kind of registrations) originate from, you can ensure that your marketing efforts are more focused. Footprints makes it easy to look back and see what led to a user signing up.

## Installation

You can install the package via composer:

```bash
composer require ermradulsharma/footprints
```

### Setup

1. **Publish the configuration and migrations:**

```bash
php artisan vendor:publish --provider="Ermradulsharma\Footprints\FootprintsServiceProvider"
```

2. **Run the migrations:**

```bash
php artisan migrate
```

3. **Register Middleware:**

#### Laravel 11 & 12 (Modern way)

Add the `CaptureAttributionDataMiddleware` in your `bootstrap/app.php` file:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append(\Ermradulsharma\Footprints\Middleware\CaptureAttributionDataMiddleware::class);
})
```

#### Laravel 10 and below (Legacy way)

Add the `CaptureAttributionDataMiddleware` to your middleware stack in `app/Http/Kernel.php`. It should be placed after `EncryptCookies`.

```php
// app/Http/Kernel.php

protected $middleware = [
    // ...
    \App\Http\Middleware\EncryptCookies::class,
    \Ermradulsharma\Footprints\Middleware\CaptureAttributionDataMiddleware::class,
];
```

4. **Prepare your Model:**

Add the `TrackableInterface` and use the `TrackRegistrationAttribution` trait in your trackable model (usually the `User` model).

```php
namespace App\Models;

use Ermradulsharma\Footprints\TrackableInterface;
use Ermradulsharma\Footprints\TrackRegistrationAttribution;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements TrackableInterface
{
    use TrackRegistrationAttribution;

    // ...
}
```

## Configuration

After publishing, the configuration file will be located at `config/footprints.php`. Below are the key settings you can customize:

| Key                       | Description                                                | Default             |
| ------------------------- | ---------------------------------------------------------- | ------------------- |
| `model`                   | The model to track attribution for.                        | `App\Models\User`   |
| `guard`                   | The authentication guard to use.                           | `web`               |
| `column_name`             | The foreign key column in the visits table.                | `user_id`           |
| `attribution_duration`    | How long (in seconds) an attribution should last.          | `2628000` (1 month) |
| `async`                   | Whether to process tracking asynchronously (using queues). | `false`             |
| `disable_robots_tracking` | Whether to ignore requests from search engine crawlers.    | `false`             |

### Blacklisting Routes

By default, all routes using the middleware are tracked. You can define a blacklist of paths that should be ignored (e.g., admin panels, webhooks):

```php
'landing_page_blacklist' => [
    'admin*',
    'webhooks/*',
],
```

### Uniqueness

If you want to track users across browsers or where cookies might be blocked, you can disable the `uniqueness` setting. Note that this might cause requests from different users on the same IP to be matched.

```php
'uniqueness' => false,
```

## Usage

### How does Footprints work?

Footprints tracks the UTM parameters and HTTP refererers from all requests to your application that are sent by un-authenticated users. Not sure what UTM parameters are? [Wikipedia](https://en.wikipedia.org/wiki/UTM_parameters) has you covered:

> UTM parameters (UTM) is a shortcut for Urchin Traffic Monitor. These text tags allow users to track and analyze traffic sources in analytical tools (e.g., Google Analytics). By adding UTM parameters to URLs, you can identify the source and campaigns that send traffic to your website. When a user clicks a referral link/ad or banner, these parameters are sent to Google Analytics, so you can see the effectiveness of each campaign in your reports.

#### There are 5 dimensions of UTM parameters

- **utm_source** = name of the source (usually the domain of source website)
- **utm_medium** = name of the medium; type of traffic (e.g., cpc = paid search, organic = organic search; referral = link from another website etc.)
- **utm_campaign** = name of the campaign, e.g., name of the campaign in Google AdWords, date of your e-mail campaign, etc.
- **utm_content** = to distinguish different parts of one campaign; e.g., name of AdGroup in Google AdWords
- **utm_term** = to distinguish different parts of one content; e.g., keyword in Google AdWords

For a more technical explanation of the flow, please consult the section [Tracking process in details](#tracking-process-in-details) below.

### What data is tracked for each visit?

The default configuration tracks the most relevant information

- `landing_page`
- `referrer_url`
- `referrer_domain`
- `utm_source`
- `utm_campaign`
- `utm_medium`
- `utm_term`
- `utm_content`
- `created_at` (date of visit)

But the package also makes it easy to the users ip address or basically any information available from the request object.

##### Get all of a user's visits before registering.

```php
$user = User::find(1);
$user->visits;
```

#### Get the attribution data of a user's initial visit before registering

```php
$user = User::find(1);
$user->initialAttributionData();
```

#### Get the attribution data of a user's final visit before registering

```php
$user = User::find(1);
$user->finalAttributionData();
```

### Events

The `TrackingLogger` emits an event `RegistrationTracked` once a registration has been processed while it is possible to listen for any visits tracked by simply listening for the [Eloquent Events](https://laravel.com/docs/eloquent#events) on the `Visit` model.

### Tracking process in details

First off the `CaptureAttributionDataMiddleware` can be registered globally or on a selected list of routes.

Whenever an incoming request passes through the `CaptureAttributionDataMiddleware` middleware then it checks whether or not the request should be tracked using the class `TrackingFilter` (can be changed to any class implementing the `TrackingFilterInterface`) and if the request should be logged `TrackingLogger` will do so (can be changed to any class implementing `TrackingLoggerInterface`).

The `TrackingLogger` is responsible for logging relevant information about the request as a `Vist` record. The most important parameter is the request's "footprint" which is the entity that _should_ be the same for multiple requests performed by the same user and hence this is what is used to link different requests.

Calculating the footprint is done with a request macro which in turn uses a `Footprinter` singleton (can be changed to any class implementing `FootprinterInterface`). It will look for the presence of a `footprints` cookie (configurable) and use that if it exists. If the cookie does not exist then it will create it so that it can be tracked on subsequent requests. It might be desireable for some to implement a custom logic for this but note that it is important that the calculation is a _pure function_ meaning that calling this method multiple times with the same request as input should always yield the same result.

At some point the user signs up (or _any_ trackable model is created) which fires the job `AssignPreviousVisits`. This job calculates the footprint of the request and looks for any existing logged `Visit` records and link those to the new user.

### Keeping the footprints table light

#### Prune the table

Without pruning, the `visits` table can accumulate records very quickly. To mitigate this, you should schedule the `footprints:prune` Artisan command to run daily:

```php
$schedule->command('footprints:prune')->daily();
```

Beyond pruning, unassigned entries older than the duration set in `attribution_duration` are removed.

```php
$schedule->command('footprints:prune --days=10')->daily();
```

=======

#### Disable robots tracking

> Before disabling robots tracking, you will need to install `jaybizzle/crawler-detect`. To do so : `composer require jaybizzle/crawler-detect`

Your table can get pretty big fast, mostly because of robots (Google, Bing, etc.). To disable robots tracking, change your `footprints.php` file on `config` folder accordingly :

```php
'disable_robots_tracking' => true
```

## Upgrading

### 2.x => 3.x

Version 3.x of this package contains a few breaking changes that must be addressed if upgrading from earlier versions.

- Rename the `cookie_token` column to `footprint`, in the table configured in `config('footprints.table_name')`
- Add field `ip`' as a `nullable` `string` to the configured footprints table
- Implement `TrackableInterface` on any models where the tracking should be tracked (usually the Eloquent model `User`)
- (optional | recommended) Publish the updated configuration file: `php artisan vendor:publish --provider="Ermradulsharma\Footprints\FootprintsServiceProvider" --tag=config --force`
- If any modifications have been made to `TrackRegistrationAttribution` please consult the updated version to ensure proper compatability

## Change log

Please see the commit history for more information what has changed recently.

## Testing

Haven't got round to this yet - PR's welcome ;)

```bash
composer test
```

## Contributing

If you run into any issues, have suggestions or would like to expand this packages functionality, please open an issue or a pull request :)

## Thanks

Thanks to ZenCast, some of the [best Podcast Hosting](https://zencast.fm?ref=footprints-github) around.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/ermradulsharma/footprints.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ermradulsharma/footprints/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ermradulsharma/footprints.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/ermradulsharma/footprints
[link-travis]: https://travis-ci.org/ermradulsharma/footprints
[link-downloads]: https://packagist.org/packages/ermradulsharma/footprints
