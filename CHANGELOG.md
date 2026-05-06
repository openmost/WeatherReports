## Changelog

### v5.2.0

> ⚠️ **Action required after upgrading**
> 1. **Database migration** — Matomo will prompt you to upgrade the schema on activation. `weather_pressure` and `weather_visibility` widen `INT → FLOAT`; `weather_wind_direction` narrows `VARCHAR(255) → VARCHAR(8)`. Existing data is preserved (the 16-point compass is ≤3 chars).
> 2. **Republish your Matomo Tag Manager container.** The bundled `Weather` tag template (`WeatherTag.web.js`) was rewritten — published containers keep serving the old JS until you publish a new version.
> 3. **If you use the plain JS snippet** (no MTM), copy the updated snippet from `docs/index.md` so you also drop the third-party `ipapi.co` call.

**Privacy / data flow**
- remove: third-party `ipapi.co` IP-lookup. The Matomo Tag Manager template now uses WeatherAPI's built-in `q=auto:ip` so visitor IPs are no longer shared with a second service.
- add: public `WeatherReports&action=getUserIp` Controller endpoint as a self-hosted IP-resolution fallback for CDN/proxy setups (uses Matomo's `IP::getIpFromHeader()`, honours `proxy_client_headers`).
- harden: tag JS now bails out cleanly on missing API key, HTTP errors, malformed responses, and unavailable `sessionStorage`.

**Tracking & validation**
- refactor: `Columns/` now share a single `Base` class — ~1,500 lines of duplicated dimension boilerplate replaced by ~200 lines.
- fix: input is type-cast (`int`/`float`/`string`) at the tracker via `Common::getRequestVar` instead of being stored as raw strings.
- fix: out-of-range values are dropped at ingest:
  - Cloud / Humidity: 0-100
  - UV: 0-20
  - Temperature / Felt temperature: -100..200
  - Pressure: 0..2000 (covers both mb and inHg)
  - Visibility / Precipitation / WindSpeed: 0..1000
  - WindDirection: must match the 16-point compass (N, NNE, NE, ENE, …, NNW)
- fix: string columns (Condition, WindDirection) no longer write integer `0` when the request has no value — they store `null`.

**Schema**
- fix: `weather_pressure` is now `FLOAT NULL` (was `INT(10)`) so values in inHg keep precision.
- fix: `weather_visibility` is now `FLOAT NULL` (was `INT(10)`) so values in miles keep precision.
- fix: `weather_wind_direction` is now `VARCHAR(8) NULL` (was `VARCHAR(255)`) — the 16-point compass max is 3 chars.
- A standard Matomo schema migration prompts admins on next activation; existing data is preserved (INT→FLOAT widens losslessly).

**Reports**
- fix: scale reports (Temperature, Pressure, Humidity, …) sort numerically by label so chart x-axes read 1, 2, 10, 20 instead of 1, 10, 2, 20.
- fix: categorical reports (Condition, WindDirection) sort by visit count and no longer collapse all rows into a single `0` bucket from a stray `(float)` cast.
- add: categorical report tables show **top 15** rows + "Others" by default (was Matomo's 5).
- chore: dedupe the rounding closure in `API.php` — six methods now share one `getRoundedScaleDataTable()` helper.

**Visitor log**
- ui: redesigned weather panel as a themed card with a header line (`Weather · Condition` + temperature with felt-temp suffix) and a 2-column metric grid below.
- ui: units (`°C`/`°F`, `mm`/`in`, `mb`/`inHg`, `km`/`mi`, `km/h`/`mph`) are rendered next to each value, read from the existing per-site `MeasurableSettings` (those settings are now actually wired in).
- ui: light/dark theme aware — colors and surfaces use Matomo's CSS variables (`--theme-color-text`, `--theme-color-text-light`, `--theme-color-border`, `--theme-color-background-contrast`) with hex fallbacks. DarkTheme, GoogleTheme and Morpheus all match automatically.
- ui: rows with no recorded value are hidden (no more bare `°C` / `mb` / `%` suffixes); the whole card is skipped if a visit has no weather data at all.
- harden: `renderVisitorDetails` is wrapped in `try/catch` and logs to `tmp/logs/` instead of breaking the visitor log if rendering fails.

**Other**
- fix: removed broken `Live\Model::getCRMData` / `getAdviews` calls in `WeatherReports.php` (copy-paste leftovers from another plugin; methods don't exist).
- chore: deleted dead commented-out `MeasurableSettings::makeWeatherLangSetting` (~50 lines) and empty `config/config.php` / `config/tracker.php`.
- chore: dropped boilerplate example comments from every `Columns/*.php` file.

**Tests**
- add: 53 PHPUnit unit tests covering every column's bounds, type coercion, the 16-point compass validator, and Condition's UTF-8 truncation.
- add: `phpunit.xml` with `Unit` and `Integration` test suites.

**Docs**
- rewritten classic-snippet docs (no more ipapi.co), documented the optional `getUserIp` endpoint, refreshed the dimensions table to reflect the FLOAT migrations, added a privacy note.

### v5.1.2

fix: Archiving issues

### v5.1.1

fix: Sorting deprecated method replace with "label"

### v5.1.0

The great update you waited for!

support: Conversions reports
update: translations for DE, ES, IT, NL and SV
refactor: ReportsBuilders
enhanced bar charts visualisation

### v5.0.3

add: Cover for marketplace

### v5.0.2

update: Documentation URL

### v5.0.1

update: MeasurableSettings

### v5.0.0

Support Matomo v5

### v4.3.3

Update documentation FAQ

### v4.3.2

Update tag language selector

### v4.3.1

Add screenshots

### v4.3.0

Add custom unit in Weather Tag from Tag Manager

### v4.2.1

Add screenshots and update documentation

### v4.2.0

Add Matomo Tag Manager Custom Tag 

### v4.1.0

Support Matomo Tag Manager

Add FR translation

Change `_paq.push(['WeatherReports.setWeather'])` parameters order

### v4.0.13

Add fetch to IPApi to get rid of PHP global variable

### v4.0.12

Change request to PING=1

### v4.0.11

Rename screenshots

### v4.0.10

Add screenshots

### v4.0.9

Fix report Archiver.php class

### v4.0.8

Test #3 with methods in Archiver.php

### v4.0.7

Test #2 with abstract Class in Archiver.php

### v4.0.6

Test #2 with abstract method in Archiver.php

### v4.0.5

Remove abstract method from Archiver.php

### v4.0.4

Change blade view for VisitorDetails

### v4.0.3

Add abstract method to Archiver.php

### v4.0.2

Update IP Address

### v4.0.1

Update table in docs/index.md (Markdown tables are not supported by Matomo)

### v4.0.0

Plugin starting code base
