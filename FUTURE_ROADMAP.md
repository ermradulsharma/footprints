# Future Roadmap: UTM-Footprints Package Enhancements

This document outlines the proposed features for the next major versions of the `ermradulsharma/footprints` package.

---

## v1.1.0 - Geo-Location Tracking

**Idea**: Integration with GeoIP libraries or APIs to resolve visitor IP addresses into specific geographic data such as City, Country, and ISP.
**Benefit**: Provides deep insights into the geographic distribution of converting users, allowing for better-targeted localized marketing campaigns.

---

## v1.2.0 - Browser & Device Stats

**Idea**: Capturing and storing metadata about the user's technology stack (e.g., Browser version like Chrome/Safari, and Device type like Mobile/Tablet/Desktop).
**Benefit**: Helps marketing and development teams understand whether to prioritize mobile-first ad spending or desktop-optimized landing pages.

---

## v1.3.0 - Visual Dashboard (Analytic UI)

**Idea**: Implementation of a dedicated Blade-based dashboard within the package. It will feature interactive charts (e.g., bar charts for top `utm_source` like Facebook, Google, etc.) to visualize attribution data.
**Benefit**: Users can instantly see which marketing channels are driving the most registrations without needing to write SQL queries or exported raw data.

---

## v2.0.0 - Multi-Touch Attribution Models

**Idea**: Moving beyond "First-Touch" and "Last-Touch" attribution. Implementing logic for models like **Linear Attribution** (equal credit to all visits) or **Time Decay** (more credit to visits closer to the signup).
**Benefit**: Professional marketers get a much more accurate picture of the complete customer journey, rather than just the beginning or end.

---

## v2.1.0 - Instant Notifications (Slack/Discord Webhooks)

**Idea**: A built-in notification system that triggers whenever a "high-value" registration occurs (e.g., from a specific UTM campaign like `black_friday`).
**Benefit**: Sales and growth teams can react instantly to new registrations coming from expensive or high-priority marketing campaigns.

---

## v2.2.0 - Export Feature (CSV/Excel)

**Idea**: A simple UI button or an Artisan command (`footprints:export`) to generate structured reports of all attribution data.
**Benefit**: Integration with external Excel models or third-party CRM tools becomes effortless for the marketing team.
