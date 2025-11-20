<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WeatherReports;

class Archiver extends \Piwik\Plugin\Archiver
{
    public const CONDITION_RECORD_NAME = 'WeatherReports_Condition';
    public const CLOUD_RECORD_NAME = 'WeatherReports_Cloud';
    public const FELT_TEMPERATURE_RECORD_NAME = 'WeatherReports_FeltTemperature';
    public const HUMIDITY_RECORD_NAME = 'WeatherReports_Humidity';
    public const PRECIPITATION_RECORD_NAME = 'WeatherReports_Precipitation';
    public const PRESSURE_RECORD_NAME = 'WeatherReports_Pressure';
    public const TEMPERATURE_RECORD_NAME = 'WeatherReports_Temperature';
    public const UV_RECORD_NAME = 'WeatherReports_Uv';
    public const VISIBILITY_RECORD_NAME = 'WeatherReports_Visibility';
    public const WIND_SPEED_RECORD_NAME = 'WeatherReports_WindSpeed';
    public const WIND_DIRECTION_RECORD_NAME = 'WeatherReports_WindDirection';

    public const CONDITION_DIMENSION = "log_visit.weather_condition";
    public const CLOUD_DIMENSION = "log_visit.weather_cloud";
    public const FELT_TEMPERATURE_DIMENSION = "log_visit.weather_felt_temperature";
    public const HUMIDITY_DIMENSION = "log_visit.weather_humidity";
    public const PRECIPITATION_DIMENSION = "log_visit.weather_precipitation";
    public const PRESSURE_DIMENSION = "log_visit.weather_pressure";
    public const TEMPERATURE_DIMENSION = "log_visit.weather_temperature";
    public const UV_DIMENSION = "log_visit.weather_uv";
    public const VISIBILITY_DIMENSION = "log_visit.weather_visibility";
    public const WIND_SPEED_DIMENSION = "log_visit.weather_wind_speed";
    public const WIND_DIRECTION_DIMENSION = "log_visit.weather_wind_direction";
}
