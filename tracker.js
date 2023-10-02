(function () {

  function init() {

    Matomo.on('TrackerSetup', function (tracker) {
      tracker.WeatherReports = {
        setWeather: function (
          cloud,
          condition,
          feltTemperature,
          humidity,
          precipitation,
          pressure,
          temperature,
          uv,
          visibility,
          windDirection,
          windSpeed
        ) {

          var request = "ping=1";

          request += "&weather_cloud=" + cloud;
          request += "&weather_condition=" + condition;
          request += "&weather_felt_temperature=" + feltTemperature;
          request += "&weather_humidity=" + humidity;
          request += "&weather_precipitation=" + precipitation;
          request += "&weather_pressure=" + pressure;
          request += "&weather_temperature=" + temperature;
          request += "&weather_uv=" + uv;
          request += "&weather_visibility=" + visibility;
          request += "&weather_wind_direction=" + windDirection;
          request += "&weather_wind_speed=" + windSpeed;

          tracker.trackRequest(request);
        }
      };
    });

  }

  if ('object' === typeof window.Matomo) {
    init();
  } else {
    // tracker might not be loaded yet
    if ('object' !== typeof window.matomoPluginAsyncInit) {
      window.matomoPluginAsyncInit = [];
    }

    window.matomoPluginAsyncInit.push(init);
  }

})();
