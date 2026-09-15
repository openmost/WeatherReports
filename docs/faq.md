## FAQ

### How do I install this plugin?

This plugin is available in the official Matomo Marketplace. Install it the same way as any other
plugin:

- Go to the administration panel.
- Open *Marketplace → Plugins*.
- Search for **WeatherReports**, then install and activate.
- Follow the [setup documentation](index.md) to save your WeatherAPI key and wire up the data collection.

### Which Matomo versions are supported?

Version 6.x of the plugin requires Matomo 6, PHP 8.1+ and MySQL 8.0+ or MariaDB 10.6+. Use the 5.x
versions of the plugin on Matomo 5.

### Do I need to do anything after updating the plugin?

No. Updates of the 6.x versions need no database migration: existing data, settings, Tag Manager tags
and tracking codes keep working. Republish your Tag Manager container to benefit from the latest
Weather tag.

### Where do I save my WeatherAPI key?

In *Administration → General settings → WeatherReports* (super user). Leave the API key of the Weather
tag empty: the tag then gets the weather through Matomo and the key is never visible in your website
code. A tag with its own API key keeps calling WeatherAPI directly.

### Can I use a weather API other than WeatherAPI?

Yes. The plugin only cares about the values pushed via `_paq.push(['WeatherReports.setWeather', …])`.
Any source that fits that contract works. We recommend [WeatherAPI](https://www.weatherapi.com/)
because it has a free 1M-call tier and we test against its payload. Condition translations only
apply to WeatherAPI condition texts.

### How many WeatherAPI calls does it use?

At most one per browser session per hour: the weather is cached in `sessionStorage` and reused on the
following pages. Through the Matomo endpoint, responses are also cached 30 minutes per location, so
nearby visitors share the same call.

### Why are conditions displayed in my language and not in the language of the tag?

Condition texts are matched against the official WeatherAPI list of conditions in 40 languages and
displayed in the language of each Matomo user. The same condition tracked in several languages (for
example after changing the tag language) is merged in one row. Texts that are not WeatherAPI
conditions are displayed as tracked.

### Do the reports support goals and conversions?

**Yes, every weather report supports goal metrics and ecommerce conversions.** When you select a
goal in the report's metric switcher, you get conversion rate, conversions and revenue broken down
by the weather dimension. Weather is also persisted on `log_conversion`, so historical conversions
remain pinned to the weather they were tracked under.

### Do I need to republish my Matomo Tag Manager container after a plugin update?

To use the new version of the Weather tag, yes. Matomo Tag Manager bakes the tag template into the
published container JS file at publish time, so the previous tag keeps serving (and working) until
you publish a new version.

### Is the plugin active for all Matomo users on my instance?

Yes. Once you activate it, every user with access to the visitor reports can see Weather reports
and segments.

### Where are the per-site unit settings?

In *Websites → Manage*, when editing a website. Set Temperature (°C/°F), Precipitation (mm/in), Pressure
(mb/inHg), Visibility (km/mi) and Wind speed (km/h/mph). The Weather tag sends metric values and Matomo
converts them to these units when tracking. Reports show the unit in the column title and the visitor
log next to each value. Data tracked before a unit change is not converted.

### How do I run the test suite?

```bash
./vendor/bin/phpunit -c plugins/WeatherReports/phpunit.xml --testsuite "WeatherReports Unit"
TZ=UTC npx vitest run plugins/WeatherReports
```

### How can I contribute to this plugin?

Open an issue or pull request on
[github.com/openmost/WeatherReports](https://github.com/openmost/WeatherReports). Any contribution is welcome:
bug reports, translations, doc improvements, or features.

### How long will this plugin be maintained?

As long as possible. We use it on our own Matomo instances, so issues and fixes are typically
addressed quickly.
