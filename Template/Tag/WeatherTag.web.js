(function () {
  return function (parameters, TagManager) {
    this.fire = function () {

      var apiKey = parameters.get("apiKey");
      var lang = parameters.get("lang") ? parameters.get("lang") : "en";
      var temperatureUnit = parameters.get("temperatureUnit") ? parameters.get("temperatureUnit") : "c";
      var precipitationUnit = parameters.get("precipitationUnit") ? parameters.get("precipitationUnit") : "mm";
      var pressureUnit = parameters.get("pressureUnit") ? parameters.get("pressureUnit") : "mb";
      var visibilityUnit = parameters.get("visibilityUnit") ? parameters.get("visibilityUnit") : "km";
      var windSpeedUnit = parameters.get("windSpeedUnit") ? parameters.get("windSpeedUnit") : "kph";

      if (apiKey.length) {
        async function fetchWeatherData() {

          if (!sessionStorage.getItem("matomoWeather")) {
            const ipapiResponse = await fetch("https://ipapi.co/json/");
            const ipapiData = await ipapiResponse.json();

            if (ipapiData.ip) {
              const response = await fetch(`https://api.weatherapi.com/v1/current.json?key=${apiKey}&q=${ipapiData.ip}&aqi=no&lang=${lang}`);
              const data = await response.json();
              const weather = data.current;

              _paq.push(["WeatherReports.setWeather",
                weather.cloud,
                weather.condition.text,
                weather[`feelslike_${temperatureUnit}`],
                weather.humidity,
                weather[`precip_${precipitationUnit}`],
                weather[`pressure_${pressureUnit}`],
                weather[`temp_${temperatureUnit}`],
                weather.uv,
                weather[`vis_${visibilityUnit}`],
                weather[`wind_dir`],
                weather[`wind_${windSpeedUnit}`]
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
