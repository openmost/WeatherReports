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

    function push(weather) {
      window._paq = window._paq || [];
      // metric values, Matomo converts them to the units of the website settings
      window._paq.push(['WeatherReports.setWeather',
        weather.cloud,
        weather.condition && weather.condition.text,
        weather.feelslike_c,
        weather.humidity,
        weather.precip_mm,
        weather.pressure_mb,
        weather.temp_c,
        weather.uv,
        weather.vis_km,
        weather.wind_dir,
        weather.wind_kph,
        'metric'
      ]);
    }

    function buildUrl(apiKey, matomoUrl, lang) {
      if (apiKey) {
        // WeatherAPI auto-detects the calling client IP, no third-party IP lookup needed.
        return 'https://api.weatherapi.com/v1/current.json'
          + '?key=' + encodeURIComponent(apiKey)
          + '&q=auto:ip'
          + '&aqi=no'
          + '&lang=' + encodeURIComponent(lang);
      }

      if (!matomoUrl) {
        return null;
      }

      // Matomo calls WeatherAPI with the key of the plugin settings
      if (matomoUrl.charAt(matomoUrl.length - 1) !== '/') {
        matomoUrl += '/';
      }
      return matomoUrl + 'index.php?module=WeatherReports&action=getWeather&lang=' + encodeURIComponent(lang);
    }

    this.fire = function () {
      var url = buildUrl(
        String(parameters.get('apiKey') || ''),
        String(parameters.get('matomoUrl') || ''),
        String(parameters.get('lang') || 'en')
      );
      if (!url) {
        return;
      }

      // Weather is sent on every page so a new visit in the same browser session gets it too
      var cached = readCache();
      if (cached) {
        push(cached);
        return;
      }

      fetch(url, {credentials: 'omit'})
        .then(function (response) {
          if (!response.ok) {
            throw new Error('Weather HTTP ' + response.status);
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
