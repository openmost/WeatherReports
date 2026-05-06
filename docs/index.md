## Documentation

### 1- Install the plugin from the marketplace or via GitHub

Install this plugin from the Marketplace as super user or download the plugin and install it on your server from FTP in
the `/plugins` folder.

Upon activation, this plugin will automatically update the structure of your database's `log_visit` table by adding 11
new columns prefixed `weather_` for the new dimensions.

### 2 - Fetch data on your website

This plugin retrieves weather data from [WeatherAPI](https://www.weatherapi.com) and sends it to your Matomo
instance as Weather reports. Because data only needs to be retrieved once per visit, the snippet uses a `sessionStorage`
flag to avoid duplicate requests. The flag is purged when the browser is closed.

You'll need to generate your own API key (Free plan is up to 1 Million calls per month).
Replace `XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX` with your API key and adjust the language if necessary.

WeatherAPI's `q=auto:ip` lets the API auto-detect the visitor's IP from the request, so no third-party IP-lookup
service is required.

### 2 - 1 With Matomo Tag Manager (Recommended)

Use the **Weather** custom Tag in Matomo Tag Manager (in the *Openmost* section). Set your API key, choose units
and language, and publish a new container version.

> **Upgrading from a previous version of the plugin?** The published container is a static JS file
> built at publish time, so any improvements to the bundled tag template (the `Weather` tag) only
> take effect once you **republish** your container. If you skip this step the older tag JS keeps
> serving — for example, the `ipapi.co` lookup that was removed in v5.2.0 will keep firing until
> you republish.

### 2 - 2 OR with Matomo classic code (only if you don't use Matomo Tag Manager)

Implement the `_paq.push(['WeatherReports.setWeather'])` method on your website using the following snippet.

> **Upgrading from a previous version?** This snippet has been simplified — `ipapi.co` is no longer
> needed (WeatherAPI auto-detects the IP via `q=auto:ip`). Replace the old snippet on your site
> with the one below to drop the third-party lookup.

```html
<!-- Openmost WeatherReports code for Matomo -->
<script>
    (async function () {
        const apiKey = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        const lang = "en"; // see https://www.weatherapi.com/docs/

        if (!apiKey || sessionStorage.getItem("matomoWeather")) return;

        try {
            const response = await fetch(
                `https://api.weatherapi.com/v1/current.json?key=${encodeURIComponent(apiKey)}&q=auto:ip&aqi=no&lang=${encodeURIComponent(lang)}`
            );
            if (!response.ok) return;
            const data = await response.json();
            const weather = data && data.current;
            if (!weather) return;

            _paq.push(["WeatherReports.setWeather",
                weather.cloud,                       // Cloud
                weather.condition && weather.condition.text, // Condition
                weather.feelslike_c,                 // Felt temperature in °C (use feelslike_f for °F)
                weather.humidity,                    // Humidity
                weather.precip_mm,                   // Precipitation in mm (use precip_in for inches)
                weather.pressure_mb,                 // Pressure in mb (use pressure_in for inches)
                weather.temp_c,                      // Temperature in °C (use temp_f for °F)
                weather.uv,                          // UV
                weather.vis_km,                      // Visibility in km (use vis_miles for miles)
                weather.wind_dir,                    // Wind direction (compass)
                weather.wind_kph                     // Wind speed in km/h (use wind_mph for mph)
            ]);

            try { sessionStorage.setItem("matomoWeather", JSON.stringify(weather)); } catch (_) {}
        } catch (_) { /* swallow network errors */ }
    })();
</script>
<!-- End Openmost WeatherReports code for Matomo -->
```

### Optional: resolve the visitor IP via Matomo

If you sit behind a CDN/proxy and `q=auto:ip` can't see the real client IP, the plugin exposes a small public
endpoint that returns the IP as Matomo resolves it (honouring `proxy_client_headers` from your `config.ini.php`):

```
GET {matomoUrl}/index.php?module=WeatherReports&action=getUserIp
→ {"ip": "203.0.113.5"}
```

You can then call WeatherAPI with `q=<that_ip>` instead of `auto:ip`.

### 3 - Enjoy new reports and features

You will find the different reports in the **Weather** section of the Matomo *Visitors* menu.
These reports support Matomo's automatic archiving CRON for better performance (recommended).

**Dimensions, segments and API methods:**

```
| Dimension name   | Type   | Segment name           | API method                        | Tracking HTTP API parameter |
|------------------|--------|------------------------|-----------------------------------|-----------------------------|
| Condition        | string | weatherCondition       | WeatherReports.getCondition       | weather_condition           |
| Cloud            | int    | weatherCloud           | WeatherReports.getCloud           | weather_cloud               |
| Temperature      | float  | weatherTemperature     | WeatherReports.getTemperature     | weather_temperature         |
| Felt temperature | float  | weatherFeltTemperature | WeatherReports.getFeltTemperature | weather_felt_temperature    |
| Pressure         | float  | weatherPressure        | WeatherReports.getPressure        | weather_pressure            |
| Precipitation    | float  | weatherPrecipitation   | WeatherReports.getPrecipitation   | weather_precipitation       |
| Humidity         | int    | weatherHumidity        | WeatherReports.getHumidity        | weather_humidity            |
| Uv               | float  | weatherUv              | WeatherReports.getUv              | weather_uv                  |
| Visibility       | float  | weatherVisibility      | WeatherReports.getVisibility      | weather_visibility          |
| Wind direction   | string | weatherWindDirection   | WeatherReports.getWindDirection   | weather_wind_direction      |
| Wind speed       | float  | weatherWindSpeed       | WeatherReports.getWindSpeed       | weather_wind_speed          |
```

### Privacy note

This plugin asks WeatherAPI for the current weather at the visitor's IP. The IP is therefore shared with WeatherAPI
(or whichever weather provider you configure). Make sure your privacy policy mentions this. The plugin no longer
calls any third-party IP-geolocation service.

### Example WeatherAPI response

```json
{
  "last_updated": "2026-05-06 20:00",
  "temp_c": 13.2, "temp_f": 55.8,
  "is_day": 1,
  "condition": { "text": "Overcast", "code": 1009 },
  "wind_mph": 6, "wind_kph": 9.7, "wind_degree": 346, "wind_dir": "NNW",
  "pressure_mb": 1011, "pressure_in": 29.85,
  "precip_mm": 0.08, "precip_in": 0,
  "humidity": 67, "cloud": 100,
  "feelslike_c": 12.5, "feelslike_f": 54.5,
  "vis_km": 10, "vis_miles": 6,
  "uv": 0
}
```
