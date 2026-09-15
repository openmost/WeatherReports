## Documentation

### 1- Install the plugin from the marketplace or via GitHub

Install this plugin from the Marketplace as super user or download the plugin and install it on your server from FTP in
the `/plugins` folder.

Upon activation, this plugin will automatically update the structure of your database's `log_visit` and
`log_conversion` tables by adding 11 new columns prefixed `weather_` for the new dimensions.

### 2 - Save your WeatherAPI key in Matomo (recommended)

This plugin retrieves weather data from [WeatherAPI](https://www.weatherapi.com). Generate your own API key (the free
plan is up to 1 million calls per month), then save it as super user in *Administration → General settings →
WeatherReports*.

Matomo then calls WeatherAPI itself through a small public endpoint, so the key never appears in your website code:

```
GET {matomoUrl}/index.php?module=WeatherReports&action=getWeather&lang=en
→ {"current": {"temp_c": 13.2, "condition": {"text": "Overcast", "code": 1009}, ...}}
```

Responses are cached for 30 minutes per location. WeatherAPI receives the coordinates found by Matomo geolocation
(rounded to about 10 km) when a GeoIP city database is configured, otherwise the visitor IP without its last byte.

### 3 - Configure the units of the website

In *Administration → Websites → Manage*, edit the website and choose the Temperature (°C/°F), Precipitation (mm/in),
Pressure (mb/inHg), Visibility (km/mi) and Wind speed (km/h/mph) units. Values are stored and displayed in these units:
the tracking code sends metric values and Matomo converts them when tracking. Reports show the unit in the column title
and the visitor log next to each value.

Changing the units later does not convert the data tracked before the change.

### 4 - Fetch data on your website

The weather is fetched once per hour per browser session (cached in `sessionStorage`) and added to the tracking
requests of every page, so a new visit started in the same browser session gets it too.

### 4 - 1 With Matomo Tag Manager (Recommended)

Use the **Weather** custom Tag in Matomo Tag Manager (in the *Openmost* section), fire it on every page view and
publish a new container version.

- **WeatherAPI key**: leave it empty to use the key saved in Matomo (recommended). When set, the browser calls
  WeatherAPI directly with this key, visible in the container.
- **Matomo URL**: prefilled with the URL of your Matomo, used when the tag has no API key.
- **Language**: language of the condition text sent to Matomo. Reports and the visitor log display conditions in the
  language of each Matomo user whatever the language tracked.

> **Upgrading from a previous version of the plugin?** The published container is a static JS file
> built at publish time, so improvements to the bundled tag template only take effect once you **republish** your
> container. Until then the previous tag keeps working as before.

### 4 - 2 OR with Matomo classic code (only if you don't use Matomo Tag Manager)

Add the following snippet on every page, after the Matomo tracking code. Replace `https://matomo.example.com/` with
the URL of your Matomo.

```html
<!-- Openmost WeatherReports code for Matomo -->
<script>
    (async function () {
        const matomoUrl = "https://matomo.example.com/";
        const lang = "en"; // language of the condition text, see https://www.weatherapi.com/docs/
        const cacheKey = "matomoWeather";
        const cacheTtl = 60 * 60 * 1000; // one weather lookup per hour per browser session

        const send = (weather) => {
            window._paq = window._paq || [];
            window._paq.push(["WeatherReports.setWeather",
                weather.cloud,                               // Cloud
                weather.condition && weather.condition.text, // Condition
                weather.feelslike_c,                         // Felt temperature
                weather.humidity,                            // Humidity
                weather.precip_mm,                           // Precipitation
                weather.pressure_mb,                         // Pressure
                weather.temp_c,                              // Temperature
                weather.uv,                                  // UV
                weather.vis_km,                              // Visibility
                weather.wind_dir,                            // Wind direction (compass)
                weather.wind_kph,                            // Wind speed
                "metric"                                     // Matomo converts to the units of the website
            ]);
        };

        try {
            const cached = JSON.parse(sessionStorage.getItem(cacheKey) || "null");
            if (cached && cached.current && Date.now() - cached.time < cacheTtl) {
                send(cached.current);
                return;
            }
        } catch (_) { /* storage disabled */ }

        try {
            const response = await fetch(
                `${matomoUrl}index.php?module=WeatherReports&action=getWeather&lang=${encodeURIComponent(lang)}`,
                { credentials: "omit" }
            );
            if (!response.ok) return;
            const data = await response.json();
            const weather = data && data.current;
            if (!weather) return;

            send(weather);

            try {
                sessionStorage.setItem(cacheKey, JSON.stringify({ time: Date.now(), current: weather }));
            } catch (_) { /* storage full or disabled */ }
        } catch (_) { /* swallow network errors */ }
    })();
</script>
<!-- End Openmost WeatherReports code for Matomo -->
```

To call WeatherAPI directly instead (the key is then visible in your website code), replace the fetched URL with
`https://api.weatherapi.com/v1/current.json?key=YOUR_KEY&q=auto:ip&aqi=no&lang=...`.

The last `setWeather` argument tells the unit system of the values: `"metric"` or `"imperial"` (`feelslike_f`,
`precip_in`, `pressure_in`, `temp_f`, `vis_miles`, `wind_mph`). Without it, values are stored as sent, as snippets of
previous plugin versions do.

Values are sent with the page view when the weather is known before it, otherwise with a `ping` request attached to
the current visit.

### Optional: resolve the visitor IP via Matomo

The plugin also exposes a small public endpoint that returns the visitor IP as Matomo resolves it (honouring
`proxy_client_headers` from your `config.ini.php`). It can be called from any origin:

```
GET {matomoUrl}/index.php?module=WeatherReports&action=getUserIp
→ {"ip": "203.0.113.5"}
```

### 5 - Enjoy new reports and features

You will find the different reports in the **Weather** section of the Matomo *Visitors* menu.
These reports support Matomo's automatic archiving CRON for better performance (recommended).

The visitor log and visitor profile show a weather card for each visit with weather data.

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

Tracking HTTP API: add `weather_units=metric` or `weather_units=imperial` to have the values converted to the units of
the website.

The segment of a condition compares with the text as tracked (in the language of the tag). Condition report rows
link to a segment matching every text merged in the row.

### Privacy note

Through the Matomo endpoint, WeatherAPI receives rounded coordinates or the visitor IP without its last byte. When the
tag calls WeatherAPI directly, WeatherAPI receives the visitor IP. Make sure your privacy policy mentions it. The plugin
does not call any third-party IP-geolocation service.

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
