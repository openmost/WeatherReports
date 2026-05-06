## FAQ

### How do I install this plugin?

This plugin is available in the official Matomo Marketplace. Install it the same way as any other
plugin:

- Go to the administration panel.
- Open *Marketplace → Plugins*.
- Search for **WeatherReports**, then install and activate.
- Follow the [setup documentation](index.md) to wire up the data-collection snippet.

### Can I use a weather API other than WeatherAPI?

Yes — the plugin only cares about the values pushed via `_paq.push(['WeatherReports.setWeather', …])`.
Any source that fits that contract works. We recommend [WeatherAPI](https://www.weatherapi.com/)
because it has a free 1M-call tier and we test against its payload, but you're free to swap it.

### Do the reports support goals and conversions?

**Yes — every weather report supports goal metrics and ecommerce conversions.** When you select a
goal in the report's metric switcher, you get conversion rate, conversions and revenue broken down
by the weather dimension. Weather is also persisted on `log_conversion`, so historical conversions
remain pinned to the weather they were tracked under.

### Do I need to republish my Matomo Tag Manager container after a plugin update?

**Yes**, if the bundled Weather tag template changes. Matomo Tag Manager bakes the tag template
into the published container JS file at publish time, so the older JS keeps serving until you
publish a new version. v5.2.0, for example, removed the `ipapi.co` IP lookup — that change only
takes effect after a republish.

### Is the plugin active for all Matomo users on my instance?

Yes — once you activate it, every user with access to the visitor reports can see Weather reports
and segments.

### Where are the per-site unit settings?

In *Site → Manage → Measurable settings*. Set Temperature (°C/°F), Precipitation (mm/in), Pressure
(mb/inHg), Visibility (km/mi) and Wind speed (km/h/mph) per site. The visitor log uses these units;
report values are stored in whatever unit you tracked them with.

### How do I run the test suite?

```bash
./vendor/bin/phpunit -c plugins/WeatherReports/phpunit.xml --testsuite "WeatherReports Unit"
```

### How can I contribute to this plugin?

Open an issue or pull request on
[github.com/openmost/WeatherReports](https://github.com/openmost/WeatherReports). Any contribution is welcome —
bug reports, translations, doc improvements, or features.

### How long will this plugin be maintained?

As long as possible. We use it on our own Matomo instances, so issues and fixes are typically
addressed quickly.
