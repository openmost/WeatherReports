# Matomo WeatherReports Plugin

## Description

Visualize all weather details in a powerful report

## Dataset used to create the plugin
https://openweathermap.org/api/one-call-3#hist_example
```json
 {
  "lat": 52.2297,
  "lon": 21.0122,
  "timezone": "Europe/Warsaw",
  "timezone_offset": 3600,
  "data": [
    {
      "dt": 1645888976,
      "sunrise": 1645853361,
      "sunset": 1645891727,
      "temp": 279.13,
      "feels_like": 276.44,
      "pressure": 1029,
      "humidity": 64,
      "dew_point": 272.88,
      "uvi": 0.06,
      "clouds": 0,
      "visibility": 10000,
      "wind_speed": 3.6,
      "wind_deg": 340,
      "weather": [
        {
          "id": 800,
          "main": "Clear",
          "description": "clear sky",
          "icon": "01d"
        }
      ]
    }
  ]
}
```


Accepted values for clouds : https://openweathermap.org/weather-conditions


`&weather_cloud=Clear&weather_pressure=1029&weather_humidity=64&weather_temperature=24.50&weather_dew_point=6.12&weather_uv=1.02&weather_wind_speed=3.6&weather_wind_direction=340&visibility=1000&weather_felt_temperature=13.2`