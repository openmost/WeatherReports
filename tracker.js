(function () {

  // Order of the setWeather() arguments
  var PARAMETERS = [
    'weather_cloud',
    'weather_condition',
    'weather_felt_temperature',
    'weather_humidity',
    'weather_precipitation',
    'weather_pressure',
    'weather_temperature',
    'weather_uv',
    'weather_visibility',
    'weather_wind_direction',
    'weather_wind_speed'
  ];

  // Query string of the last weather set on this page, appended to every following tracking request
  var weatherQuery = '';

  function buildQuery(values) {
    var parts = [], i, value;

    for (i = 0; i < PARAMETERS.length; i++) {
      value = values[i];
      if (value === undefined || value === null || value === '') {
        continue;
      }
      parts.push(PARAMETERS[i] + '=' + encodeURIComponent(String(value)));
    }

    return parts.join('&');
  }

  function appendWeather() {
    return weatherQuery ? '&' + weatherQuery : '';
  }

  function init() {

    Matomo.addPlugin('WeatherReports', {
      log: appendWeather,
      link: appendWeather,
      sitesearch: appendWeather,
      event: appendWeather,
      ecommerce: appendWeather
    });

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
          windSpeed,
          units
        ) {
          var query = buildQuery(arguments);
          if (!query) {
            return;
          }

          // 'metric' or 'imperial': Matomo converts the values to the units of the website settings.
          // Without it, values are stored as sent.
          if (units === 'metric' || units === 'imperial') {
            query += '&weather_units=' + units;
          }

          weatherQuery = query;

          // Page views tracked from now on carry the weather. When the page view of this page was already sent,
          // attach the weather to the visit with a ping (a ping never creates a visit on its own).
          if (tracker.getNumTrackedPageViews() > 0) {
            tracker.trackRequest('ping=1&' + query);
          }
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
