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
    const WEATHER_RECORD_NAME = 'WeatherReports_Weather';

    const WEATHER_FIELD = "weather";

    const WEATHER_DIMENSION = "log_visit.weather";

    public function aggregateMultipleReports()
    {
        $archiveProcessor = $this->getProcessor();
        $archiveProcessor->aggregateDataTableRecords('WeatherReports_Weather', 500);
    }
}
