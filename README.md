# Matomo WeatherReports Plugin

## Description

Visualize all weather details in a powerful report

## Dataset used to create the plugin
https://www.weatherapi.com/docs/
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

## Script to add on your website

```angular2html
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            const weatherInfo = sessionStorage.getItem("matomoWeather");
            if (!weatherInfo) {
                const response = await fetch('https://api.weatherapi.com/v1/current.json?key=dfcfbb969d1c4547b6c141601232809&q=90.49.112.61&aqi=no&lang=fr')
                const data = await response.json();
                const weather = data.current;

                _paq.push(['WeatherReports.setWeather',
                    weather.condition.text,   // condition
                    weather.cloud,            // cloud
                    weather.temp_c,           // temperature
                    weather.feelslike_c,      // feltTemperature
                    weather.precip_mm,        // dewPoint
                    weather.humidity,         // humidity
                    weather.pressure_mb,      // pressure
                    weather.uv,               // uv
                    weather.vis_km,           // visibility
                    weather.wind_dir,         // windDirection
                    weather.wind_kph,         // windSpeed
                ]);

                sessionStorage.setItem("matomoWeather", JSON.stringify(weather));
            }

        })
    </script>
```