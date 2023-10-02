(function () {
  return function (parameters, TagManager) {
    this.fire = function () {

      var apiKey = parameters.get("apiKey");

      if (apiKey.length) {
        async function fetchWeatherData() {

          if (!sessionStorage.getItem("matomoWeather")) {
            const ipapiResponse = await fetch('https://ipapi.co/json/');
            const ipapiData = await ipapiResponse.json();

            if (ipapiData.ip) {
              const response = await fetch(`https://api.weatherapi.com/v1/current.json?key=${apiKey}&q=${ipapiData.ip}&aqi=no&lang=en`);
              const data = await response.json();
              const weather = data.current;

              _paq.push(["WeatherReports.setWeather",
                weather.cloud,
                weather.condition.text,
                weather.feelslike_c,
                weather.humidity,
                weather.precip_mm,
                weather.pressure_mb,
                weather.temp_c,
                weather.uv,
                weather.vis_km,
                weather.wind_dir,
                weather.wind_kph
              ]);

              sessionStorage.setItem("matomoWeather", JSON.stringify(weather));
            }
          }
        }

        fetchWeatherData();
      }
    };
  };
})
();
