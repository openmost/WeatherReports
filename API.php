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
use Piwik\Plugins\WeatherReports\Settings\SiteUnitsStorage;

/**
 * @method static \Piwik\Plugins\WeatherReports\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    /**
     * Conditions are labelled in the language of the current user, the same condition tracked in several
     * languages is merged in one row.
     */
    public function getCondition($idSite, $period, $date, $segment = false)
    {
        $dataTable = $this->getDataTable('WeatherReports_Condition', $idSite, $period, $date, $segment);

        $language = Conditions::getCurrentLanguage();
        $dataTable->filter(static function (DataTable $table) use ($language) {
            ConditionLabels::translateTable($table, $language);
        });

        return $dataTable;
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

    /**
     * Units in which the weather values of a site are stored and displayed.
     *
     * @return array<string, string> quantity => unit code
     */
    public function getSiteUnits($idSite)
    {
        Piwik::checkUserHasAdminAccess($idSite);

        return Units::getUnitCodes(SiteUnitsStorage::read((int) $idSite));
    }

    /**
     * Sets the units of a site. Values tracked with a unit system marker are converted to these units, data
     * tracked before the change is not converted.
     *
     * @param int $idSite
     * @param string $temperature c or f
     * @param string $precipitation mm or in
     * @param string $pressure mb or in
     * @param string $visibility km or miles
     * @param string $windSpeed kph or mph
     * @return bool
     */
    public function setSiteUnits($idSite, $temperature, $precipitation, $pressure, $visibility, $windSpeed)
    {
        Piwik::checkUserHasAdminAccess($idSite);

        $units = [
            Units::TEMPERATURE => (string) $temperature,
            Units::PRECIPITATION => (string) $precipitation,
            Units::PRESSURE => (string) $pressure,
            Units::VISIBILITY => (string) $visibility,
            Units::WIND => (string) $windSpeed,
        ];

        foreach ($units as $quantity => $unit) {
            if (!Units::isKnownUnit($quantity, $unit)) {
                throw new \Exception(Piwik::translate('WeatherReports_InvalidUnit', [$unit, $quantity]));
            }
        }

        SiteUnitsStorage::save((int) $idSite, $units);

        return true;
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
