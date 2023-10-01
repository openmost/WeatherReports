<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\DataTable;

/**
 * Class encapsulating logic to process Day/Period Archiving for the Actions reports
 *
 */
class Archiver extends \Piwik\Plugin\Archiver
{
    const CONDITION_RECORD_NAME = 'WeatherReports_Condition';
    const CLOUD_RECORD_NAME = 'WeatherReports_Cloud';
    const PRECIPITATION_RECORD_NAME = 'WeatherReports_Precipitation';
    const FELT_TEMPERATURE_RECORD_NAME = 'WeatherReports_FeltTemperature';
    const HUMIDITY_RECORD_NAME = 'WeatherReports_Humidity';
    const PRESSURE_RECORD_NAME = 'WeatherReports_Pressure';
    const TEMPERATURE_RECORD_NAME = 'WeatherReports_Temperature';
    const UV_RECORD_NAME = 'WeatherReports_Uv';
    const VISIBILITY_RECORD_NAME = 'WeatherReports_Visibility';
    const WIND_SPEED_RECORD_NAME = 'WeatherReports_WindSpeed';
    const WIND_DIRECTION_RECORD_NAME = 'WeatherReports_WindDirection';


    const CONDITION_DIMENSION = "log_visit.weather_condition";
    const CLOUD_DIMENSION = "log_visit.weather_cloud";
    const PRECIPITATION_DIMENSION = "log_visit.weather_precipitation";
    const FELT_TEMPERATURE_DIMENSION = "log_visit.weather_felt_temperature";
    const HUMIDITY_DIMENSION = "log_visit.weather_humidity";
    const PRESSURE_DIMENSION = "log_visit.weather_pressure";
    const TEMPERATURE_DIMENSION = "log_visit.weather_temperature";
    const UV_DIMENSION = "log_visit.weather_uv";
    const VISIBILITY_DIMENSION = "log_visit.weather_visibility";
    const WIND_SPEED_DIMENSION = "log_visit.weather_wind_speed";
    const WIND_DIRECTION_DIMENSION = "log_visit.weather_wind_direction";

    public abstract function aggregateDayReport();

    public abstract function aggregateMultipleReports();

}
