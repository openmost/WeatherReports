(function () {
  return function (parameters, TagManager) {
    this.fire = function () {
      var apiKey = parameters.get('apiKey');
      if (!apiKey) {
        return;
      }

      if (sessionStorage.getItem('matomoWeather')) {
        return;
      }

      var lang = parameters.get('lang') || 'en';
      var temperatureUnit = parameters.get('temperatureUnit') || 'c';
      var precipitationUnit = parameters.get('precipitationUnit') || 'mm';
      var pressureUnit = parameters.get('pressureUnit') || 'mb';
      var visibilityUnit = parameters.get('visibilityUnit') || 'km';
      var windSpeedUnit = parameters.get('windSpeedUnit') || 'kph';

      // WeatherAPI auto-detects the calling client IP — no third-party IP lookup needed.
      var url = 'https://api.weatherapi.com/v1/current.json'
        + '?key=' + encodeURIComponent(apiKey)
        + '&q=auto:ip'
        + '&aqi=no'
        + '&lang=' + encodeURIComponent(lang);

      fetch(url)
        .then(function (response) {
          if (!response.ok) {
            throw new Error('WeatherAPI HTTP ' + response.status);
          }
          return response.json();
        })
        .then(function (data) {
          if (!data || !data.current) {
            return;
          }
          var weather = data.current;

          _paq.push(['WeatherReports.setWeather',
            weather.cloud,
            weather.condition && weather.condition.text,
            weather['feelslike_' + temperatureUnit],
            weather.humidity,
            weather['precip_' + precipitationUnit],
            weather['pressure_' + pressureUnit],
            weather['temp_' + temperatureUnit],
            weather.uv,
            weather['vis_' + visibilityUnit],
            weather.wind_dir,
            weather['wind_' + windSpeedUnit]
          ]);

          try {
            sessionStorage.setItem('matomoWeather', JSON.stringify(weather));
          } catch (e) { /* storage may be full or disabled */ }
        })
        .catch(function () { /* swallow network errors silently */ });
    };
  };
})();
