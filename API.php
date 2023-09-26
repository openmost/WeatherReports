<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Archive;
use Piwik\DataTable;
use Piwik\Piwik;

/**
 * API for plugin WeatherReports
 *
 * @method static \Piwik\Plugins\WeatherReports\API getInstance()
 */
class API extends \Piwik\Plugin\API
{

    /**
     * Another example method that returns a data table.
     * @param int $idSite
     * @param string $period
     * @param string $date
     * @param bool|string $segment
     * @return DataTable
     */
    public function getClouds($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Clouds', $idSite, $period, $date, $segment);
    }

    public function getTemperature($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Temperature', $idSite, $period, $date, $segment);
    }

    public function getFeltTemperature($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_FeltTemperature', $idSite, $period, $date, $segment);
    }

    public function getPressure($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Pressure', $idSite, $period, $date, $segment);
    }

    public function getHumidity($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Humidity', $idSite, $period, $date, $segment);
    }

    public function getDewPoint($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_DewPoint', $idSite, $period, $date, $segment);
    }

    public function getUv($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Uv', $idSite, $period, $date, $segment);
    }

    public function getVisibility($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Visibility', $idSite, $period, $date, $segment);
    }

    public function getWindSpeed($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_WindSpeed', $idSite, $period, $date, $segment);
    }

    public function getWindDirection($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_WindDirection', $idSite, $period, $date, $segment);
    }


    protected function getDataTable($name, $idSite, $period, $date, $segment)
    {
        Piwik::checkUserHasViewAccess($idSite);
        $archive = Archive::build($idSite, $period, $date, $segment);
        $dataTable = $archive->getDataTable($name);
        $dataTable->queueFilter('ReplaceColumnNames');
        $dataTable->queueFilter('ReplaceSummaryRowLabel');
        return $dataTable;
    }
}
