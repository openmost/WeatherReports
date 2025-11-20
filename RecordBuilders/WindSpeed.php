<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */

namespace Piwik\Plugins\WeatherReports\RecordBuilders;

use Piwik\Plugins\WeatherReports\Archiver;

class WindSpeed extends Base
{
    public function __construct()
    {
        // Cast to DECIMAL for proper numeric sorting in SQL
        parent::__construct(Archiver::WIND_SPEED_RECORD_NAME, 'CAST(' . Archiver::WIND_SPEED_DIMENSION . ' AS DECIMAL(10,2))', true);
    }
}
