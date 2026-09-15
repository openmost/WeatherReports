(function () {
  return function (parameters, TagManager) {
    var STORAGE_KEY = 'matomoWeather';
    // WeatherAPI refreshes current conditions every 15 minutes, one call per hour per browser session is plenty
    var CACHE_TTL = 60 * 60 * 1000;

    function readCache() {
      try {
        var cached = JSON.parse(sessionStorage.getItem(STORAGE_KEY) || 'null');
        if (cached && cached.current && cached.time && (new Date().getTime() - cached.time) < CACHE_TTL) {
          return cached.current;
        }
      } catch (e) { /* storage disabled or invalid content */ }
      return null;
    }

    function writeCache(weather) {
      try {
        sessionStorage.setItem(STORAGE_KEY, JSON.stringify({time: new Date().getTime(), current: weather}));
      } catch (e) { /* storage may be full or disabled */ }
    }

    this.fire = function () {
      var apiKey = parameters.get('apiKey');
      if (!apiKey) {
        return;
      }

      var lang = String(parameters.get('lang') || 'en');
      var temperatureUnit = String(parameters.get('temperatureUnit') || 'c');
      var precipitationUnit = String(parameters.get('precipitationUnit') || 'mm');
      var pressureUnit = String(parameters.get('pressureUnit') || 'mb');
      var visibilityUnit = String(parameters.get('visibilityUnit') || 'km');
      var windSpeedUnit = String(parameters.get('windSpeedUnit') || 'kph');

      function push(weather) {
        window._paq = window._paq || [];
        window._paq.push(['WeatherReports.setWeather',
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
      }

      // Weather is sent on every page so a new visit in the same browser session gets it too
      var cached = readCache();
      if (cached) {
        push(cached);
        return;
      }

      // WeatherAPI auto-detects the calling client IP, no third-party IP lookup needed.
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
          push(data.current);
          writeCache(data.current);
        })
        .catch(function () { /* swallow network errors silently */ });
    };
  };
})();
