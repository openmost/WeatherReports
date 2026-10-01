# Weather Reports for Matomo

Track the weather during your visitors' sessions and see how temperature, rain or wind influence traffic, behaviour and conversions.

## Features

- **11 weather dimensions**: Condition, Cloud, Temperature, Felt temperature, Humidity, Pressure, Precipitation, UV, Visibility, Wind direction and Wind speed, each with its own report, segment and API method, in the **Visitors > Weather** section.
- **Goals and ecommerce on every dimension**: conversion rate, conversions and revenue broken down by weather. Weather is also stored with each conversion.
- **Readable reports**: bar charts with numeric ordering for scale reports, top 15 rows plus "Others" for Condition and Wind direction.
- **Units per website**: choose °C/°F, mm/in, mb/inHg, km/mi and km/h/mph on the **Administration > Websites > Weather** page. Values are not converted: the Weather tag must send them in these units.
- **Matomo Tag Manager template**: a ready-to-use **Weather** tag (Openmost category) that calls WeatherAPI with your key, in the language and units of your choice, once per browser session.
- **Visitor log card**: the weather of each visit with its units, following Matomo's light and dark themes.
- **Validated tracking**: out-of-range or malformed values are dropped when tracking.
- **Classic tracking code supported**: push the values yourself with `_paq.push(['WeatherReports.setWeather', ...])` if you do not use Tag Manager.
- Available in 13 languages.

## Requirements

- Matomo 5.10.0 or later, below 6.0.0
- A [WeatherAPI](https://www.weatherapi.com/) API key (the free plan includes 1 million calls per month)
- Matomo Tag Manager (optional, recommended)

## Installation / Configuration

1. Install the plugin from the Matomo Marketplace, or upload it to `plugins/WeatherReports`, then activate it. Matomo adds the `weather_*` columns to the visit and conversion tables.
2. Set the units of each website in **Administration > Websites > Weather** (website admin access).
3. In Matomo Tag Manager, add the **Weather** tag, enter your WeatherAPI key, choose the language and the same units as the website, and publish the container. Without Tag Manager, use the classic code snippet from the documentation.

After a plugin update, republish your Tag Manager container to serve the latest Weather tag.

Before you deactivate or uninstall the plugin, delete the Weather tags of your containers, or keep the TagManagerExtended plugin active. Matomo Tag Manager cannot display a tag whose template comes from a disabled plugin, the tags list of the container then stays on "Loading data".

## Privacy and data

- The Weather tag calls WeatherAPI from the visitor's browser, so WeatherAPI receives the visitor IP to find the local weather.
- No other third-party service is contacted. Mention WeatherAPI in your privacy policy.
- The WeatherAPI key is part of the Tag Manager tag, so it is visible in your website code.

## Need help with Matomo?

Openmost is an official Matomo Implementation Partner. We connect Matomo to external data sources and the rest of your stack with [Matomo integrations](https://openmost.com/matomo/services/integration?utm_source=matomo_marketplace&utm_medium=referral&utm_campaign=services&utm_content=weatherreports) built on official APIs, with documented and privacy-checked data flows.

## Support

- Email: ronan@openmost.com
- Homepage: https://openmost.com/matomo/extensions/weather-reports
- Issues: https://github.com/openmost/WeatherReports/issues

## Screenshots

Screenshots of the reports, the visitor log card, the Weather tag and the units page are available in the `screenshots/` folder and on the Marketplace.
