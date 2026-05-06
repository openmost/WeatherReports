# Matomo WeatherReports Plugin

## Description

Track weather conditions during your visitors' sessions and correlate weather with behaviour.
The plugin adds 11 new visit dimensions (temperature, humidity, pressure, wind, …) populated from
[WeatherAPI](https://www.weatherapi.com); each one ships with its own report, segment, API method
and goal/conversion metrics.

## Highlights

- **11 weather dimensions** — Condition, Cloud, Temperature, Felt temperature, Humidity, Pressure,
  Precipitation, UV, Visibility, Wind direction, Wind speed.
- **Goals & conversions on every dimension** — break down conversion rate, conversions and revenue by
  any weather metric. Weather is also persisted to `log_conversion`, so historical conversions stay
  pinned to the weather they were tracked under.
- **Segments** for every dimension (`weatherTemperature`, `weatherCondition`, …).
- **Bar-chart visualizations** for scale-based reports with logical numeric ordering on the x-axis
  (1, 2, 10, 20 — not 1, 10, 2, 20).
- **Top 15 + Others** on categorical reports by default (Condition, Wind direction).
- **Per-site unit preferences** in *Site → Settings → Measurable settings*: °C/°F, mm/in, mb/inHg,
  km/mi, km/h/mph. Units are now applied in the visitor log too.
- **Visitor log card** that adapts to Matomo's light/dark themes via `--theme-color-*` variables.
- **Matomo Tag Manager template** included (Openmost category).
- **No third-party IP-geolocation dependency** — uses WeatherAPI's own `q=auto:ip`. Optional
  self-hosted `WeatherReports&action=getUserIp` Controller endpoint for CDN/proxy setups.
- **Tracking input is validated**: out-of-range or malformed values are dropped at ingest
  (Humidity 0-100, UV 0-20, Wind direction restricted to the 16-point compass, …).
- **53 PHPUnit tests** cover the column validation logic.

## Installation

Install via the Matomo Marketplace, or upload to `plugins/WeatherReports` and activate.

On activation Matomo will offer a database migration that adds the `weather_*` columns to
`log_visit` and `log_conversion`. See [docs/index.md](docs/index.md) for the full setup walk-through
and [docs/faq.md](docs/faq.md) for FAQ.

## Tests

```bash
./vendor/bin/phpunit -c plugins/WeatherReports/phpunit.xml --testsuite "WeatherReports Unit"
```

## Privacy

The plugin asks WeatherAPI for the current weather at each visitor's IP, so the visitor IP is
shared with that service. Mention this in your privacy policy. The plugin does not contact any
other third-party service.

## Requirements

- Matomo 5.x
- PHP 7.4+ (the plugin runs on PHP 8.x)

## Links

- Marketplace: <https://plugins.matomo.org/WeatherReports>
- Issues: <https://github.com/openmost/WeatherReports/issues>
- Homepage: <https://openmost.io/products/weather-reports/>

## License

GPL v3 or later
