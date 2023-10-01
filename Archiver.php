<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\DataArray;

/**
 * Class encapsulating logic to process Day/Period Archiving for the Actions reports
 *
 */
class Archiver extends \Piwik\Plugin\Archiver
{
    const CONDITION_RECORD_NAME = 'WeatherReports_Condition';
    const CLOUD_RECORD_NAME = 'WeatherReports_Cloud';
    const FELT_TEMPERATURE_RECORD_NAME = 'WeatherReports_FeltTemperature';
    const HUMIDITY_RECORD_NAME = 'WeatherReports_Humidity';
    const PRECIPITATION_RECORD_NAME = 'WeatherReports_Precipitation';
    const PRESSURE_RECORD_NAME = 'WeatherReports_Pressure';
    const TEMPERATURE_RECORD_NAME = 'WeatherReports_Temperature';
    const UV_RECORD_NAME = 'WeatherReports_Uv';
    const VISIBILITY_RECORD_NAME = 'WeatherReports_Visibility';
    const WIND_SPEED_RECORD_NAME = 'WeatherReports_WindSpeed';
    const WIND_DIRECTION_RECORD_NAME = 'WeatherReports_WindDirection';


    const CONDITION_DIMENSION = "log_visit.weather_condition";
    const CLOUD_DIMENSION = "log_visit.weather_cloud";
    const FELT_TEMPERATURE_DIMENSION = "log_visit.weather_felt_temperature";
    const HUMIDITY_DIMENSION = "log_visit.weather_humidity";
    const PRECIPITATION_DIMENSION = "log_visit.weather_precipitation";
    const PRESSURE_DIMENSION = "log_visit.weather_pressure";
    const TEMPERATURE_DIMENSION = "log_visit.weather_temperature";
    const UV_DIMENSION = "log_visit.weather_uv";
    const VISIBILITY_DIMENSION = "log_visit.weather_visibility";
    const WIND_SPEED_DIMENSION = "log_visit.weather_wind_speed";
    const WIND_DIRECTION_DIMENSION = "log_visit.weather_wind_direction";


    public function aggregateDayReport()
    {
        $this->aggregateByCondition();
        $this->aggregateByCloud();
        $this->aggregateByFeltTemperature();
        $this->aggregateByHumidity();
        $this->aggregateByPrecipitation();
        $this->aggregateByPressure();
        $this->aggregateByTemperature();
        $this->aggregateByUv();
        $this->aggregateByVisibility();
        $this->aggregateByWindDirection();
        $this->aggregateByWindSpeed();
    }

    public function aggregateMultipleReports()
    {
        $dataTableRecords = array(
            self::CONDITION_RECORD_NAME,
            self::CLOUD_RECORD_NAME,
            self::FELT_TEMPERATURE_RECORD_NAME,
            self::HUMIDITY_RECORD_NAME,
            self::PRECIPITATION_RECORD_NAME,
            self::PRESSURE_RECORD_NAME,
            self::TEMPERATURE_RECORD_NAME,
            self::UV_RECORD_NAME,
            self::VISIBILITY_RECORD_NAME,
            self::WIND_DIRECTION_RECORD_NAME,
            self::WIND_SPEED_RECORD_NAME,
        );
        $columnsAggregationOperation = null;
        $this->getProcessor()->aggregateDataTableRecords(
            $dataTableRecords,
            $maximumRowsInDataTableLevelZero = null,
            $maximumRowsInSubDataTable = null,
            $columnToSortByBeforeTruncation = null,
            $columnsAggregationOperation,
            $columnsToRenameAfterAggregation = null,
            $countRowsRecursive = array());
    }


    public function aggregateByCondition()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::CONDITION_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::CONDITION_RECORD_NAME, $report);
    }

    public function aggregateByCloud()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::CLOUD_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::CLOUD_RECORD_NAME, $report);
    }


    public function aggregateByFeltTemperature()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::FELT_TEMPERATURE_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::FELT_TEMPERATURE_RECORD_NAME, $report);
    }

    public function aggregateByHumidity()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::HUMIDITY_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::HUMIDITY_RECORD_NAME, $report);
    }

    public function aggregateByPrecipitation()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::PRECIPITATION_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::PRECIPITATION_RECORD_NAME, $report);
    }

    public function aggregateByPressure()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::PRESSURE_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::PRESSURE_RECORD_NAME, $report);
    }

    public function aggregateByTemperature()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::TEMPERATURE_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::TEMPERATURE_RECORD_NAME, $report);
    }

    public function aggregateByUv()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::UV_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::UV_RECORD_NAME, $report);
    }

    public function aggregateByVisibility()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::VISIBILITY_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::VISIBILITY_RECORD_NAME, $report);
    }

    public function aggregateByWindDirection()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::WIND_DIRECTION_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::WIND_DIRECTION_RECORD_NAME, $report);
    }

    public function aggregateByWindSpeed()
    {
        $array = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::WIND_SPEED_DIMENSION);
        $report = $array->asDataTable()->getSerialized();
        $this->getProcessor()->insertBlobRecord(self::WIND_SPEED_RECORD_NAME, $report);
    }
}
