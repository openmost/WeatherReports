<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

namespace Piwik\Plugins\WeatherReports;

use Piwik\Archive;
use Piwik\DataTable;
use Piwik\Piwik;

/**
 * @method static \Piwik\Plugins\WeatherReports\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    public function getCondition($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Condition', $idSite, $period, $date, $segment);
    }

    public function getCloud($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Cloud', $idSite, $period, $date, $segment);
    }

    public function getTemperature($idSite, $period, $date, $segment = false)
    {
        return $this->getRoundedScaleDataTable('WeatherReports_Temperature', $idSite, $period, $date, $segment);
    }

    public function getFeltTemperature($idSite, $period, $date, $segment = false)
    {
        return $this->getRoundedScaleDataTable('WeatherReports_FeltTemperature', $idSite, $period, $date, $segment);
    }

    public function getPressure($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Pressure', $idSite, $period, $date, $segment);
    }

    public function getHumidity($idSite, $period, $date, $segment = false)
    {
        return $this->getDataTable('WeatherReports_Humidity', $idSite, $period, $date, $segment);
    }

    public function getPrecipitation($idSite, $period, $date, $segment = false)
    {
        return $this->getRoundedScaleDataTable('WeatherReports_Precipitation', $idSite, $period, $date, $segment);
    }

    public function getUv($idSite, $period, $date, $segment = false)
    {
        return $this->getRoundedScaleDataTable('WeatherReports_Uv', $idSite, $period, $date, $segment);
    }

    public function getVisibility($idSite, $period, $date, $segment = false)
    {
        return $this->getRoundedScaleDataTable('WeatherReports_Visibility', $idSite, $period, $date, $segment);
    }

    public function getWindSpeed($idSite, $period, $date, $segment = false)
    {
        return $this->getRoundedScaleDataTable('WeatherReports_WindSpeed', $idSite, $period, $date, $segment);
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

    /**
     * Float-valued scales (temperature, UV, ...) are bucketed to integers
     * for the chart. Categorical and integer-valued scales skip this filter.
     */
    private function getRoundedScaleDataTable($name, $idSite, $period, $date, $segment)
    {
        $table = $this->getDataTable($name, $idSite, $period, $date, $segment);
        $table->filter('GroupBy', ['label', static function ($label) {
            return is_numeric($label) ? (string) (int) round((float) $label) : $label;
        }]);
        return $table;
    }
}
