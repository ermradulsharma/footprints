<div align="center">

# 👣 Footprints: Lifecycle Attribution
### *High-Precision User Tracking and UTM Mapping for Laravel 12+*

[![Latest Version](https://img.shields.io/badge/version-1.0.0-darkgreen.svg?style=for-the-badge)](https://packagist.org/packages/skywalker-labs/footprints)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x-red.svg?style=for-the-badge)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.4+-777bb4.svg?style=for-the-badge)](https://php.net)

---

**Footprints** tracks the journey of your users before they even register. It captures UTM parameters, referrers, and landing pages, providing an elite **Attribution Engine** to measure marketing ROI with absolute precision.

</div>

## 🔍 The Attribution Edge

- **Pre-Registration Memory:** Remembers where a user came from, even if they browse for days before signing up.
- **Queue-First Logging:** Tracking doesn't slow down your app; it uses high-speed Jobs for asynchronous persistence.
- **Deep Attribution:** Maps the entire lifecycle from first-click to final conversion.

---

## 🔥 Killer Features

### 1. Automated UTM Mapping
Captures `utm_source`, `utm_medium`, and `utm_campaign` automatically and attaches them to the user profile on signup.

### 2. Multi-Visit Analysis
Not just the last click. Footprints tracks the entire sequence of visits.

---

## ⚡ Stats at a Glance

| Feature | Google Analytics | Footprints Elite |
| :--- | :--- | :--- |
| **Data Ownership** | Google | **Your Database** |
| **Privacy** | Cookie Intrusive | **Server-Side Stealth** |
| **Integration** | External Script | **Native Laravel Link** |

---

## 🛠️ Usage (PHP 8.4+)

```php
// Track registration attribution automatically
class User extends Model implements TrackableInterface {
    use TrackRegistrationAttribution;
}
```

---

Created & Maintained by **Skywalker-Labs**.
