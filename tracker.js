(function () {

  function init() {

    Matomo.on('TrackerSetup', function (tracker) {
      tracker.WeatherReports = {
        setWeather: function (
          clouds = false,
          temperature = false,
          feltTemperature = false,
          dewPoint = false,
          humidity = false,
          pressure = false,
          uv = false,
          visibility = false,
          windDirection = false,
          windSpeed = false,
        ) {

          let array = [];

          tracker.trackRequest('ronan=1');
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
