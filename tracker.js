(function () {

  function init() {

    Matomo.on('TrackerSetup', function (tracker) {
      tracker.WeatherReports = {
        setWeather: function (
          clouds,
          temperature,
          feltTemperature,
          dewPoint,
          humidity,
          pressure,
          uv,
          visibility,
          windDirection,
          windSpeed
        ) {

          var request = "";

          if (clouds) {
            request += "&weather_clouds=" + clouds;
          }

          if (temperature) {
            request += "&weather_temperature=" + temperature;
          }

          if (feltTemperature) {
            request += "&weather_felt_temperature=" + feltTemperature;
          }

          if (dewPoint) {
            request += "&weather_dew_point=" + dewPoint;
          }

          if (humidity) {
            request += "&weather_humidity=" + humidity;
          }

          if (pressure) {
            request += "&weather_pressure=" + pressure;
          }

          if (uv) {
            request += "&weather_uv=" + uv;
          }

          if (visibility) {
            request += "&weather_visibility=" + visibility;
          }

          if (windDirection) {
            request += "&weather_wind_direction=" + windDirection;
          }

          if (windSpeed) {
            request += "&weather_wind_speed=" + windSpeed;
          }

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
