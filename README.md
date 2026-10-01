# Weather Reports for Matomo

Track the weather during your visitors' sessions and see how temperature, rain or wind influence traffic, behaviour and conversions.

## Features

- **11 weather dimensions**: Condition, Cloud, Temperature, Felt temperature, Humidity, Pressure, Precipitation, UV, Visibility, Wind direction and Wind speed, each with its own report, segment and API method, in the **Visitors > Weather** section.
- **Goals and ecommerce on every dimension**: conversion rate, conversions and revenue broken down by weather. Weather is also stored with each conversion.
- **Readable reports**: bar charts with numeric ordering for scale reports, unit shown in the column title (for example `Temperature (°C)`), top 15 rows plus "Others" for Condition and Wind direction.
- **Conditions in the language of each user**: condition texts are matched against the official WeatherAPI list and displayed in the Matomo user's language. The same condition tracked in several languages is merged in one row, and conditions that WeatherAPI names with the same word in some languages (such as Mist and Fog) keep separate rows.
- **Units per website**: choose °C/°F, mm/in, mb/inHg, km/mi and km/h/mph on the **Administration > Websites > Weather** page. The Weather tag sends metric values and Matomo converts them when tracking.
- **WeatherAPI key kept on the server**: save the key in the plugin settings, the Weather tag gets the weather through a Matomo endpoint and the key never appears in your website code. Responses are cached 30 minutes per location.
- **Matomo Tag Manager template**: a ready-to-use **Weather** tag (Openmost category). The weather is cached one hour in the browser session and added to the tracking requests of every page, with no extra request in most cases.
- **Visitor log card**: condition, temperatures, measures and a wind direction arrow, following Matomo's light and dark themes.
- **Validated tracking**: out-of-range or malformed values are dropped when tracking.
- **Classic tracking code supported**: push the values yourself with `_paq.push(['WeatherReports.setWeather', ...])` if you do not use Tag Manager.
- Available in 13 languages.

## Requirements

- Matomo 6.0.0 or later, below 7.0.0
- PHP 8.1 or later
- A [WeatherAPI](https://www.weatherapi.com/) API key (the free plan includes 1 million calls per month)
- Matomo Tag Manager (optional, recommended)

## Installation / Configuration

1. Install the plugin from the Matomo Marketplace, or upload it to `plugins/WeatherReports`, then activate it. Matomo adds the `weather_*` columns to the visit and conversion tables.
2. Save your WeatherAPI key in **Administration > System > General settings > WeatherReports** (super user).
3. Set the units of each website in **Administration > Websites > Weather** (website admin access).
4. In Matomo Tag Manager, add the **Weather** tag, leave its API key empty to use the key saved in Matomo, and publish the container. Without Tag Manager, use the classic code snippet from the documentation.

After a plugin update, republish your Tag Manager container to serve the latest Weather tag.

Before you deactivate or uninstall the plugin, delete the Weather tags of your containers, or keep the TagManagerExtended plugin active. Matomo Tag Manager cannot display a tag whose template comes from a disabled plugin, the tags list of the container then stays on "Loading data".

## Privacy and data

- The weather is looked up from the visitor's location. Through the Matomo endpoint (recommended), WeatherAPI receives the coordinates found by Matomo geolocation rounded to about 10 km, or the visitor IP without its last byte.
- When the Weather tag or your own code calls WeatherAPI directly with its own key, WeatherAPI receives the visitor IP.
- No other third-party service is contacted. Mention WeatherAPI in your privacy policy.
- The WeatherAPI key is stored in the Matomo plugin settings.

## Need help with Matomo?

Openmost is an official Matomo Implementation Partner. We connect Matomo to external data sources and the rest of your stack with [Matomo integrations](https://openmost.com/matomo/services/integration?utm_source=matomo_marketplace&utm_medium=referral&utm_campaign=services&utm_content=weatherreports) built on official APIs, with documented and privacy-checked data flows.

## Support

- Email: ronan@openmost.com
- Homepage: https://openmost.com/matomo/extensions/weather-reports
- Issues: https://github.com/openmost/WeatherReports/issues

## Screenshots

Screenshots of the reports, the visitor log card, the Weather tag and the units page are available in the `screenshots/` folder and on the Marketplace.
