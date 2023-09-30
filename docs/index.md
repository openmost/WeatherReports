## Documentation

### 1- Install the plugin from the marketplace or via GitHub

Install this plugin from the Marketplace as super user or download the plugin and install it on your server from FTP in
the `/plugins` folder.

Upon activation, this plugin will automatically update the structure of your database's `log.visit` table by adding 11
new columns prefixed `weather_` for the new dimensions.

### 2 - Fetch data on your website

This code allows you to retrieve weather data from [WeatherAPI](https://www.weatherapi.com) and send it to your Matomo
instance in Weather reports. Because you only need to retrieve data once per visit, this code has a sessionStorage
variable to avoid multiple requests. The sessionStorage is purged each time the browser is closed.

You'll need to generate your own API key (Free plan is up to 1 Million calls per month).
Replace `XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX` with your API Key and adjust your language if necessary.

Implement the `_paq.push(['WeatherReports.setWeather'])` method on your website using the following snippet:

```html

<script>
    document.addEventListener('DOMContentLoaded', async function () {

        const apiKey = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
        const lang = 'en'; // Available lang code here https://www.weatherapi.com/docs/

        if (!sessionStorage.getItem("matomoWeather")) {
            const response = await fetch(`https://api.weatherapi.com/v1/current.json?key=${apiKey}&q=90.49.112.61&aqi=no&lang=${lang}`)
            const data = await response.json();
            const weather = data.current;

            _paq.push(['WeatherReports.setWeather',
                weather.condition.text,   // Condition
                weather.cloud,            // Cloud
                weather.temp_c,           // Temperature in Celsius (for Fahrenheit, use: weather.temp_f)
                weather.feelslike_c,      // Temperature in Celsius (for Fahrenheit, use: weather.feelslike_f)
                weather.precip_mm,        // Precipitation in millimeters (for inches, use: weather.precip_in)
                weather.humidity,         // Humidity
                weather.pressure_mb,      // Pressure in millibars (for inches, use: weather.pressure_in)
                weather.uv,               // Uv
                weather.vis_km,           // Visibility in kilometers (for miles, use: weather.vis_miles)
                weather.wind_dir,         // WindDirection
                weather.wind_kph,         // WindSpeed in Kilometers/h (for miles/h, use: weather.wind_mph)
            ]);

            sessionStorage.setItem("matomoWeather", JSON.stringify(weather));
        }

    })
</script>
```

### 3 - Enjoy new reports and features

Now that the plugin is correctly configured, you will find the different reports in the "Weather" section of the
Matomo "Visitors" menu.

These different reports support Matomo's automatic archiving CRON for better performance (recommended)

**New dimensions with segments adn API methods:**

| Dimension name   | Type     | Segment name             | API method                          | Tracking HTTP API parameter |
|------------------|----------|--------------------------|-------------------------------------|-----------------------------|
| Condition        | `string` | `weatherCondition`       | `WeatherReports.getCondition`       | `weather_condition`         |
| Cloud            | `int`    | `weatherCloud`           | `WeatherReports.getCloud`           | `weather_cloud`             |
| Pressure         | `int`    | `weatherPressure`        | `WeatherReports.getPressure`        | `weather_pressure`          |
| Temperature      | `float`  | `weatherTemperature`     | `WeatherReports.getTemperature`     | `weather_temperature`       |
| Felt temperature | `float`  | `weatherFeltTemperature` | `WeatherReports.getFeltTemperature` | `weather_felt_temperature`  |
| Precipitation    | `float`  | `weatherPrecipitation`   | `WeatherReports.getPrecipitation`   | `weather_precipitation`     |
| Humidity         | `int`    | `weatherHumidity`        | `WeatherReports.getHumidity`        | `weather_humidity`          |
| Uv               | `float`  | `weatherUv`              | `WeatherReports.getUv`              | `weather_uv`                |
| Visibility       | `int`    | `weatherVisibility`      | `WeatherReports.getVisibility`      | `weather_visibility`        |
| Wind direction   | `string` | `weatherWindDirection`   | `WeatherReports.getWindDirection`   | `weather_wind_direction`    |
| Wind speed       | `float`  | `weatherWindSpeed`       | `WeatherReports.getWindSpeed`       | `weather_wind_speed`        |

Enjoy !

### This is an expample of WeatherAPI response:

You do not have to copy or understand this code, it is simply information about the type of metric returned by
WeatherAPI.

```json
 {
  "last_updated_epoch": 1695921300,
  "last_updated": "2023-09-28 19:15",
  "temp_c": 23,
  "temp_f": 73.4,
  "is_day": 1,
  "condition": {
    "text": "Partly cloudy",
    "icon": "//cdn.weatherapi.com/weather/64x64/day/116.png",
    "code": 1003
  },
  "wind_mph": 10.5,
  "wind_kph": 16.9,
  "wind_degree": 220,
  "wind_dir": "SW",
  "pressure_mb": 1015,
  "pressure_in": 29.97,
  "precip_mm": 0,
  "precip_in": 0,
  "humidity": 69,
  "cloud": 75,
  "feelslike_c": 25.1,
  "feelslike_f": 77.2,
  "vis_km": 10,
  "vis_miles": 6,
  "uv": 5,
  "gust_mph": 18.7,
  "gust_kph": 30.2
}
```